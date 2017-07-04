<?php

/**
 * Default properties for the LogFileNotFound plugin
 * @author Bob Ray <https://bobsguides.com>
 * 10/31/2011
 *
 * @package logpagenotfound
 * @subpackage build
 */

$properties = array(
    array(
        'name' => 'table_width',
        'desc' => 'lpnf_table_width_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '80%',
        'lexicon' => 'logpagenotfound:properties',
    ),
    array(
        'name' => 'cell_width',
        'desc' => 'lpnf_cell_width_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '30',
        'lexicon' => 'logpagenotfound:properties',
    ),
 );

return $properties;