<?php
/***********************************************
* File      :   admin_photo.php
* Project   :   piwigo-openstreetmap
* Descr     :   Location edit in photo panel
*
* Created   :   2.11.2013
*
* Copyright 2012-2015 <xbgmsharp@gmail.com>
*
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
************************************************/

// Check whether we are indeed included by Piwigo.
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Check access and exit when user status is not ok
check_status(ACCESS_ADMINISTRATOR);

if (!isset($_GET['image_id']) or !isset($_GET['section']))
{
	die('Invalid data!');
}

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];
$self_url = get_root_url().'admin.php?page=plugin&amp;section=piwigo-openstreetmap/admin/admin_photo.php&amp;image_id='.$_GET['image_id'];
$delete_url = get_root_url().'admin.php?page=plugin&amp;section=piwigo-openstreetmap/admin/admin_photo.php&amp;delete_coords=1&amp;image_id='.$_GET['image_id'].'&amp;pwg_token='.get_pwg_token();

load_language('plugin.lang', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');
load_language('plugin.lang', OSM_PATH);

global $template, $page, $conf;

// Delete the extra data
if (isset($_GET['delete_coords']) and $_GET['delete_coords'] == 1)
{
        check_pwg_token();
	$_POST['osmlat'] = "";
	$_POST['osmlon'] = "";
	$_POST['submit'] = 1;
}


if (isset($_POST['submit']))
{
	check_pwg_token();

	$lat = trim($_POST['osmlat']);
	$lon = trim($_POST['osmlon']);
	if ( strlen($lat)>0 and strlen($lon)>0 )
	{
		if ( is_numeric($lat) and is_numeric($lon)
			and (double)$lat<=90 and (double)$lat>=-90
			and (double)$lon<=180 and (double)$lon>=-180 )
			$update_query = 'latitude='.$lat.', longitude='.$lon;
		else
			$page['errors'][] = 'Invalid latitude or longitude value';
	}
	elseif ( (strlen($lat)==0 and strlen($lon)==0) or (isset($_GET['delete_coords']) and $_GET['delete_coords'] == 1)) {
		$update_query = 'latitude=NULL, longitude=NULL';
		array_push( $page['infos'], l10n('COORDS_ERASED'));
	} else {
		$page['errors'][] = 'Both latitude/longitude must be empty or not empty';
	}

	if (isset($update_query))
	{
		$update_query = 'UPDATE '.IMAGES_TABLE.' SET '.$update_query.' WHERE id = '.$_GET['image_id'].';';
		pwg_query($update_query);
	}

	if (count($page['errors']) == 0 )
	{
		array_push( $page['infos'], l10n('The photo was updated'));
	}
}

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('photo');
$tabsheet->select('openstreetmap');
$tabsheet->assign();

$template->set_filenames(
  array(
    'plugin_admin_content' => dirname(__FILE__).'/admin_photo.tpl'
    )
  );

include_once( dirname(__FILE__) .'/../include/functions_map.php');

// Retrieving direct information about picture
$query = "SELECT *,
IF(i.representative_ext IS NULL,
        CONCAT(SUBSTRING_INDEX(TRIM(LEADING '.' FROM i.path), '.', 1 ), '-sq.', SUBSTRING_INDEX(TRIM(LEADING '.' FROM i.path), '.', -1 )),
        TRIM(LEADING '.' FROM
            REPLACE(i.path, TRIM(TRAILING '.' FROM SUBSTRING_INDEX(i.path, '/', -1 )),
                CONCAT('pwg_representative/',
                    CONCAT(
                        TRIM(TRAILING '.' FROM SUBSTRING_INDEX( SUBSTRING_INDEX(i.path, '/', -1 ) , '.', 1 )),
                        CONCAT('-sq.', i.representative_ext)
                    )
                )
            )
        )
    ) AS `pathurl` FROM ".IMAGES_TABLE." AS i WHERE id = ".$_GET['image_id'].";";
$picture = pwg_db_fetch_assoc(pwg_query($query));
$lat = isset($picture['latitude']) ? $picture['latitude'] : 0;
$lon = isset($picture['longitude']) ? $picture['longitude'] : 0;

// Load parameter, fallback to default if unset
$zoom = isset($conf['osm_conf']['right_panel']['zoom']) ? $conf['osm_conf']['right_panel']['zoom'] : '18';
$baselayer = isset($conf['osm_conf']['map']['baselayer']) ? $conf['osm_conf']['map']['baselayer'] : 'mapnik';
$custombaselayer = isset($conf['osm_conf']['map']['custombaselayer']) ? $conf['osm_conf']['map']['custombaselayer'] : '';
$custombaselayerurl = isset($conf['osm_conf']['map']['custombaselayerurl']) ? $conf['osm_conf']['map']['custombaselayerurl'] : '';
$noworldwarp = isset($conf['osm_conf']['map']['noworldwarp']) ? $conf['osm_conf']['map']['noworldwarp'] : 'false';
$attrleaflet = isset($conf['osm_conf']['map']['attrleaflet']) ? $conf['osm_conf']['map']['attrleaflet'] : 'false';
$attrimagery = isset($conf['osm_conf']['map']['attrimagery']) ? $conf['osm_conf']['map']['attrimagery'] : 'false';
$attrmodule = isset($conf['osm_conf']['map']['attrplugin']) ? $conf['osm_conf']['map']['attrplugin'] : 'false';

if ($lat == 0 and $lon == 0) { $zoom = 2; }

$local_conf = array();
$local_conf['contextmenu'] = 'false';
$local_conf['control'] = true;
$local_conf['img_popup'] = false;
$local_conf['popup'] = 2;
$local_conf['center_lat'] = $lat;
$local_conf['center_lng'] = $lon;
$local_conf['zoom'] = $zoom;
$local_conf['editor'] = true;

$pathurl = get_absolute_root_url() ."i.php?".$picture['pathurl'];
$js_data = array(array($lat, $lon, null, $pathurl, null, null, null, null));

$js = osm_get_js($conf, $local_conf, $js_data);

// Fetch the template.
global $prefixeTable;
// Easy access
define('osm_place_table', $prefixeTable.'osm_places');
// Save location, eg Place
$list_of_places = array();
$available_places = array();
  $query = '
SELECT id, name, latitude, longitude
  FROM '.osm_place_table.'
;';
  $result = pwg_query($query);
// JS for the template
  while ($row = pwg_db_fetch_assoc($result))
  {
    $list_of_places[$row['id']] = array($row['name'], $row['latitude'], $row['longitude']);
    $available_places[$row['id']] =  $row['name'];
  }

$jsplaces = "\nvar arr_places = ". json_encode($list_of_places) .";\n";

$template->assign(array(
	'PWG_TOKEN'		=> get_pwg_token(),
	'F_ACTION'		=> $self_url,
	'DELETE_URL'		=> $delete_url,
	'TN_SRC'		=> DerivativeImage::thumb_url($picture).'?'.time(),
	'TITLE'			=> render_element_name($picture),
	'OSM_PATH'		=> embellish_url(get_absolute_root_url().OSM_PATH),
	'OSM_JS'		=> $js,
	'LAT'			=> $lat,
	'LON'			=> $lon,
	'AVAILABLE_PLACES'	=> $available_places,
	'LIST_PLACES'		=> $jsplaces,
));

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>
