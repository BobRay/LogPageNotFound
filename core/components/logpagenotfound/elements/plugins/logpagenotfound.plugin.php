<?php
/**
 * LogPageNotFound Plugin
 *
 * Copyright 2011-2017 Bob Ray <https://bobsguides.com>
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
        $host = @dns_get_record($ptr, DNS_PTR);
        if ($host == null) {
            return $ip;
        }
        else {
            return $host[0]['target'];
        }
    }
}
if (!function_exists("logLine")) {
    function logLine($line, $maxLines, $file)
    {

        if ($line) {
            $line = strip_tags($line);
            $line = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');

            $log = file($file);
            if ($fp = fopen($file, 'a')) { // tiny danger of 2 threads interfering; live with it
                if (count($log) >= $maxLines) {
                    fclose($fp);
                    while (count($log) >= $maxLines)
                    {
                        array_shift($log);
                    }
                    array_push($log, $line);
                    $line = implode('', $log);
                    $fp = fopen($file, 'w');
                }
                fwrite($fp, $line);
                fclose($fp);
            }

        }
        return;
    }
}
/* Function courtesy of Anonymous at php.net */
if (! function_exists('get_browser_name')) {
    function get_browser_name($user_agent) {
        // Make case-insensitive.
        $t = strtolower($user_agent);

        // If the string *starts* with the string, strpos returns 0 (i.e., FALSE). Do a ghetto hack and start with a space.
        // "[strpos()] may return Boolean FALSE, but may also return a non-Boolean value, which evaluates to FALSE."
        //     http://php.net/manual/en/function.strpos.php
        $t = " " . $t;

        // Humans / Regular Users
        if (strpos($t, 'opera') || strpos($t, 'opr/')) {
            return 'Opera';
        } elseif (strpos($t, 'edge')) {
            return 'Edge';
        } elseif (strpos($t, 'chrome')) {
            return 'Chrome';
        } elseif (strpos($t, 'safari')) {
            return 'Safari';
        } elseif (strpos($t, 'firefox')) {
            return 'Firefox';
        } elseif (strpos($t, 'msie') || strpos($t, 'trident/7')) {
            return 'Internet Explorer';
        } // Search Engines
        elseif (strpos($t, 'google')) {
            return '[Bot] Googlebot';
        } elseif (strpos($t, 'bing')) {
            return '[Bot] Bingbot';
        } elseif (strpos($t, 'slurp')) {
            return '[Bot] Yahoo! Slurp';
        } elseif (strpos($t, 'duckduckgo')) {
            return '[Bot] DuckDuckBot';
        } elseif (strpos($t, 'baidu')) {
            return '[Bot] Baidu';
        } elseif (strpos($t, 'yandex')) {
            return '[Bot] Yandex';
        } elseif (strpos($t, 'sogou')) {
            return '[Bot] Sogou';
        } elseif (strpos($t, 'exabot')) {
            return '[Bot] Exabot';
        } elseif (strpos($t, 'msn')) {
            return '[Bot] MSN';
        } // Common Tools and Bots
        elseif (strpos($t, 'mj12bot')) {
            return '[Bot] Majestic';
        } elseif (strpos($t, 'ahrefs')) {
            return '[Bot] Ahrefs';
        } elseif (strpos($t, 'semrush')) {
            return '[Bot] SEMRush';
        } elseif (strpos($t, 'rogerbot') || strpos($t, 'dotbot')) {
            return '[Bot] Moz or OpenSiteExplorer';
        } elseif (strpos($t, 'frog') || strpos($t, 'screaming')) {
            return '[Bot] Screaming Frog';
        } // Miscellaneous
        elseif (strpos($t, 'facebook')) {
            return '[Bot] Facebook';
        } elseif (strpos($t, 'pinterest')) {
            return '[Bot] Pinterest';
        } // Check for strings commonly used in bot user agents
        elseif (strpos($t, 'crawler') || strpos($t, 'api') ||
            strpos($t, 'spider') || strpos($t, 'http') ||
            strpos($t, 'bot') || strpos($t, 'archive') ||
            strpos($t, 'info') || strpos($t, 'data')) {
            return '[Bot] Other';
        }
        return 'Other (unknown)';
    }
}

/* Holds info to save to log */
$data = array(
    'page' => '',
    'time' => '',
    'ip' => '',
    'host' => '',
    'userAgent' => '',
    'referer' => ''
);
/* Don't execute in Manager */
/** @var $modx modX */
/** @var $scriptProperties array */
if ($modx->context->get('key') == 'mgr') {
    return '';
}
$oldSetting = ignore_user_abort(TRUE); // otherwise can screw up logfile

$modx->log(modX::LOG_LEVEL_ERROR, 'REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
$modx->log(modX::LOG_LEVEL_ERROR, 'base_url: : ' . $modx->getOption('base_url'));
$page = $_SERVER['REQUEST_URI'];

/* Remove site url prefix */
$siteUrl = $modx->getOption('base_url');
$page = (strpos($page, $siteUrl) === 0)
    ? substr($page, strlen($siteUrl))
    : $page;

$data['page'] = $page;
$t = gettimeofday();
$data['time'] = date('m/d/y H:i:s:');

$ip = $_SERVER['REMOTE_ADDR'];
if (strpos($ip, ',') !== false) {
    $ips = explode(', ', $ip);
    $ip = $ips[0];
}
if ($ip == '::1') {
    $ip = '127.0.0.1';
}
$data['ip'] = $ip;

$data['userAgent'] = isset($_SERVER['HTTP_USER_AGENT'])
   ? get_browser_name($_SERVER['HTTP_USER_AGENT'])
   : '(unknown)';

$data['host'] =  ($ip === '127.0.0.1')
    ? 'localhost'
    : $data['host'] = get_host($data['ip']);

$data['referer'] = empty($_SERVER['HTTP_REFERER']) ? '(empty)' : $_SERVER['HTTP_REFERER'];

/* Create line for log */
$msg = implode('`', array_values($data));

$maxLines  = $modx->getOption('log_max_lines',$scriptProperties, 300);
$file = $modx->getOption('log_path', $scriptProperties, MODX_CORE_PATH . 'cache/logs/pagenotfound.log', true );

logLine($msg . "\n", $maxLines, $file);

ignore_user_abort($oldSetting);

return '';

