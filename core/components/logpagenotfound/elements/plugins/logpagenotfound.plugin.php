<?php
/**
 * LogPageNotFound Plugin
 *
 * Copyright 2011-2015 Bob Ray <http://bobsguides.com>
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
 * @subpackage build
 */


if (!function_exists("get_host")) {
    function get_host($ip)
    {
        $ptr = implode(".", array_reverse(explode(".", $ip))) . ".in-addr.arpa";
        $host = @@dns_get_record($ptr, DNS_PTR);
        if ($host == null) {
            return $ip;
        }
        else {
            return $host[0]['target'];
        }
    }
}
if (!function_exists("logLine")) {
    function logLine($bLogLine, $maxLines, $file)
    {

        if ($bLogLine) {
            $bLogLine = strip_tags($bLogLine);
            $bLogLine = htmlspecialchars($bLogLine, ENT_QUOTES, 'UTF-8');

            $log = file($file);
            if ($fp = fopen($file, 'a')) { // tiny danger of 2 threads interfering; live with it
                if (count($log) >= $maxLines) {
                    fclose($fp);
                    while (count($log) >= $maxLines)
                    {
                        array_shift($log);
                    }
                    array_push($log, $bLogLine);
                    $bLogLine = implode('', $log);
                    $fp = fopen($file, 'w');
                }
                fputs($fp, $bLogLine);
                fclose($fp);
            }
            return;
        }
    }
}
/* Don't execute in Manager */
/** @var $modx modX */
/** @var $scriptProperties array */
if ($modx->context->get('key') == 'mgr') {
    return '';
}
$oldSetting = ignore_user_abort(TRUE); // otherwise can screw-up logfile
$data['page'] = $_SERVER['REQUEST_URI'];
$t = gettimeofday();
$data['time'] = date('d/m/y H:i:s:') . substr($t['usec'], 0, 3); // H:i:s:u
$data['ip'] = $_SERVER['REMOTE_ADDR'];
$data['userAgent'] = isset($_SERVER['HTTP_USER_AGENT'])
        ? $_SERVER['HTTP_USER_AGENT']
        : '<unknown user agent>';

$data['host'] = get_host($data['ip']);
$data['referer'] = empty($_SERVER['HTTP_REFERER']) ? '(empty)' : $_SERVER['HTTP_REFERER'];
$msg = implode('`', $data);

$maxLines  = $modx->getOption('log_max_lines',$scriptProperties, 300);
$file = $modx->getOption('log_path', $scriptProperties, MODX_CORE_PATH . 'logs/pagenotfound.log', true );

logLine($msg . "\n", $maxLines, $file);

ignore_user_abort($oldSetting);

return '';

