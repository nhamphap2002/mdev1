<?php
class Mgroup_Catalogsearch_Model_Resource_Helper_Mysql4 extends Mage_CatalogSearch_Model_Resource_Helper_Mysql4
{
    /**
     * Join information for usin full text search
     *
     * @param  Varien_Db_Select $select
     * @return Varien_Db_Select $select
     */
    public function chooseFulltext($table, $alias, $select)
    {
        $expr = '(';
        $expr .= 'MATCH ('.$alias.'.data_index) AGAINST (:query IN BOOLEAN MODE) ';
        $expr .= '+ MATCH ('.$alias.'.name_index) AGAINST (:query IN BOOLEAN MODE) ';
        $expr .= '+ 10 * IF ('.$alias.'.name_index LIKE :customquery, 1, 0)';
        $expr .= '+ IF ('.$alias.'.name_index LIKE :prefixquery, 1, 0)';
        $expr .= '+ IF ('.$alias.'.name_index LIKE :prefixquery2, 1, 0)';
        $expr .= '+ IF ('.$alias.'.name_index LIKE :surfixquery, 1, 0)';
        $expr .= '+ IF ('.$alias.'.name_index LIKE :surfixquery2, 1, 0)';
        $expr .= ')';

        $field = new Zend_Db_Expr($expr);
        $select->columns(array('relevance' => $field));
        return $field;
    }
}