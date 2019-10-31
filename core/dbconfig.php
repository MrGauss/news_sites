<?php

if( !defined('GAUSS_CMS') ){ echo basename(__FILE__); exit; }

$db = new db
(
  '127.0.0.1',   // HOST
  '5434',   	 // HOST
  'news_sites',  // DBNAME
  'news_sites',  // DBUSER
  '$news_sites%', // DBPASS
  DOMAIN,        // SCHEMA
  CHARSET,       // CHARSET
  'WIN1251'      	 // COLLATE
);

?>