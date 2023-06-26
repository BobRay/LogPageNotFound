<?php
/**
 * resources transport file for LogPageNotFound extra
 *
 * Copyright 2023 Bob Ray <https://bobsguides.com>
 * Created on 06-22-2023
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
/* @var xPDOObject[] $resources */


$resources = array();

$resources[1] = $modx->newObject('modResource');
$resources[1]->fromArray(array (
  'id' => 1,
  'type' => 'document',
  'contentType' => 'text/html',
  'pagetitle' => 'Page Not Found Log Report',
  'longtitle' => 'Page Not Found Log Report',
  'description' => 'Shows the formatted content of the Page Not Found Log',
  'alias' => 'page-not-found-log-report',
  'alias_visible' => true,
  'link_attributes' => '',
  'published' => false,
  'isfolder' => false,
  'introtext' => '',
  'richtext' => false,
  'template' => 'LogPageNotFoundTemplate',
  'menuindex' => 0,
  'searchable' => false,
  'cacheable' => true,
  'createdby' => 0,
  'editedby' => 1,
  'deleted' => false,
  'deletedon' => 0,
  'deletedby' => 0,
  'menutitle' => 'Page Not Found Log Report',
  'donthit' => false,
  'privateweb' => false,
  'privatemgr' => false,
  'content_dispo' => 0,
  'hidemenu' => true,
  'context_key' => 'web',
  'content_type' => 1,
  'hide_children_in_tree' => 0,
  'show_in_tree' => 1,
  'properties' => NULL,
), '', true, true);
$resources[1]->setContent(file_get_contents($sources['data'].'resources/page not found log report.content.html'));

return $resources;
