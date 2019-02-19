<?php

class Mgroup_Catalogsearch_Model_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        return Mage_Eav_Model_Entity_Collection_Abstract::_loadEntities($printQuery, $logQuery);
    }
}
