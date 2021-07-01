<?php
/***********************************************
* File      :   admin_batchmanager.php
* Project   :   piwigo-openstreetmap
* Descr     :   handle batch manager
* Base on   :   RV Maps & Earth plugin
*
* Created   :   4.06.2013
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

// Hook to add a new filter in the batch mode
add_event_handler('get_batch_manager_prefilters', 'osm_get_batch_manager_prefilters');
function osm_get_batch_manager_prefilters($prefilters)
{
	$prefilters[] = array('ID' => 'osm0', 'NAME' => l10n('OSM Geotagged'));
	$prefilters[] = array('ID' => 'osm1', 'NAME' => l10n('OSM Not geotagged'));
	$prefilters[] = array('ID' => 'osm2', 'NAME' => l10n('OSM GPX tracks'));
	return $prefilters;
}

// Hook to perfom the filter in the batch mode
add_event_handler('perform_batch_manager_prefilters', 'osm_perform_batch_manager_prefilters', 50, 2);
function osm_perform_batch_manager_prefilters($filter_sets, $prefilter)
{
	if ($prefilter==="osm0")
		$filter = "`latitude` IS NOT NULL and `longitude` IS NOT NULL";
	else if ($prefilter==="osm1")
		$filter = "`latitude` IS NULL OR `longitude` IS NULL";
	else if ($prefilter==="osm2")
		$filter = "`path` LIKE '%.gpx%'";

	if ( isset($filter) )
	{
		$query = "SELECT id FROM ".IMAGES_TABLE." WHERE ".$filter;
		$filter_sets[] = array_from_query($query, 'id');
	}
	return $filter_sets;
}

// Hook to show action when selected
add_event_handler('loc_end_element_set_global', 'osm_loc_end_element_set_global');
function osm_loc_end_element_set_global()
{
	global $template, $conf, $prefixeTable;
	define('osm_place_table', $prefixeTable.'osm_places');
	// Save location, eg Place
	$list_of_places = array();
	$available_places = array();
	$place_options = array();
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
		$place_options[] = '<option value="' . $row['id'] . '">' . $row['name'] . '</options>';
	}
	$jsplaces = "\nvar arr_places = ". json_encode($list_of_places) .";\n";

	$batch_global_height = isset($conf['osm_conf']['batch']['global_height']) ? $conf['osm_conf']['batch']['global_height'] : '200';
	$template->append('element_set_global_plugins_actions',
		array('ID' => 'openstreetmap', 'NAME'=>l10n('OSM GeoTag'), 'CONTENT' => '
  <label>'.l10n('Latitude').' (-90=S to 90=N)
    <input type="text" size="8" id="osmlat" name="osmlat">
  </label>
  <label>'.l10n('Longitude').' (-180=W to 180=E)
    <input type="text" size="9" id="osmlon" name="osmlon">
  </label>
  </label> '.l10n( 'EMPTY_COORD_VAL_WARNING').'
  <label>Saved Places:
    <select id="osmplaces" name="osmplaces" >
      <option value="NULL">--</option>
      '. implode("\n", $place_options) . '
    </select>
  <style type="text/css"> .map1 { height: '. $batch_global_height .'px !important; width:100% !important; margin: 5px; } </style>
  <script src="plugins/piwigo-openstreetmap/leaflet/qleaflet.jquery.js"></script>
  <div class="osm-map1 map1"></div>
  <script>
    $(document).ready(function() {' . $jsplaces . '
	 var map;
         $("#permitAction").on("change", function (e) {
            var optionSelected = $("option:selected", this);
            if ("openstreetmap" == optionSelected.val()) {
              map = $(".osm-map1").qleaflet();
	      map.click(function(a,b,c) {
	      $("#osmplaces").val("NULL");
	      });
            }
         });
	 $("#osmplaces").change(function(){
	   var select = $("#osmplaces").val();
	   var lat_elem = $("#osmlat");
	   var lon_elem = $("#osmlon");
	   if (select == "NULL")
	   {
	     lat_elem.val(0);
	     lon_elem.val(0);
	   }
	     else
	   {
	     lat_elem.val(arr_places[select][1]);
	     lon_elem.val(arr_places[select][2]);
	   }
	 });
    });
  </script>
'));
}

// Hook to perform the action on in global mode
add_event_handler('element_set_global_action', 'osm_element_set_global_action', 50, 2);
function osm_element_set_global_action($action, $collection)
{
	if ($action!=="openstreetmap")
		return;

	global $page;

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
	elseif ( strlen($lat)==0 and strlen($lon)==0 )
		$update_query = 'latitude=NULL, longitude=NULL';
	else
		$page['errors'][] = 'Both latitude/longitude must be empty or not empty';

	if (isset($update_query))
	{
		$update_query = 'UPDATE '.IMAGES_TABLE.' SET '.$update_query.'
					WHERE id IN ('.implode(',',$collection).')';
		pwg_query($update_query);
	}
}

// Hook to perform the action on in single mode
add_event_handler('loc_begin_element_set_unit', 'osm_loc_begin_element_set_unit');
function osm_loc_begin_element_set_unit()
{
	global $page;

	if (!isset($_POST['submit']))
	      return;

	$collection = explode(',', $_POST['element_ids']);
	$query = "SELECT `id`, `latitude`, `longitude`
			FROM ".IMAGES_TABLE."
			WHERE id IN (".implode(',',$collection).")";

	$datas = array();
	$errors = array();
	$form_errors = 0;

	$result = pwg_query($query);
	while ($row = pwg_db_fetch_assoc($result))
	{
		if (!isset($_POST['osmlat-'.$row['id']]))
		{
			$form_errors++;
			continue;
		}
		$error = false;
		$data = array(
			'id' => $row['id'],
			'latitude' => trim($_POST['osmlat-'.$row['id']]),
			'longitude' => trim($_POST['osmlon-'.$row['id']])
		);

		if ( strlen($data['latitude'])>0 and strlen($data['longitude'])>0 )
		{
			if ( !is_numeric($data['latitude']) or !is_numeric($data['longitude'])
				or (double)$data['latitude']>90 or (double)$data['latitude']<-90
				or (double)$data['longitude']>180 or (double)$data['longitude']<-180 )
				$error = true;
		}
		elseif ( strlen($data['latitude'])==0 and strlen($data['longitude'])==0 )
		{
			// nothing
		}
		else
		{
			$error = true;
		}

		if ($error)
			$errors[] = $row['name'];
		else
			$datas[] = $data;
	}

	mass_updates(
		IMAGES_TABLE,
		array(
			'primary' => array('id'),
			'update' => array('latitude', 'longitude')
		),
		$datas
	);

	if (count($errors)>0)
	{
		$page['errors'][] = 'Invalid latitude or longitude value for files: '.implode(', ', $errors);
	}
	if ($form_errors)
		$page['errors'][] = 'OpenStreetMap: Invalid form submission for '.$form_errors.' photos';
}

// Hoook for batch manager in single mode
add_event_handler('loc_end_element_set_unit', 'osm_loc_end_element_set_unit');
function osm_loc_end_element_set_unit()
{
	global $template, $conf, $page, $is_category, $category_info;
	$template->set_prefilter('batch_manager_unit', 'osm_prefilter_batch_manager_unit');
}

function osm_prefilter_batch_manager_unit($content)
{
	global $conf;

	$needle = '</table>';
	$pos = strpos($content, $needle);
	$batch_unit_height = isset($conf['osm_conf']['batch']['unit_height']) ? $conf['osm_conf']['batch']['unit_height'] : '200';
	if ($pos!==false)
	{
		$add = '<tr><td><strong>{\'OSM Geotag\'|@translate}</strong></td>
		  <td>
		    <label>{\'Latitude\'|@translate}
		      <input type="text" size="8" name="osmlat-{$element.id}" value="{$element.latitude}">
		    </label>
		    <label>{\'Longitude\'|@translate}
		      <input type="text" size="9" name="osmlon-{$element.id}" value="{$element.longitude}">
		    </label>

<style type="text/css"> .map1 { height: '. $batch_unit_height .'px !important; width:100% !important; margin: 5px; } </style>
<script src="plugins/piwigo-openstreetmap/leaflet/qleaflet.jquery.js"></script>
<div class="osm-map-{$element.id} map1" data-markerpos="{$element.latitude},{$element.longitude}" data-markertext="{$element.name}" data-formid="{$element.id}"></div>
<script>
  $(document).ready(function() {
        $(".osm-map-{$element.id}").qleaflet();
  });
</script>

		  </td>
		</tr>';
		$content = substr_replace($content, $add, $pos, 0);
	}
	return $content;
}
