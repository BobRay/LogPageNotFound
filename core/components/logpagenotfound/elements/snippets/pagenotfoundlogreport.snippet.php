<?php
/**
 * PageNotFoundLogReport
 * Copyright 2011-2023 Bob Ray
 *
 * PageNotFoundLogReport is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * PageNotFoundLogReport is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * PageNotFoundLogReport; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package logpagenotfound
 * @author Bob Ray <https://bobsguides.com>

 *
 * Description: The PageNotFoundLogReport snippet presents the contents of the Page Not Found log as a table.
 *
 * /

/* Modified: January, 2013 */
/** @var $modx modX */
/** @var $scriptProperties array */

$file = $modx->getOption('log_path', $scriptProperties, MODX_CORE_PATH . 'cache/logs/pagenotfound.log', true);
$rowTpl = '
<tr class="lpnf_row">
  <td class="lpnf_cell lpnf_page">[[+page]]</td>
  <td class="lpnf_cell lpnf_time">[[+time]]</td>  
  <td class="lpnf_cell lpnf_ip">[[+ip]]</td>
  <td class="lpnf_cell lpnf_userAgent">[[+userAgent]]</td>
  <td class="lpnf_cell lpnf_host">[[+host]]</td>
  <td class="lpnf_cell lpnf_referer">[[+referer]]</td>
</tr>';

if (isset($_POST['clearlog'])) {
    file_put_contents($file, "");
    unset($_POST['clearlog']);
}

$output = '';
$i = 0;
$fp = fopen($file, 'r');
if ($fp) {
    $columns = array('page', 'time', 'ip', 'userAgent', 'host',  'referer');

    while (($row = fgetcsv($fp, 1000, "`")) !== false) {
        $final = $rowTpl;
        $j = 0;
        foreach ($row as $item) {
            $item = urldecode($item);
            $item = strip_tags($item);
            $item = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
            $item = str_replace('&amp;', '&', $item);
            $final = str_replace('[[+' . $columns[$j] . ']]', $item, $final);
            $j++;
        }

        $output .=  "\n" . $final;

        $i++;
    }
    fclose($fp);
} else {
    $output = 'Could not open: ' . $file;
}

return $output;