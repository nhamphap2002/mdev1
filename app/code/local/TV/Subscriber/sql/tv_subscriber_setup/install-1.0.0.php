<?php
// Add Country code column to newsletter subscriber table
$this->getConnection()->addColumn($this->getTable('newsletter/subscriber'), 'sign_type', 'varchar(20) null');
$this->getConnection()->addColumn($this->getTable('newsletter/subscriber'), 'store_type', 'varchar(20) null');