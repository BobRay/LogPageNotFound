<?php
/**
 * LogPageNotFound transport snippets
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
 * Description:  Array of snippet objects for LogPageNotFound package
 * @package logpagenotfound
 * @subpackage build
 */
/** @var $modx modX */
/** @var $sources array */

if (! function_exists('getSnippetContent')) {
    function getSnippetContent($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<?php','',$o);
        $o = str_replace('?>','',$o);
        $o = trim($o);
        return $o;
    }
}
$snippets = array();

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'PageNotFoundLogReport',
    'description' => 'PageNotFoundLogReport snippet for LogPageNotFound.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/pagenotfoundlogreport.snippet.php'),
),'',true,true);
$properties = include $sources['data'].'/properties/properties.pagenotfoundlogreport.php';
$snippets[1]->setProperties($properties);

unset($properties);

return $snippets;