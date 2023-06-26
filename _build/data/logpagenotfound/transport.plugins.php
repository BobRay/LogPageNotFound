<?php
/**
 * plugins transport file for LogPageNotFound extra
 *
 * Copyright 2023 Bob Ray <https://bobsguides.com>
 * Created on 06-20-2023
 *
 * @package logpagenotfound
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $plugins */


$plugins = array();

$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'LogPageNotFound',
  'description' => 'LogPageNotFound plugin -- logs page-not-found requests',
  'disabled' => false,
), '', true, true);
$plugins[1]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/logpagenotfound.plugin.php'));


$properties = include $sources['data'].'properties/properties.logpagenotfound.plugin.php';
$plugins[1]->setProperties($properties);
unset($properties);

return $plugins;
