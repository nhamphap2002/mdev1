<?php

class Mgroup_Catalogsearch_Model_Resource_Fulltext_Engine extends Mage_CatalogSearch_Model_Resource_Fulltext_Engine
{

    /**
     * Multi add entities data to fulltext search table
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entity 'product'|'cms'
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product', $nameIndexes)
    {
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => $index) {
            $data[] = array(
                'product_id'    => (int)$entityId,
                'store_id'      => $storeId,
                'data_index'    => $index,
                'name_index' => $nameIndexes[$entityId]
            );
        }

        if ($data) {
            Mage::getResourceHelper('catalogsearch')
                ->insertOnDuplicate($this->getMainTable(), $data, array('data_index'));
        }

        return $this;
    }
}