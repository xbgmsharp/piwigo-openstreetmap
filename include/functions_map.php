<?php
/***********************************************
* File      :   functions_map.php
* Project   :   piwigo-openstreetmap
* Descr     :   Read Geotag Metdata
* Base on   :   RV Maps & Earth plugin
*
* Created   :   30.05.2013
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

function osmcopyright($attrleaflet, $attrimagery, $attrmodule, $bl, $custombaselayer)
{
    return '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
    $return = "";

    if ($attrleaflet) $return .= '<a href="http://leafletjs.com/" target="_blank">Leaflet</a> ';

    if ($attrmodule) $return .= l10n('PLUGINBY').' <a href="https://github.com/xbgmsharp/piwigo-openstreetmap" target="_blank">xbgmsharp</a> ';

    if ($attrimagery)
    {
        $return .= " ";
        if     ($bl == 'mapnik')	$return .= "Tiles Courtesy of OSM.org (CC BY-SA)";
        else if($bl == 'mapnikfr')	$return .= "Tiles Courtesy of Openstreetmap.fr (CC BY-SA)";
        else if($bl == 'mapnikde')	$return .= "Tiles Courtesy of Openstreetmap.de (CC BY-SA)";
        else if($bl == 'blackandwhite')	$return .= "Tiles Courtesy of OSM.org (CC BY-SA)";
        else if($bl == 'mapnikhot')	$return .= 'Tiles Courtesy of &copy; <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>';
        else if($bl == 'mapquest')	$return .= 'Tiles Courtesy of &copy; <a href="http://www.mapquest.com/">MapQuest</a>';
        else if($bl == 'mapquestaerial')	$return .= 'Tiles Courtesy of <a href="http://www.mapquest.com/">MapQuest</a> &mdash; Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency';
        else if($bl == 'toner')		$return .= 'Tiles Courtesy of &copy; <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash;';
        else if($bl == 'custom')	$return .= $custombaselayer;
        else if($bl == 'esri')		$return .= "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community";
    }
    // Mandatory by http://www.openstreetmap.org/copyright
    $return .= ' &copy; ';
    $return .= l10n("OSM_CONTRIBUTORS");
    return $return;
}

function osm_get_gps($page)
{
    // Limit search by category, by tag, by smartalbum
    $LIMIT_SEARCH="";
    $INNER_JOIN="";
    if (isset($page['section']))
    {
        if ($page['section'] === 'categories' and isset($page['category']) and isset($page['category']['id']) )
        {
            $LIMIT_SEARCH = "FIND_IN_SET(".$page['category']['id'].", c.uppercats) AND ";
            $INNER_JOIN = "INNER JOIN ".CATEGORIES_TABLE." AS c ON ic.category_id = c.id";
        }
        if ($page['section'] === 'tags' and isset($page['tags']) and isset($page['tags'][0]['id']) )
        {
            $items = get_image_ids_for_tags( array_reduce( $page['tags'], 'osm_get_page_tag_id' ) );
            if ( !empty($items) )
            {
                $LIMIT_SEARCH = "ic.image_id IN (".implode(',', $items).") AND ";
            }
        }
        if ($page['section'] === 'tags' and isset($page['category']) and isset($page['category']['id']) )
        {
            $LIMIT_SEARCH = "FIND_IN_SET(".$page['category']['id'].", c.uppercats) AND ";
            $INNER_JOIN = "INNER JOIN ".CATEGORIES_TABLE." AS c ON ic.category_id = c.id";
        }
    }

    $forbidden = get_sql_condition_FandF(
        array
        (
            'forbidden_categories' => 'ic.category_id',
            'visible_categories' => 'ic.category_id',
            'visible_images' => 'i.id'
        ),
        "\n AND"
    );

    /* Get all GPX tracks */
    $query="SELECT i.path FROM ".IMAGES_TABLE." AS i
            INNER JOIN (".IMAGE_CATEGORY_TABLE." AS ic ".$INNER_JOIN.") ON i.id = ic.image_id
            WHERE ".$LIMIT_SEARCH." `path` LIKE '%.gpx' ".$forbidden." ";

    return array_from_query($query, 'path');
}

function osm_get_items($page)
{
    // Limit search by category, by tag, by smartalbum
    $LIMIT_SEARCH="";
    $INNER_JOIN="";
    if (isset($page['section']))
    {
        if ($page['section'] === 'categories' and isset($page['category']) and isset($page['category']['id']) )
        {
            $LIMIT_SEARCH = "FIND_IN_SET(".$page['category']['id'].", c.uppercats) AND ";
            $INNER_JOIN = "INNER JOIN ".CATEGORIES_TABLE." AS c ON ic.category_id = c.id";
        }
        if ($page['section'] === 'tags' and isset($page['tags']) and isset($page['tags'][0]['id']) )
        {
            $items = get_image_ids_for_tags( array_reduce( $page['tags'], 'osm_get_page_tag_id' ) );
            if ( !empty($items) )
            {
                $LIMIT_SEARCH = "ic.image_id IN (".implode(',', $items).") AND ";
            }
        }
        if ($page['section'] === 'tags' and isset($page['category']) and isset($page['category']['id']) )
        {
            $LIMIT_SEARCH = "FIND_IN_SET(".$page['category']['id'].", c.uppercats) AND ";
            $INNER_JOIN = "INNER JOIN ".CATEGORIES_TABLE." AS c ON ic.category_id = c.id";
        }
    }

    $forbidden = get_sql_condition_FandF(
        array
        (
            'forbidden_categories' => 'ic.category_id',
            'visible_categories' => 'ic.category_id',
            'visible_images' => 'i.id'
        ),
        "\n AND"
    );

    /* We have lat and lng coordonate for virtual album */
    if (isset($_GET['min_lat']) and isset($_GET['max_lat']) and isset($_GET['min_lng']) and isset($_GET['max_lng']))
    {
        $LIMIT_SEARCH="";
        $INNER_JOIN="";

        foreach (array('min_lat', 'min_lng', 'max_lat', 'max_lng') as $get_key)
        {
                check_input_parameter($get_key, $_GET, false, '/^-?\d+(\.\d+)?$/');
        }

        /* Delete all previous album */
        $query="SELECT `id` FROM ".CATEGORIES_TABLE." WHERE `name` = 'Locations' AND `comment` LIKE '%OSM plugin%';";
        $ids = array_from_query($query, 'id');
        /* Unlink items for the previous album */
        delete_categories($ids, $photo_deletion_mode='no_delete');

        /* Create an album */
        $options = array(
            'comment'=> 'Generated by OSM plugin',
        );
        $osm_album = create_virtual_category('Locations', NULL, $options);

        /* Create a sub album */
        $options = array(
            'comment'=> "OSM virtual album\nlat:".$_GET['min_lat']." ".$_GET['max_lat']."\nlng:".$_GET['min_lng']." ".$_GET['max_lng'],
        );
        $osm_sub_album = create_virtual_category("OSM".$_GET['min_lat']."", $osm_album['id'], $options);

        /* Get all items inside the lat and lng */
        $query="SELECT  `id`, `latitude`, `longitude` 
    FROM ".IMAGES_TABLE." AS i
        INNER JOIN ".IMAGE_CATEGORY_TABLE." AS ic ON id = ic.image_id
    WHERE ".$LIMIT_SEARCH." `latitude` IS NOT NULL AND `longitude` IS NOT NULL 
    AND `latitude` > ".$_GET['min_lat']." AND `latitude` < ".$_GET['max_lat']."
    AND `longitude` > ".$_GET['min_lng']." AND `longitude` < ".$_GET['max_lng']."
    ".$forbidden.";";

        $items = hash_from_query( $query, 'id');

        /* Add  items to the new sub album */
        foreach ($items as $item)
        {
            $query="INSERT INTO ".IMAGE_CATEGORY_TABLE." ( `image_id` ,`category_id` ,`rank` ) VALUES ( '".$item['id']."', '".$osm_sub_album['id']."', NULL );";
            pwg_query($query);
        }

        /* Redirect to the new album */
        header('Location: '.get_absolute_root_url().'index.php?/category/'.$osm_sub_album['id']);
        exit;
    }

    // Fetch data with latitude and longitude
    //$query="SELECT `latitude`, `longitude`, `name`, `path` FROM ".IMAGES_TABLE." WHERE `latitude` IS NOT NULL AND `longitude` IS NOT NULL;";
    // SUBSTRING_INDEX(TRIM(LEADING '.' FROM `path`), '.', 1) full path without filename extension
    // SUBSTRING_INDEX(TRIM(LEADING '.' FROM `path`), '.', -1) full path with only filename extension


    if (isset($page['image_id'])) $LIMIT_SEARCH .= 'i.id = ' . $page['image_id'] . ' AND ';

    $query="SELECT i.latitude, i.longitude,
    IFNULL(i.name, '') AS `name`,
    TRIM(LEADING '.' FROM IF(i.representative_ext IS NULL,
        CONCAT(LEFT(i.path,LENGTH(i.path)-1-LENGTH(SUBSTRING_INDEX(i.path, '.', -1 ))), '-sq.', SUBSTRING_INDEX(i.path, '.', -1 )),
            REPLACE(i.path, TRIM(TRAILING '.' FROM SUBSTRING_INDEX(i.path, '/', -1 )),
                CONCAT('pwg_representative/',
                    CONCAT(
                        TRIM(TRAILING '.' FROM SUBSTRING_INDEX( SUBSTRING_INDEX(i.path, '/', -1 ) , '.', 1 )),
                        CONCAT('-sq.', i.representative_ext)
                    )
                )
            )
    )) AS `pathurl`,
    TRIM(TRAILING '/' FROM CONCAT( i.id, '/category/', IFNULL(ic.category_id, '') ) ) AS `imgurl`,
    IFNULL(i.comment, '') AS `comment`,
    IFNULL(i.author, '') AS `author`,
    i.width
        FROM ".IMAGES_TABLE." AS i
            INNER JOIN (".IMAGE_CATEGORY_TABLE." AS ic ".$INNER_JOIN.") ON i.id = ic.image_id
            WHERE ".$LIMIT_SEARCH." i.latitude IS NOT NULL AND i.longitude IS NOT NULL ".$forbidden." GROUP BY i.id;";
    //echo $query;
    $php_data = array_from_query($query);
    //print_r($php_data);
    $js_data = array();
    foreach($php_data as $array)
    {
        // MySQL did all the job
        //print_r($array);
        $js_data[] = array((double)$array['latitude'],
                   (double)$array['longitude'],
                   $array['name'],
                   get_absolute_root_url() ."i.php?".$array['pathurl'],
                   get_absolute_root_url() ."picture.php?/".$array['imgurl'],
                   $array['comment'],
                   $array['author'],
                   (int)$array['width']
                   );
    }
    /* START Debug generate dummy data
    $js_data = array();
    $str = 'abcdef';
    $minLat = -90.00;
    $maxLat = 90.00;
    $minLon = -180.00;
    $maxLon = 180.00;
    for ($i = 1; $i <= 5000; $i++)
    {
        $js_data[] = array( (double)$minLat + (double)((float)rand()/(float)getrandmax() * (($maxLat - $minLat) + 1)),
                   (double)$minLon + (double)((float)rand()/(float)getrandmax() * (($maxLon - $minLon) + 1)),
                   str_shuffle($str),
                   "http://placehold.it/120x120",
                   "http://placehold.it/200x200",
                   "Comment",
                   "Author",
                   (int)120
                   );
    }
    END Debug generate dummy data */
    return $js_data;
}

function osm_get_js($conf, $local_conf, $js_data)
{
    // Load parameter, fallback to default if unset
    if (isset($local_conf['popup']))
        $popup = $local_conf['popup'];
    else
        $popup = isset($conf['osm_conf']['left_menu']['popup']) ? $conf['osm_conf']['left_menu']['popup'] : 0;
    $linkname = isset($conf['osm_conf']['left_menu']['link']) ? $conf['osm_conf']['left_menu']['link'] : 'OS World Map';
    $popupinfo_name = isset($conf['osm_conf']['left_menu']['popupinfo_name']) ? $conf['osm_conf']['left_menu']['popupinfo_name'] : 0;
    $popupinfo_img = isset($conf['osm_conf']['left_menu']['popupinfo_img']) ? $conf['osm_conf']['left_menu']['popupinfo_img'] : 0;
    $popupinfo_link = isset($conf['osm_conf']['left_menu']['popupinfo_link']) ? $conf['osm_conf']['left_menu']['popupinfo_link'] : 0;
    $popupinfo_comment = isset($conf['osm_conf']['left_menu']['popupinfo_comment']) ? $conf['osm_conf']['left_menu']['popupinfo_comment'] : 0;
    $popupinfo_author  = isset($conf['osm_conf']['left_menu']['popupinfo_author']) ? $conf['osm_conf']['left_menu']['popupinfo_author'] : 0;
    $baselayer = isset($conf['osm_conf']['map']['baselayer']) ? $conf['osm_conf']['map']['baselayer'] : 'mapnik';
    $custombaselayer = isset($conf['osm_conf']['map']['custombaselayer']) ? $conf['osm_conf']['map']['custombaselayer'] : '';
    $custombaselayerurl = isset($conf['osm_conf']['map']['custombaselayerurl']) ? $conf['osm_conf']['map']['custombaselayerurl'] : '';
    $noworldwarp = isset($conf['osm_conf']['map']['noworldwarp']) ? $conf['osm_conf']['map']['noworldwarp'] : 'false';
    $attrleaflet = isset($conf['osm_conf']['map']['attrleaflet']) ? $conf['osm_conf']['map']['attrleaflet'] : 'false';
    $attrimagery = isset($conf['osm_conf']['map']['attrimagery']) ? $conf['osm_conf']['map']['attrimagery'] : 'false';
    $attrmodule = isset($conf['osm_conf']['map']['attrplugin']) ? $conf['osm_conf']['map']['attrplugin'] : 'false';
    $pinid = isset($conf['osm_conf']['pin']['pin']) ? $conf['osm_conf']['pin']['pin'] : 1;
    $pinpath = isset($conf['osm_conf']['pin']['pinpath']) ? $conf['osm_conf']['pin']['pinpath'] : '';
    $pinsize = isset($conf['osm_conf']['pin']['pinsize']) ? $conf['osm_conf']['pin']['pinsize'] : '';
    $pinshadowpath = isset($conf['osm_conf']['pin']['pinshadowpath']) ? $conf['osm_conf']['pin']['pinshadowpath'] : '';
    $pinshadowsize = isset($conf['osm_conf']['pin']['pinshadowsize']) ? $conf['osm_conf']['pin']['pinshadowsize'] : '';
    $pinoffset = isset($conf['osm_conf']['pin']['pinoffset']) ? $conf['osm_conf']['pin']['pinoffset'] : '';
    $pinpopupoffset = isset($conf['osm_conf']['pin']['pinpopupoffset']) ? $conf['osm_conf']['pin']['pinpopupoffset'] : '';
    $divname = isset($local_conf['divname']) ? $local_conf['divname'] : 'map';

    /* If the config include parameters get them */
    $center = isset($conf['osm_conf']['left_menu']['center']) ? $conf['osm_conf']['left_menu']['center'] : '0,0';
    $center_arr = preg_split('/,/', $center);
    $center_lat = isset($center_arr) ? $center_arr[0] : 0;
    $center_lng = isset($center_arr) ? $center_arr[1] : 0;

    /* If we have zoom and center coordonate, set it otherwise fallback default */
    $zoom = isset($_GET['zoom'])
        ? $_GET['zoom']
        : (
            isset($local_conf['zoom'])
                ? $local_conf['zoom']
                : 2
            );
    $center_lat = isset($_GET['center_lat']) ? $_GET['center_lat'] : $center_lat;
    $center_lng = isset($_GET['center_lng']) ? $_GET['center_lng'] : $center_lng;

    $autocenter = isset($local_conf['autocenter'])
        ? $local_conf['autocenter']
        : 0;
    
    // When gallery is SSL and when switching to SSL baselayerURL is possible, use $httpx
    $httpx = ((!empty($_SERVER['HTTPS'])) and (strtolower($_SERVER['HTTPS']) !== 'off')) ? 'https' : 'http';

    // Load baselayerURL
    if     ($baselayer == 'mapnik')     $baselayerurl = $httpx.'://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    else if($baselayer == 'mapquest')   $baselayerurl = 'http://otile1.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png';
    else if($baselayer == 'mapnikde')   $baselayerurl = $httpx.'://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png';
    else if($baselayer == 'mapnikfr')   $baselayerurl = $httpx.'://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
    else if($baselayer == 'blackandwhite')  $baselayerurl = 'http://{s}.www.toolserver.org/tiles/bw-mapnik/{z}/{x}/{y}.png';
    else if($baselayer == 'mapnikhot')  $baselayerurl = $httpx.'://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
    else if($baselayer == 'mapquestaerial') $baselayerurl = 'http://otile1.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png';
    else if($baselayer == 'toner') $baselayerurl = $httpx.'://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.png';
    else if($baselayer == 'custom') $baselayerurl = $custombaselayerurl;
    else if($baselayer == 'esri') $baselayerurl = $httpx.'://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';

    $attribution = osmcopyright($attrleaflet, $attrimagery, $attrmodule, $baselayer, $custombaselayer);

    // Generate Javascript
    // ----------------------------------------
    // no worldWarp (no world copies, restrict the view to one world)
    if($noworldwarp)
    {
        $nowarp = " true ";
        $worldcopyjump = "worldCopyJump: false, maxBounds: [ [82, -180], [-82, 180] ]";
    }
    else
    {
        $nowarp = " false ";
        $worldcopyjump = "worldCopyJump: true";
    }

    //$js = "\nvar addressPoints = ". json_encode($js_data, JSON_UNESCAPED_SLASHES) .";\n";
    $js = "\nvar addressPoints = ". str_replace("\/","/",json_encode($js_data)) .";\n";

    $available_pin = array(
        '0' => '',
        '1' => '',
        '2' => 'PlgIconGreen',
        '3' => 'PlgIconRed',
        '4' => 'LeafIconGreen',
        '5' => 'LeafIconOrange',
        '6' => 'LeafIconRed',
        '7' => 'MapIconBlue',
        '8' => 'MapIconGreen',
        '9' => 'CustomIcon',
        '10' => 'ImgIcon'
    );

    $editor = isset($local_conf['editor']) ? "editInOSMControlOptions: { editors: ['id'] }, " : '';

    if ($divname === 'mapgpx')
    {
        // Create the map and get a new map instance attached and element with $divname
        // we return directly as there is no addressPoints for GPX
        $js .= "\nvar Url = '".$baselayerurl."',
        Attribution = '".$attribution."',
        TileLayer = new L.TileLayer(Url, {maxZoom: 16, noWrap: ".$nowarp.", attribution: Attribution});\n";
        $js .= "var " . $divname . " = new L.Map('" . $divname . "', {" . $worldcopyjump . ", zoom: ".$zoom.", layers: [TileLayer], contextmenu: " . $local_conf['contextmenu'] . "});\n";
        $js .= $divname . ".attributionControl.setPrefix('');\n";
        $js .= "\nL.control.scale().addTo(" . $divname . ");\n";
        return $js;
    } else {
        // Create the map and get a new map instance attached and element with $divname
        $js .= "\nvar Url = '".$baselayerurl."',
        Attribution = '".$attribution."',
        TileLayer = new L.TileLayer(Url, {maxZoom: 16, noWrap: ".$nowarp.", attribution: Attribution}),
        latlng = new L.LatLng(".$local_conf['center_lat'].", ".$local_conf['center_lng'].");\n";
        $js .= "var " . $divname . " = new L.Map('" . $divname . "', {" . $worldcopyjump . ", center: latlng, ".$editor." zoom: ".$zoom.", layers: [TileLayer], contextmenu: " . $local_conf['contextmenu'] . "});\n";
        $js .= $divname . ".attributionControl.setPrefix('');\n";
        $js .= "var MarkerClusterList=[];\n";
        $js .= "if (typeof L.MarkerClusterGroup === 'function')\n";
        $js .= "     var markers = new L.MarkerClusterGroup({maxClusterRadius: 30});\n";
    }

    if ($local_conf['control'] === true)
    {
        $js .= "\nL.control.scale().addTo(" . $divname . ");\n";
    }

    if ($pinid >= 2)
    {
        // Icons
        $js .= "

        var PlgIcon = L.Icon.extend({
            options: {
                shadowUrl: 'plugins/piwigo-openstreetmap/leaflet/images/marker-shadow.png',
                iconSize:     [25, 41],
                shadowSize:   [41, 41],
                iconAnchor:   [21, 21],
                popupAnchor:  [-10, -10]
            }
        });

        var LeafIcon = L.Icon.extend({
            options: {
                shadowUrl: 'plugins/piwigo-openstreetmap/leaflet/images/leaf-shadow.png',
                iconSize:     [38, 95],
                shadowSize:   [50, 64],
                iconAnchor:   [22, 94],
                shadowAnchor: [4, 62],
                popupAnchor:  [-3, -76]
            }
        });

        var MapIcon = L.Icon.extend({
            options: {
                shadowUrl: 'plugins/piwigo-openstreetmap/leaflet/images/mapicons-shadow.png',
                iconSize:     [32, 37],
                shadowSize:   [51, 37],
                iconAnchor:   [19, 38],
                shadowAnchor: [4, 33],
                popupAnchor:  [-2, -33]
            }
        });
        ";

        if ($pinid == 9)
        {
            $js .= "\nvar CustomIcon = L.Icon.extend({
                options: {
                    iconUrl: ".$pinpath.",
                    shadowUrl: ".$pinshadowpath.",
                    iconSize: [".$pinsize."],
                    shadowSize: [".$pinshadowsize."],
                    iconAnchor: [".$pinoffset."],
                    shadowAnchor: [".$pinoffset."],
                    popupAnchor: [".$pinpopupoffset."]
                }
            });";
        }

        $js .= "\nvar ImgIcon = L.Icon.extend({
            options: {
                iconSize:     [42, 42],
                iconAnchor:   [21, 21],
                popupAnchor:  [0,-21]
            }
        });

        var PlgIconGreen = new PlgIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/marker-green.png'}),
            PlgIconRed = new PlgIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/marker-red.png'});

        var LeafIconGreen = new LeafIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/leaf-green.png'}),
            LeafIconRed = new LeafIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/leaf-red.png'}),
            LeafIconOrange = new LeafIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/leaf-orange.png'});

        var MapIconBlue = new MapIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/mapicons-blue.png'}),
            MapIconGreen = new MapIcon({iconUrl: 'plugins/piwigo-openstreetmap/leaflet/images/mapicons-green.png'});

        ";
    }

    $js .= "for (var i = 0; i < addressPoints.length; i++) {
        var a = addressPoints[i];
        var latlng = new L.LatLng(a[0], a[1]);
        var title = a[2];
        var pathurl = a[3];
        var imgurl = a[4];
        var comment = a[5];
        var author = a[6];
        var width = a[7];
        ";

    // create Marker
    if ($pinid == 1) { // 0 is No Marker
        $js .= "var marker = new L.Marker(latlng, { title: title });\n";
    } else if ($pinid >= 2 and $pinid <= 9) {
        $js .= "var marker = new L.Marker(latlng, { title: title, icon: ".$available_pin[$pinid]."});\n";
    } else if ($pinid == 10) {
        $js .= "var marker = new L.Marker(latlng, { title: title, icon: new ImgIcon({iconUrl: pathurl})});\n";
    }

    // create Popup
    if ($popup < 2)
    {
        $myinfo = "'<div id=\"thumb-'+i+'\"><p>";
        if($popupinfo_name)
        {
            $myinfo .= "'+title+'";
        }
        if($popupinfo_img and !$popupinfo_link)
        {
            $myinfo .= "<br /><img src=\"'+pathurl+'\">";
        }
        else if($popupinfo_img and $popupinfo_link)
        {
            if ($local_conf['img_popup'] == true)
                $attribute = ' target=\"_blank\"';
            else
                $attribute = '';
            $myinfo .= "<br /><a target=\"_blank\" href=\"'+imgurl+'\"".$attribute."><img src=\"'+pathurl+'\"></a>";
        }
        if($popupinfo_comment)
        {
            $myinfo .= "<br />'+comment+'";
        }
        if($popupinfo_author)
        {
            $myinfo .= "<br />'+author+'";
        }
        $myinfo .= "</p></div>'";
        $js .= "\tvar myinfo = ".$myinfo.";\n";
        $js .= "\tmarker.bindPopup(myinfo, {minWidth: '+width+'});\n";
    }

        $js .= "\nif (typeof L.MarkerClusterGroup === 'function')
\t    markers.addLayer(marker);
\telse
\t    " . $divname . ".addLayer(marker);
\tMarkerClusterList.push(marker);
\t}";
    if (isset($local_conf['paths'])) {
        foreach ($local_conf['paths'] as $path) {
            $ext = pathinfo($path);
            $ext = $ext['extension'];
            $js .= "\nomnivore.".$ext."('".$path."').addTo(".$divname.");";
        }
    }
    $js .= "\nif (typeof L.MarkerClusterGroup === 'function')\n";
    $js .= "    " . $divname . ".addLayer(markers);\n";
    if ( $autocenter and !isset($_GET['center_lat']) and !isset($_GET['center_lng']) and !isset($_GET['zoom']) ) {
        $js .= "var group = new L.featureGroup(MarkerClusterList);";
        $js .= "this." . $divname . ".whenReady(function () {
        window.setTimeout(function () {
                    " . $divname . ".fitBounds(group.getBounds());
        }.bind(this), 200);
    }, this);";
    }
    return $js;
}

function osm_gen_template($conf, $js, $js_data, $tmpl, $template)
{
    $linkname = isset($conf['osm_conf']['left_menu']['link']) ? $conf['osm_conf']['left_menu']['link'] : l10n('OSWorldMap');
    $template->set_filename('map', dirname(__FILE__). '/../template/' . $tmpl);

    $template->assign(
        array(
            'CONTENT_ENCODING'	=> get_pwg_charset(),
            'OSM_PATH'			=> embellish_url(get_gallery_home_url().OSM_PATH),
            'GALLERY_TITLE'		=> $linkname .' - '. $conf['gallery_title'],
            'HOME'              => make_index_url(),
            'HOME_PREV'         => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : get_absolute_root_url(),
            'HOME_NAME'         => l10n("Home"),
            'HOME_PREV_NAME'    => l10n("Previous"),
            'TOTAL'             => sprintf( l10n('ITEMS'), count($js_data) ),
            'OSMJS'				=> $js,
            'MYROOT_URL'		=> get_absolute_root_url(),
            'default_baselayer' => $conf['osm_conf']['map']['baselayer'],
        )
    );

    if ( $conf['osm_conf']['map']['baselayer'] == 'custom' ) {
        $iconbaselayer = $conf['osm_conf']['map']['custombaselayerurl'];
        $iconbaselayer = str_replace('{s}', 'a', $iconbaselayer);
        $iconbaselayer = str_replace('{z}', '5', $iconbaselayer);
        $iconbaselayer = str_replace('{x}', '15', $iconbaselayer);
        $iconbaselayer = str_replace('{y}', '11', $iconbaselayer);
        $template->assign(
            array(
                'custombaselayer'    => $conf['osm_conf']['map']['custombaselayer'],
                'custombaselayerurl' => $conf['osm_conf']['map']['custombaselayerurl'],
                'iconbaselayer'      => $iconbaselayer,
            )
        );
    }

    $template->pparse('map');
    $template->p();
}

function osm_parse_map_data_url($tokens, &$next_token)
{
    $page = parse_section_url($tokens, $next_token);
    if ( !isset($page['section']) )
      $page['section'] = 'categories';

    $page = array_merge( $page, parse_well_known_params_url( $tokens, $next_token) );
    $page['start']=0;
    $page['box'] = osm_bounds_from_url( @$_GET['box'] );
    return $page;
}

function osm_bounds_from_url($str)
{
  if ( !isset($str) or strlen($str)==0 )
    return null;
  $r = explode(',', $str );
  if ( count($r) != 4)
    bad_request( $str.' is not a valid geographical bound' );
  $b = array(
    's' => $r[0],
    'w' => $r[1],
    'n' => $r[2],
    'e' => $r[3],
  );
  return $b;
}

/**
 * What is the id of this page tag ?
 *
 * Note : this function is called to grab every tags in a page
 *
 * @param array basket to collect id
 * @param array page tag 
 * @return basket
 */
function osm_get_page_tag_id($basket, $page_tag) {
	$basket[] = $page_tag['id']; 
	return $basket;
}

?>
