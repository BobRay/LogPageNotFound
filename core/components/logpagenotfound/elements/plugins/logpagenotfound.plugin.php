<?php
/**
 * LogPageNotFound Plugin
 *
 * Copyright 2011-2023 Bob Ray <https://bobsguides.com>
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

/* Adapted from StackOverflow: https://stackoverflow.com/a/15497878/ */
function getOS($userAgent) {

    $os_platform = false;

    $os_array = array(
        '/windows nt 11/i' => 'Windows 11',
        '/windows nt 10/i' => 'Windows 10/11',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $os_platform = $value;
        }
    }

    return $os_platform;
}


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


function logLine($modx, $line, $maxLines, $file)
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
            if (!fwrite($fp, $line)) {
                $modx->log(modX::LOG_LEVEL_ERROR, '[LogPageNotFound] Could not write to log file');
            }
            fclose($fp);
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, '[LogPageNotFound] Could not open log file');
        }

    }
    return;
}


function isProxy() {
    $proxy = false;
    $pStrings = array(
        'HTTP_VIA',
        'VIA',
        'Proxy-Connection',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED',
        'HTTP_CLIENT_IP',
        'HTTP_FORWARDED_FOR_IP',
        'X-PROXY-ID',
        'MT-PROXY-ID',
        'X-TINYPROXY',
        'X_FORWARDED_FOR',
        'FORWARDED_FOR',
        'X_FORWARDED',
        'FORWARDED',
        'CLIENT-IP',
        'CLIENT_IP',
        'PROXY-AGENT',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'FORWARDED_FOR_IP',
        'HTTP_PROXY_CONNECTION'
    );

    foreach ($pStrings as $header) {
        if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
            $proxy = true;
        }
    }
    return $proxy;
}


/* Function courtesy of Anonymous at php.net */

function get_browser_name($user_agent) {
    // Make case-insensitive.
    $t = strtolower($user_agent);


    /* In case agent is at beginning of line */
    $t = " " . $t;

    // Humans / Regular Users
    if (strpos($t, 'opera') || strpos($t, 'opr/')) {
        return 'Opera';
    } elseif (strpos($t, 'edge') || strpos($t, 'edg/')) {
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
    return '(unknown)';
}


/* Holds info to save to log */
$data = array(
    'page' => '',
    'time' => '',
    'ip' => '',
    'userAgent' => '',
    'host' => '',
    'referer' => ''
);
/* Don't execute in Manager */
/** @var $modx modX */
/** @var $scriptProperties array */
if ($modx->context->get('key') == 'mgr') {
    return '';
}
$oldSetting = ignore_user_abort(TRUE); // otherwise can screw up logfile

$page = $_SERVER['REQUEST_URI'];

/* Remove site url prefix */
$siteUrl = $modx->getOption('base_url');
$page = (strpos($page, $siteUrl) === 0)
    ? substr($page, strlen($siteUrl))
    : $page;

$data['page'] = $page;
$t = gettimeofday();
$data['time'] = date('m/d/y H:i:s');

$ip = $_SERVER['REMOTE_ADDR'];
if (strpos($ip, ',') !== false) {
    $ips = explode(', ', $ip);
    $ip = $ips[0];
}
if ($ip == '::1') {
    $ip = '127.0.0.1';
}
$data['ip'] = isProxy()
    ? $ip . '(Proxy)'
    : $ip;

if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $agent = get_browser_name($userAgent);
    $os = getOS($userAgent);
    $data['userAgent'] = $agent;
    if ($os) {
        $data['userAgent'] .= ' (' . $os . ')';
    }
} else {
    $data['userAgent'] = '(not set)';
}

$data['host'] =  ($ip === '127.0.0.1')
    ? 'localhost'
    : $data['host'] = get_host($data['ip']);

$data['referer'] = empty($_SERVER['HTTP_REFERER']) ? '(empty)' : $_SERVER['HTTP_REFERER'];

/* Create line for log */
$msg = implode('`', array_values($data));

$maxLines  = $modx->getOption('log_max_lines',$scriptProperties, 300);
$file = $modx->getOption('log_path', $scriptProperties, MODX_CORE_PATH . 'cache/logs/pagenotfound.log', true );

/* Don't record requests for apple-touch-icon or favicon */
if ((strpos($data['page'], 'apple-touch-icon') !== false) || (strpos($data['page'], 'favicon') !== false) ) {
    return '';
}
logLine($modx, $msg . "\n", $maxLines, $file);

ignore_user_abort($oldSetting);

return '';