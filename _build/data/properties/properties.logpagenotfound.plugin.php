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
        'name' => 'log_max_lines',
        'desc' => 'lpnf_log_max_lines_desc',
        'type' => 'integer',
        'options' => '',
        'value' => '300',
        'lexicon' => 'logpagenotfound:properties',
    ),
 );

return $properties;