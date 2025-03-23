<?php
$xpdo_meta_map['idImageEmbedding']= array (
  'package' => 'idimage',
  'version' => '1.1',
  'table' => 'idimage_embeddings',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'hash' => '',
    'data' => NULL,
    'updatedon' => 0,
    'createdon' => 0,
    'pid' => NULL,
  ),
  'fieldMeta' => 
  array (
    'hash' => 
    array (
      'dbtype' => 'char',
      'precision' => '40',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
      'index' => 'index',
    ),
    'data' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'json',
      'null' => true,
    ),
    'updatedon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'createdon' => 
    array (
      'dbtype' => 'int',
      'precision' => '20',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 0,
    ),
    'pid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'hash' => 
    array (
      'alias' => 'hash',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'hash' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'pid' => 
    array (
      'alias' => 'pid',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'pid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Close' => 
    array (
      'class' => 'idImageClose',
      'local' => 'hash',
      'foreign' => 'hash',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
