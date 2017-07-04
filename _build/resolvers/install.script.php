<?php

/**
 * LogPageNotFound resolver script - runs on install.
 *
 * Copyright 2011-2017 Bob Ray <https://bobsguides.com>
 * @author Bob Ray <https://bobsguides.com>
 * 10/12/2011
 *
 * LogPageNotFound is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * LogPageNotFound is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * LogPageNotFound; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package logpagenotfound
 */
/**
 * Description: Resolver script for LogPageNotFound package
 * @package logpagenotfound
 * @subpackage build
 */

/* Example Resolver script */

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */

/** @var $modx modX */
/** @var  $object object */
/** @var  $options array */

$modx =& $object->xpdo;

/* Remember that the files in the _build directory are not available
 * here and we don't know the IDs of any objects, so resources,
 * elements, and other objects must be retrieved by name with
 * $modx->getObject().
 */

/* Connecting plugins to the appropriate system events is done here. */

$pluginEvents = array('OnHandleRequest');
$plugins = array('LogPageNotFound');

$category = 'LogPageNotFound';

$hasPlugins = true;


 /* set to true to connect property sets to elements */
$connectPropertySets = false;

/* work starts here */
$success = true;

$logDir = MODX_CORE_PATH . 'logs/';
$logFile = $logDir . 'pagenotfound.log';


/* empty and remove directory */
if (!function_exists("rrmdir")) {
    function rrmdir($dir) {
        if (is_dir($dir)) {
             $objects = scandir($dir);
             foreach ($objects as $object) {
               if ($object != "." && $object != "..") {
                 if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
               }
             }
             reset($objects);
             rmdir($dir);
        }
    }
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Running PHP Resolver.');
switch($options[xPDOTransport::PACKAGE_ACTION]) {
    /* This code will execute during an install */
    case xPDOTransport::ACTION_INSTALL:
        /* Assign plugins to System events */

        $plugin = 'LogPageNotFound';
        $pluginObj = $modx->getObject('modPlugin',array('name'=>$plugin));
            if (! $pluginObj) {
                $modx->log(xPDO::LOG_LEVEL_INFO,'cannot get object: ' . $plugin);
            }else {
                $modx->log(xPDO::LOG_LEVEL_INFO,'Assigning Events to Plugin ' . $plugin);
                $intersect = $modx->newObject('modPluginEvent');
                $intersect->set('event','OnPageNotFound');
                $intersect->set('pluginid',$pluginObj->get('id'));
                $intersect->set('priority',5);
                $intersect->save();
            }

              
        /* Create log directory */

        if (! is_dir($logDir)) {
            if (!mkdir($logDir, 0700)) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to create directory: $logDir");
            } else {
                $modx->log(xPDO::LOG_LEVEL_INFO, "Created directory: $logDir");
                $fp = fopen($logFile, 'w');
                if ($fp) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, "Created file: $logFile");
                    fclose($fp);
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to create file: $logFile");
                }
            }
        } else {
            $modx->log(xPDO::LOG_LEVEL_INFO,  $logDir .  " Already exists");
            if (!file_exists($logFile)) {
                $fp = fopen($logFile, 'w');
                if ($fp) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, "Created file: $logFile");
                    fclose($fp);
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to create file: $logFile");
                }
                
            }
        }
    
    /* This code will execute during an upgrade */
    case xPDOTransport::ACTION_UPGRADE:

        /* put any upgrade tasks (if any) here such as removing
           obsolete files, settings, elements, resources, etc.
        */

        $doc = $modx->getObject('modResource', array('alias' => 'page-not-found-log-report'));
        if ($doc) {
            /** @var $doc modResource */
            $template = $doc->get('template');
            if (empty($template)) {
                $templateObj = $modx->getObject('modTemplate', array('templatename' => 'BaseTemplate'));
                if ($templateObj) {
                    $tId = $templateObj->get('id');
                } else {
                    $tId = $modx->getOption('default_template', null);
                }
                $doc->set('template', $tId);
                $doc->save();

            }
        } else {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Could not find the Log Report resource. Make sure it has a non-empty template');
        }

        $success = true;
        break;

    /* This code will execute during an uninstall */
    case xPDOTransport::ACTION_UNINSTALL:
        $modx->log(xPDO::LOG_LEVEL_INFO,'Uninstalling . . .');

        /* remove log file */

        if (unlink($logFile)) {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Removed log file');
        } else {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Failed to remove log file');
        }

        $success = true;
        break;

}
$modx->log(xPDO::LOG_LEVEL_INFO,'Script resolver actions completed');
return $success;