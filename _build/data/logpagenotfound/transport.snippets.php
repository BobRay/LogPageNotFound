<?php
/**
 * snippets transport file for logpagenotfound extra
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
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'property_preprocess' => false,
  'name' => 'PageNotFoundLogReport',
  'description' => 'PageNotFoundLogReport snippet for LogPageNotFound.',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/pagenotfoundlogreport.snippet.php'));


$properties = include $sources['data'].'properties/properties.pagenotfoundlogreport.snippet.php';
$snippets[1]->setProperties($properties);
unset($properties);

return $snippets;
