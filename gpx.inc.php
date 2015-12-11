<?php
/***********************************************
* File      :   gpx.inc.php
* Project   :   piwigo-openstreetmap
* Descr     :   Display an OSM map with elevation on GPX item
*
* Created   :   01.11.2014
*
* Copyright 2013-2015 <xbgmsharp@gmail.com>
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

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Add GPX support file extensions
// TF, 22.10.2015: handle kml as well!
array_push($conf['file_ext'], 'gpx', 'kml');

// Hook on to an event to display videos as standard images
add_event_handler('render_element_content', 'osm_render_media', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);
function osm_render_media($content, $picture)
{
	global $template, $picture, $conf;

	//print_r( $picture['current']);
	// do nothing if the current picture is actually an image !
	if ( (array_key_exists('src_image', @$picture['current'])
		&& @$picture['current']['src_image']->is_original()) )
	{
		return $content;
	}
	// If not a GPX or KML file
	if ( (array_key_exists('path', @$picture['current']))
		&& (strpos($picture['current']['path'],".gpx") === false
			&& strpos($picture['current']['path'],".kml") === false))
	{
		return $content;
	}

	$filename = embellish_url(get_gallery_home_url() . $picture['current']['element_url']);
	$height = isset($conf['osm_conf']['gpx']['height']) ? $conf['osm_conf']['gpx']['height'] : '500';
	$width = isset($conf['osm_conf']['gpx']['width']) ? $conf['osm_conf']['gpx']['width'] : '320';

	$local_conf = array();
	$local_conf['contextmenu'] = 'false';
	$local_conf['control'] = true;
	$local_conf['img_popup'] = false;
	$local_conf['popup'] = 2;
	$local_conf['center_lat'] = 0;
	$local_conf['center_lng'] = 0;
	$local_conf['zoom'] = '12';
	$local_conf['divname'] = 'mapgpx';

	$js_data = array(array(null, null, null, null, null, null, null, null));

	$js = osm_get_js($conf, $local_conf, $js_data);

	// Select the template based on the extension
	if (strpos($picture['current']['path'],".gpx") === false)
	{
		$template->set_filenames(
				array('osm_content' => dirname(__FILE__)."/template/osm-kml.tpl")
		);
	} else {
		$template->set_filenames(
				array('osm_content' => dirname(__FILE__)."/template/osm-gpx.tpl")
		);
	}

	// Assign the template variables
	$template->assign(
        array(
			'HEIGHT'   => $height,
			'WIDTH'    => $width,
			'FILENAME' => $filename,
			'OSM_PATH' => embellish_url(get_gallery_home_url().OSM_PATH),
			'OSMGPX'   => $js,
            )
	);

	// Return the rendered html
	$osm_content = $template->parse('osm_content', true);
	return $osm_content;
}

// Hook to display a fallback thumbnail if not defined
add_event_handler('get_mimetype_location', 'osm_get_mimetype_icon');
function osm_get_mimetype_icon($location, $element_info)
{
	if ($element_info == 'gpx' || $element_info == 'kml')
	{
		$location = 'plugins/'
			. basename(dirname(__FILE__))
			. '/mimetypes/'. $element_info. '.png';
	}
	return $location;
}

?>
