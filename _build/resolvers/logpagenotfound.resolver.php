<?php

/**
 * LogPageNotFound resolver script - runs on install.
 *
 * Copyright 2011-2023 Bob Ray <https://bobsguides.com>
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

/** @var $modx modX */
/** @var  $object object */
/** @var  $options array */

/** @var modTransportPackage $transport */

if ($transport) {
    $modx =& $transport->xpdo;
} else {
    $modx =& $object->xpdo;
}

/* Remember that the files in the _build directory are not available
 * here, and we don't know the IDs of any objects, so resources,
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


/* Make it run in either MODX 2 or MODX 3 */
$prefix = $modx->getVersionData()['version'] >= 3
    ? 'MODX\Revolution\\'
    : '';

/* work starts here */
$success = true;

/* if $oldLog exists, it will be copied to core/cache/logs */
$oldLog = MODX_CORE_PATH . 'logs/pagenotfound.log';
$logDir = MODX_CORE_PATH . 'cache/logs/';
$newLog = $logDir . 'pagenotfound.log';

$modx->log(xPDO::LOG_LEVEL_INFO, 'Running PHP Resolver.');

switch ($options[xPDOTransport::PACKAGE_ACTION]) {

    case xPDOTransport::ACTION_UPGRADE:
        /* Move log file to new location */
        if (file_exists($oldLog) && (! file_exists($newLog))) {
            if (copy($oldLog, $newLog)) {
                $modx->log(xPDO::LOG_LEVEL_INFO, "Moved log to {$newLog}");
                if (unlink($oldLog)) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, "Removed old log");
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to remove old log at " .
                       $oldLog);
                }
            } else{
                $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to copy old log {$oldLog} to {$newLog} please copy manually");
            }
            /* Remove properties */
            $snippet = $modx->getObject($prefix . 'modSnippet', array('name' => 'PageNotFoundLogReport'), false);

            $p = $snippet->get('_properties');
            if (!empty($p)) {
                $snippet->setProperties('a:0:{}');
                $snippet->save(true);
            }

        }

    case xPDOTransport::ACTION_INSTALL:

        /* Create log directory if necessary */
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755)) {
                $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to create directory: $logDir");
            } else {
                $modx->log(xPDO::LOG_LEVEL_INFO, "Created directory: {$logDir}");
                $fp = fopen($newLog, 'w');
                if ($fp) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, "Created file: {$newLog}");
                    fclose($fp);
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to create file: {$newLog}");
                }
            }
        } else {
            if (!file_exists($newLog)) {
                $fp = fopen($newLog, 'w');
                if ($fp) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, "Created file: {$newLog}");
                    fclose($fp);
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, "Failed to create file: {$newLog}");
                }

            }
        }
        $success = true;
        break;

    /* This code will execute during an uninstall */
    case xPDOTransport::ACTION_UNINSTALL:
        $modx->log(xPDO::LOG_LEVEL_INFO, 'Uninstalling . . .');

        /* remove log file */

        if (unlink($newLog)) {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Removed log file');
        } else {
            $modx->log(xPDO::LOG_LEVEL_INFO, 'Failed to remove log file');
        }

        $success = true;
        break;

}
$modx->log(xPDO::LOG_LEVEL_INFO, 'Script resolver actions completed');
return $success;