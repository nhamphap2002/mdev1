<?php

class Mgroup_Catalogsearch_Model_Resource_Fulltext extends Mage_CatalogSearch_Model_Resource_Fulltext
{
    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int|array $productIds Product Entity Id
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _rebuildStoreIndex($storeId, $productIds = null)
    {
        $this->cleanIndex($storeId, $productIds);

        // prepare searchable attributes
        $staticFields = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }
        $dynamicFields = array(
            'int' => array_keys($this->_getSearchableAttributes('int')),
            'varchar' => array_keys($this->_getSearchableAttributes('varchar')),
            'text' => array_keys($this->_getSearchableAttributes('text')),
            'decimal' => array_keys($this->_getSearchableAttributes('decimal')),
            'datetime' => array_keys($this->_getSearchableAttributes('datetime')),
        );

        // status and visibility filter
        $visibility = $this->_getSearchableAttribute('visibility');
        $status = $this->_getSearchableAttribute('status');
        $statusVals = Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
        $allowedVisibilityValues = $this->_engine->getAllowedVisibility();

        $websiteId = Mage::app()->getStore($storeId)->getWebsite()->getId();
        $lastProductId = 0;
        while (true) {
            $products = $this->_getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId);
            if (!$products) {
                break;
            }

            $productAttributes = array();
            $productRelations = array();
            foreach ($products as $productData) {
                $lastProductId = $productData['entity_id'];
                $productAttributes[$productData['entity_id']] = $productData['entity_id'];
                $productChildren = $this->_getProductChildrenIds($productData['entity_id'],
                    $productData['type_id'], $websiteId);
                $productRelations[$productData['entity_id']] = $productChildren;
                if ($productChildren) {
                    foreach ($productChildren as $productChildId) {
                        $productAttributes[$productChildId] = $productChildId;
                    }
                }
            }

            $productIndexes = array();
            $productNameIndexes = array();
            $productAttributes = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
            foreach ($products as $productData) {
                if (!isset($productAttributes[$productData['entity_id']])) {
                    continue;
                }

                $productAttr = $productAttributes[$productData['entity_id']];
                if (!isset($productAttr[$visibility->getId()])
                    || !in_array($productAttr[$visibility->getId()], $allowedVisibilityValues)
                ) {
                    continue;
                }
                if (!isset($productAttr[$status->getId()]) || !in_array($productAttr[$status->getId()], $statusVals)) {
                    continue;
                }

                $productIndex = array(
                    $productData['entity_id'] => $productAttr
                );

                $hasChildren = false;
                if ($productChildren = $productRelations[$productData['entity_id']]) {
                    foreach ($productChildren as $productChildId) {
                        if (isset($productAttributes[$productChildId])) {
                            $productChildAttr = $productAttributes[$productChildId];
                            if (!isset($productChildAttr[$status->getId()])
                                || !in_array($productChildAttr[$status->getId()], $statusVals)
                            ) {
                                continue;
                            }

                            $hasChildren = true;
                            $productIndex[$productChildId] = $productChildAttr;
                        }
                    }
                }
                if (!is_null($productChildren) && !$hasChildren) {
                    continue;
                }

                $index = $this->_prepareProductIndex($productIndex, $productData, $storeId);

                $productIndexes[$productData['entity_id']] = $index['index'];
                $productNameIndexes[$productData['entity_id']] = $index['name'];
            }

            $this->_saveProductIndexes($storeId, $productIndexes, $productNameIndexes);
        }

        $this->resetSearchResults();

        return $this;
    }

    /**
     * Prepare Fulltext index value for product
     *
     * @param array $indexData
     * @param array $productData
     * @param int $storeId
     * @return string
     */
    protected function _prepareProductIndex($indexData, $productData, $storeId)
    {
        $index = array();

        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (isset($productData[$attributeCode])) {
                $value = $this->_getAttributeValue($attribute->getId(), $productData[$attributeCode], $storeId);
                if ($value) {
                    //For grouped products
                    if (isset($index[$attributeCode])) {
                        if (!is_array($index[$attributeCode])) {
                            $index[$attributeCode] = array($index[$attributeCode]);
                        }
                        $index[$attributeCode][] = $value;
                    } //For other types of products
                    else {
                        $index[$attributeCode] = $value;
                    }
                }
            }
        }

        foreach ($indexData as $entityId => $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                $value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
                if (!is_null($value) && $value !== false) {
                    $attributeCode = $this->_getSearchableAttribute($attributeId)->getAttributeCode();

                    if (isset($index[$attributeCode])) {
                        $index[$attributeCode][$entityId] = $value;
                    } else {
                        $index[$attributeCode] = array($entityId => $value);
                    }
                }
            }
        }

        if (!$this->_engine->allowAdvancedIndex()) {
            $product = $this->_getProductEmulator()
                ->setId($productData['entity_id'])
                ->setTypeId($productData['type_id'])
                ->setStoreId($storeId);
            $typeInstance = $this->_getProductTypeInstance($productData['type_id']);
            if ($data = $typeInstance->getSearchableData($product)) {
                $index['options'] = $data;
            }
        }

        if (isset($productData['in_stock'])) {
            $index['in_stock'] = $productData['in_stock'];
        }

        $name = isset($index['name']) ? $index['name'] : null;
        $name = is_array($name) ? implode(' ', $name) : $name;

        if ($this->_engine) {

            return array(
                'index' => $this->_engine->prepareEntityIndex($index, $this->_separator),
                'name' => $name
            );
        }

        return array(
            'index' => Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator),
            'name' => $name
        );

    }

    /**
     * Save Multiply Product indexes
     *
     * @param int $storeId
     * @param array $productIndexes
     * @param array $productNameIndexes
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    protected function _saveProductIndexes($storeId, $productIndexes, $productNameIndexes)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndexes($storeId, $productIndexes, 'product', $productNameIndexes);
        }

        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Mage_CatalogSearch_Model_Query $query
     * @return Mage_CatalogSearch_Model_Resource_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        $adapter = $this->_getWriteAdapter();
        $searchType = $object->getSearchType($query->getStoreId());

        $preparedTerms = Mage::getResourceHelper('catalogsearch')
            ->prepareTerms($queryText, $query->getMaxQueryWords());

        $bind = array();
        $like = array();
        $likeCond = '';
        if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE
            || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
        ) {
            $helper = Mage::getResourceHelper('core');
            $words = Mage::helper('core/string')->splitWords($queryText, true, $query->getMaxQueryWords());
            foreach ($words as $word) {
                $like[] = $helper->getCILike('s.data_index', $word, array('position' => 'any'));
            }

            if ($like) {
                $likeCond = '(' . join(' OR ', $like) . ')';
            }
        }

        $mainTableAlias = 's';
        $fields = array('product_id');

        $select = $adapter->select()
            ->from(array($mainTableAlias => $this->getMainTable()), $fields)
            ->joinInner(array('e' => $this->getTable('catalog/product')),
                'e.entity_id = s.product_id',
                array())
            ->where($mainTableAlias . '.store_id = ?', (int)$query->getStoreId());

        $where = "";
        if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
            || $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
        ) {
            $bind[':query'] = implode(' ', $preparedTerms[0]);
            $bind[':customquery'] = "%" . $queryText . "%";
            $bind[':prefixquery'] = "% " . $queryText . "%";
            $bind[':surfixquery'] = "%" . $queryText . " %";
            $bind[':prefixquery2'] = "% " . $queryText;
            $bind[':surfixquery2'] = $queryText . " %";
            $where = Mage::getResourceHelper('catalogsearch')
                ->chooseFulltext($this->getMainTable(), $mainTableAlias, $select);
        }
        if ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
            $select->columns(array('relevance' => new Zend_Db_Expr(0)));
            $where .= ($where ? ' OR ' : '') . $likeCond;
        } elseif ($likeCond != '' && $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE) {
            $select->columns(array('relevance' => new Zend_Db_Expr(0)));
            $where = $likeCond;
        }

        if ($where != '') {
            $select->where($where);
        }



        $this->_foundData = $adapter->fetchPairs($select, $bind);

        return $this;
    }


}