<?php
/**
 * Resource objects for the LogPageNotFound package
 * @author Bob Ray <http://bobsguides.com>
 * 10/12/2011
 *
 * @package logpagenotfound
 * @subpackage build
 */

$resources = array();

$modx->log(modX::LOG_LEVEL_INFO,'Packaging resource: File Not Found Log Report<br />');
$resources[1]= $modx->newObject('modResource');
$resources[1]->fromArray(array(
    'id' => 2,
    'class_key' => 'modResource',
    'context_key' => 'web',
    'type' => 'document',
    'contentType' => 'text/html',
    'pagetitle' => 'Page Not Found Log Report',
    'longtitle' => 'Page Not Found Log Report',
    'description' => 'Shows the formatted content of the Page Not Found Log',
    'alias' => 'page-not-found-log-report',
    'published' => '0',
    'parent' => '0',
    'isfolder' => '0',
    'richtext' => '0',
    'menuindex' => '',
    'searchable' => '0',
    'cacheable' => '1',
    'menutitle' => 'Page Not Found Log Report',
    'hidemenu' => '1',
),'',true,true);
$resources[1]->setContent(file_get_contents($sources['build'] . 'data/resources/pagenotfoundlogreport.content.html'));

return $resources;
