<!DOCTYPE html>
<html>{html_head}
<meta http-equiv="content-type" content="text/html; charset={$CONTENT_ENCODING}" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta name="robots" content="noindex,nofollow" />
<title>{$GALLERY_TITLE}</title>
<link rel="stylesheet" href="{$OSM_PATH}fontello/css/osm.css" />
<link rel="stylesheet" href="{$OSM_PATH}leaflet/leaflet.css" />
<link rel="stylesheet" href="{$OSM_PATH}leaflet/leaflet-search.min.css" />
<link rel="stylesheet" href="{$OSM_PATH}leaflet/MarkerCluster.css" />
<link rel="stylesheet" href="{$OSM_PATH}leaflet/MarkerCluster.Default.css" />
<link rel="stylesheet" href="{$OSM_PATH}leaflet/leaflet.contextmenu.css" />
<script src="{$OSM_PATH}leaflet/leaflet.js"></script>
<script src="{$OSM_PATH}leaflet/leaflet-search.min.js"></script>
<script src="{$OSM_PATH}leaflet/leaflet.markercluster.js"></script>
<script src="{$OSM_PATH}leaflet/leaflet.contextmenu.js"></script>
<script src="{$OSM_PATH}leaflet/leaflet-omnivore.min.js"></script>
{html_style}
{literal}
html, body {
	height: 100%;
	width: 100%;
	margin: 0;
	padding: 0;
}

#map {
	position: absolute;
	top:0;
	left:0;
	right:0;
	bottom:0;
}

#content {
	position: absolute;
	bottom: 110px;
	left:0;
	right:0;
	height: 60px;
	z-index: 10;
	background-color: rgba(0,0,0,0.5);
}

#ribbon-map {
	bottom: 50px;
	color: rgb(0, 0, 0);
	display: block;
	font-family: Arial,Helvetica,sans-serif;
	background-attachment: scroll;
	background-clip: border-box;
	background-color: rgb(255, 255, 255);
	background-image: none;
	background-origin: padding-box;
	background-position: 0% 0%;
	background-repeat: repeat;
	background-size: auto auto;
	opacity: 0.65;
	position: absolute;
	box-shadow: 0px 2px 0px rgba(0, 0, 0, 0.1);
	z-index: 100;
	padding-left:50px;
	//width: 50%;
	white-space: nowrap;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-box-shadow: 0 0 2px #999;
	-moz-box-shadow: 0 0 2px #999;
	box-shadow: 0 0 2px #999;
}
#ribbon-map a:link {text-decoration: none; color: blue; text-shadow: none; transition: text-shadow 0.5s ease 0s;}
#ribbon-map a:visited {text-decoration: none; color: blue; text-shadow: none; transition: text-shadow 0.5s ease 0s;}
#ribbon-map a:active {text-decoration: none; color: blue; text-shadow: none; transition: text-shadow 0.5s ease 0s;}
#ribbon-map a:hover {text-decoration: none; text-shadow: #0090ff 0px 0px 2px;}

#ribbon-map-padding {
	display: inline-block;
	vertical-align: top;
	line-height: 30px;
	text-align: center;
	text-decoration: none;
	cursor: pointer;
	color: black;
	font-size: 500%;
	padding-top:5px;
}

#ribbon-map-toggle {
	display: inline-block;
	vertical-align: top;
	line-height: 30px;
	text-align: center;
	text-decoration: none;
}

#ribbon-map-nav {
	display: inline-block;
	vertical-align: top;
	line-height: 30px;
	margin-right: 0.75em;
	text-align: center;
	text-decoration: none;
}

#ribbon-map-results {
	display: inline-block;
	vertical-align: top;
	line-height: 30px;
	margin-right: 0.75em;
	text-align: center;
	text-decoration: none;
}

#ribbon-map-location {
	display: inline-block;
	vertical-align: top;
	line-height: 30px;
	margin-right: 0.75em;
	text-align: center;
	text-decoration: none;
}

#ribbon-map-wrapper {
	display: inline-block;
	vertical-align: top;
	height: 50px;
	padding-left: 40px;
	text-align: center;
	text-decoration: none;
}

#ribbon-map-wrapper a:link {text-decoration: none; color: white;}
#ribbon-map-wrapper a:visited {text-decoration: none; color: white;}
#ribbon-map-wrapper a:active {text-decoration: none; color: white;}
#ribbon-map-wrapper a:hover {text-decoration: none; color: white;}

#jcarousel-img {
	cursor: pointer;
}
/*PIC*/
.pic {
  height: 40px;
  width: 40px;
  overflow: hidden;
  margin: 0px;
  border: 1px solid white;

  -webkit-box-shadow: 5px 5px 5px #111;
  box-shadow: 5px 5px 5px #111;
  float: left;
}

.pic:hover {
  cursor: pointer;
}
/*MORPH*/
.morph {
  -webkit-transition: all 0.5s ease;
     -moz-transition: all 0.5s ease;
       -o-transition: all 0.5s ease;
      -ms-transition: all 0.5s ease;
          transition: all 0.5s ease;
}

.morph:hover {
  border-radius: 50%;
  -webkit-transform: rotate(360deg);
     -moz-transform: rotate(360deg);
       -o-transform: rotate(360deg);
      -ms-transform: rotate(360deg);
          transform: rotate(360deg);
}

a.tooltips {
	position: relative;
	display: inline;
}
a.tooltips span {
	position: absolute;
	padding: 5px;
	color: #FFFFFF;
	background: rgba(0,0,0,0.8);
	height: 30px;
	line-height: 30px;
	text-align: center;
	visibility: hidden;
	border-radius: 6px;
}
a.tooltips span:after {
	content: '';
	position: absolute;
	top: 100%;
	left: 50%;
	margin-left: -8px;
	width: 0;
	height: 0;
	border-top: 8px solid #000000;
	border-right: 8px solid transparent;
	border-left: 8px solid transparent;
}
a:hover.tooltips span {
	visibility: visible;
	opacity: 0.8;
	bottom: 30px;
	left: 50%;
	margin-left: -76px;
	z-index: 999;
}

#dialog {
	font-family: Arial,Helvetica,sans-serif;
	font-size: 10px;
	text-align: center;
	text-decoration: none;
}
{/literal}{/html_style}{/html_head}
</head>
<body>
<noscript>{'BROWSER_JAVASCRIPT'|@translate}</noscript> 

<div id="map"></div>
<!-- <div id="content"></div> -->

<div id="ribbon-map">
<!--
	<div id="ribbon-map-padding" onclick="toggle(this)">
		<span class="ToolTip">&lsaquo;</span> 
	</div>
-->

	<div id="ribbon-map-toggle" class="show">
		<div id="ribbon-map-nav">
			<span class="osm-home"></span><a href="{$HOME}" class="tooltips">{$HOME_NAME}<span>{'PIWIGO_GALLERY'|@translate}</span></a><br/>
			<span class="osm-left-big"></span><a href="{$HOME_PREV}" class="tooltips">{$HOME_PREV_NAME}<span>{'BACK_ON_PAGE'|@translate}</span></a>
		</div>

		<div id="ribbon-map-results">
			<b id="nb_showall" style="color: rgb(204, 0, 0);">{$TOTAL}</b><br/>
			<span class="osm-link-ext"></span><a id="map-showall" target="_blank" href="" class="tooltips">{'SHOW_ALL'|@translate}<span>{'SHOW_ALL_PIWIGO'|@translate}</span></a>
		</div>

		<div id="ribbon-map-location">
			<span class="osm-link"></span><a href="#" onclick="linkToThisMap();" id="opener" class="tooltips">{'LINK_MAP'|@translate}<span>{'SHARE'|@translate}</span></a><br/>
			<span class="osm-direction"></span><a href="#" onclick="findMyLocation();" class="tooltips">{'FIND_POSITION'|@translate}<span>{'SEARCH_MY_POSITION'|@translate}</span></a>
		</div>

		<div id="ribbon-map-wrapper" style="visibility:hidden; max-width: 0px;">
			<div class="jcarousel-wrapper" id="jcarousel-wrapper" style="max-width: 0px;">
				<div class="jcarousel">
					<ul id="jcarousel-thumb">
						<!-- <li><img class="morph pic" src="http://placehold.it/40x40&text=FooBar1"><span>FooBar1</span></a></li> -->
						<!-- <li><img src="http://placehold.it/40x40&text=FooBar2" alt="FooBar2"></li>  -->
						<!-- <li><img class="morph pic" src="http://placehold.it/40x40&text=FooBar3"></li> -->
						<!-- <li class="tooltips"><span>Tooltip</span><img class="morph pic" src="http://placehold.it/40x40&text=FooBar3"></li> -->
					</ul>
				</div>

				<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
				<a href="#" class="jcarousel-control-next">&rsaquo;</a>

			</div> <!-- jcarousel-wrapper -->
		</div> <!-- ribbon-map-wrapper -->
	</div> <!-- ribbon-map-toggle -->
</div> <!-- ribbon-map -->

<div id="dialog" title="{'LINK_MAP'|@translate}">
	<p>{'COPY_PASTE_URL'|@translate}</p>
	<input type="text" value="" style="width: 550px;" onfocus="this.select();" id="textfield"></input>
</div>

<script type="text/javascript">
function toggle(arrow)
{
	var el = document.getElementById("ribbon-map-toggle");
	var box = el.getAttribute("class");
	if(box == "hide"){
		el.setAttribute("class", "show");
		$(arrow).children(".ToolTip").html("&lsaquo;");
		el.removeAttribute("style");
	}
	else{
		el.setAttribute("class", "hide");
		el.setAttribute("style","visibility:hidden;width:0px;");
		$(arrow).children(".ToolTip").html("&rsaquo;");
	}
}
</script>

<script type="text/javascript">{$OSMJS}</script>

<script type="text/javascript">
{literal}
/*
	// Set the bounding area for the map
	MAP_FIT_PADDING = 0.25;
	MAP_MAX_PADDING = 6;
	map.fitBounds(markers.getBounds().pad(MAP_FIT_PADDING), { paddingBottomRight: [0, 110] });
	map.setMaxBounds(markers.getBounds().pad(MAP_MAX_PADDING));
*/

	/* BEGIN leaflet-search https://github.com/stefanocudini/leaflet-search */
	var jsonpurl = 'https://open.mapquestapi.com/nominatim/v1/search.php?q={s}'+
				   '&format=json&osm_type=N&limit=100&addressdetails=0',
		jsonpName = 'json_callback';
	//third party jsonp service

	function filterJSONCall(rawjson) {	//callback that remap fields name
		var json = {},
			key, loc, disp = [];

		for(var i in rawjson)
		{
			disp = rawjson[i].display_name.split(',');
			key = disp[0] +', '+ disp[1];
			loc = L.latLng( rawjson[i].lat, rawjson[i].lon );
			json[ key ]= loc;	//key,value format
		}

		return json;
	}

	var searchOpts = {
			url: jsonpurl,
			jsonpParam: jsonpName,
			filterJSON: filterJSONCall,
			animateLocation: true,
			circleLocation: false,
			markerLocation: false,
			zoom: 12,
			minLength: 3,
			autoType: false,
		};

	map.addControl( new L.Control.Search(searchOpts) );
	/* END leaflet-search */

	/* https://github.com/codeforamerica/lv-trucks-map/blob/master/js/main.js */
	/* http://clvfoodtrucks.com/ */
	L.Map.prototype.panToOffset = function (latlng, offset, options) {
	  var x = this.latLngToContainerPoint(latlng).x - offset[0]
	  var y = this.latLngToContainerPoint(latlng).y - offset[1]
	  var point = this.containerPointToLatLng([x, y])
	  return this.setView(point, this._zoom, { pan: options })
	}

	/* BEGIN leaflet-locatecontrol https://github.com/domoritz/leaflet-locatecontrol */
	/* END leaflet-locatecontrol */

	/* BEGIN leaflet-contextmenu https://github.com/aratcliffe/Leaflet.contextmenu */
	function goHome (){
		window.location.assign('{/literal}{$HOME}{literal}');
	}

	function goBack (){
		window.location.assign('{/literal}{$HOME_PREV}{literal}');
	}

	function showCoordinates (e) {
		var popup = L.popup();
		popup
			.setLatLng(e.latlng)
			.setContent("{/literal}{'CLICKED_MAP'|@translate}{literal}" + e.latlng.toString())
			.openOn(map);
	}

	function centerMap (e) {
		map.panTo(e.latlng);
		getMarkers(); /* Center on Map is not consider as Move so we have to update the data ourself */
	}

	function goShowAll (e) {
		/* Get coordonates */
		var bounds = map.getBounds();
		var min = bounds.getSouthWest().wrap();
		var max = bounds.getNorthEast().wrap();

		/* Update ShowAll link */
		var root_url = '{/literal}{$MYROOT_URL}{literal}';
		var myurl = root_url+"osmmap.php?min_lat="+min.lat+"&min_lng="+min.lng+"&max_lat="+max.lat+"&max_lng="+max.lng;
		//console.log("ShowAll:"+myurl);
		//window.location.assign(myurl);
		window.open(myurl,'_blank');
	}

	function linkToThisMap (){
		var center = map.getCenter();
		var zoom = map.getZoom();

		var centerlat = center.lat;
		var centerlng = center.lng;

		var root_url = '{/literal}{$MYROOT_URL}{literal}';
		var myurl = root_url+"osmmap.php?zoom="+zoom+"&center_lat="+centerlat+"&center_lng="+centerlng;
		//console.log(myurl);
		document.getElementById('textfield').value = myurl;
		$('#dialog').dialog('open');
	}

	function findMyLocation (){
		/* http://leafletjs.com/examples/mobile-example.html */
		/* http://www.bennadel.com/blog/2023-Geocoding-A-User-s-Location-Using-Javascript-s-GeoLocation-API.htm */
		map.locate({setView: true, maxZoom: 16});
	}

	function zoomIn (e) {
		map.zoomIn();
	}

	function zoomOut (e) {
		map.zoomOut();
	}

	map.contextmenu.addItem({text: '{/literal}{$HOME_NAME}{literal}', iconCls: 'osm-home', callback: goHome});
	map.contextmenu.addItem({text: '{/literal}{$HOME_PREV_NAME}{literal}', iconCls: 'osm-left-big', callback: goBack});
	map.contextmenu.addItem('-');
	map.contextmenu.addItem({text: '{/literal}{'SHOW_COORD'|@translate}{literal}', iconCls: 'osm-pin', callback: showCoordinates});
	map.contextmenu.addItem({text: '{/literal}{'CENTER_MAP'|@translate}{literal}', iconCls: 'osm-location', callback: centerMap});
	map.contextmenu.addItem('-');
	map.contextmenu.addItem({text: '{/literal}{'SHOW_ALL_ITEMS'|@translate}{literal}', iconCls: 'osm-link-ext', callback: goShowAll});
	map.contextmenu.addItem({text: '{/literal}{'LINK_MAP'|@translate}{literal}', iconCls: 'osm-link', callback: linkToThisMap});
	map.contextmenu.addItem({text: '{/literal}{'FIND_POSITION'|@translate}{literal}', iconCls: 'osm-direction', callback: findMyLocation});
	map.contextmenu.addItem({separator: true});
	map.contextmenu.addItem({text: '{/literal}{'ZOOM_IN'|@translate}{literal}', iconCls: 'osm-zoom-in', callback: zoomIn});
	map.contextmenu.addItem({text: '{/literal}{'ZOOM_OUT'|@translate}{literal}', iconCls: 'osm-zoom-out', callback: zoomOut});
	/* END leaflet-locatecontrol */

	/* BEGIN piwigo-openstreetmap plugin */
	map.on('moveend', onMapMove);

	function onMapMove(e){
		getMarkers();
	}

	/* Generate the carrousel */
	function getMarkers(){
		/* Get coordonates */
		var bounds = map.getBounds();
		var min = bounds.getSouthWest().wrap();
		var max = bounds.getNorthEast().wrap();

		/* Remove any previous thumbnails */
		removeChildren({parentId:'jcarousel-thumb'});

		/* Update ShowAll link */
		var root_url = '{/literal}{$MYROOT_URL}{literal}';
		var myurl = root_url+"osmmap.php?min_lat="+min.lat+"&min_lng="+min.lng+"&max_lat="+max.lat+"&max_lng="+max.lng;
		//console.log("ShowAll:"+myurl);
		document.getElementById("map-showall").setAttribute('href',myurl);

		var nb_items = 0;
		for (var i = 0; i < addressPoints.length; i++) {
			var a = addressPoints[i];
			var pathurl = a[3];

			//console.log(a[0] +" > "+ min.lat +" && "+ a[1] +" > "+ min.lng +" && "+ a[0] +" < "+ max.lat +" && "+ a[1] +" < "+ max.lng);
			if (a[0] > min.lat && a[1] > min.lng && a[0] < max.lat && a[1] < max.lng)
			{
				//console.log("Inside bounds");
				var li_elem = document.createElement("li");
				var img_elem = document.createElement("img");
				img_elem.setAttribute("id","jcarousel-img");
				img_elem.setAttribute("width","40");
				img_elem.setAttribute("height","40");
				img_elem.setAttribute("onclick","OnThumbClick("+i+")");
				img_elem.setAttribute("class","morph pic");
				img_elem.src = pathurl;
				document.getElementById("jcarousel-thumb").appendChild(li_elem).appendChild(img_elem);
				nb_items++;
				//console.log("Inside bounds:"+i);
			}
		}
		/* Update items counts */
		document.getElementById("nb_showall").innerHTML = nb_items +' items';

		/* Update jcarousel and all witdh*/
		if (nb_items>0) {
			var nb_items_width = nb_items*55; /* img is 40px + margin 5px + ?? 10px */
			var document_width = $( document ).width();
			var nav_width = document.getElementById('ribbon-map-nav').offsetWidth;
			var results_width = document.getElementById('ribbon-map-results').offsetWidth;
			var location_width = document.getElementById('ribbon-map-location').offsetWidth;
			var wrapper_width = parseInt(nav_width) + parseInt(results_width) + parseInt(location_width) +150;
			var jcarousel_width = 0;

			//console.log("jcarousel_width:"+jcarousel_width+" document_width:"+document_width+" nb_items_width:"+nb_items_width+" nb_items:"+nb_items);
			if (parseInt(nb_items_width) <= (parseInt(document_width) - parseInt(wrapper_width)))
			{
				jcarousel_width = nb_items_width;
				document_width = parseInt(nav_width) + parseInt(results_width) + parseInt(location_width) + parseInt(nb_items_width) + 150;
			} else {
				jcarousel_width = parseInt(document_width) - (parseInt(nav_width) + parseInt(results_width) + parseInt(location_width) + 200);
				document_width = parseInt(document_width) - 50; /* padding left 50px */
			}
			//console.log("jcarousel_width:"+jcarousel_width+" document_width:"+document_width+" nb_items_width:"+nb_items_width+" nb_items:"+nb_items);

			/* Update ribbon-map width size */
			document.getElementById('ribbon-map').setAttribute('style','width:'+document_width+'px;');

			/* Update jcarousel width size */
			document.getElementById('jcarousel-wrapper').setAttribute('style','max-width:'+jcarousel_width+'px;');

			/* Make it all visible */
			document.getElementById('ribbon-map-wrapper').setAttribute('style','visibility:visible;');
			$('.jcarousel').jcarousel('reload');
		} else {
			document.getElementById('ribbon-map-wrapper').setAttribute('style','visibility:hidden;');
			document.getElementById('jcarousel-wrapper').setAttribute('style','max-width:0px;'); /* Should be 0px in production */
			document.getElementById('ribbon-map').removeAttribute('style');
		}
	}

	function OnThumbClick(Id){
		/* http://leaflet.github.io/Leaflet.markercluster/example/marker-clustering-zoomtoshowlayer.html */
		//console.log("Open:"+Id+" "+MarkerClusterList[Id].getLatLng());
		markers.zoomToShowLayer(MarkerClusterList[Id], function () {
				map.panTo(MarkerClusterList[Id].getLatLng());
				MarkerClusterList[Id].togglePopup();
		});
	}

	function removeChildren (params){
		var parentId = params.parentId;

		var childNodes = document.getElementById(parentId).childNodes;
		for(var i=childNodes.length-1;i >= 0;i--){
			var childNode = childNodes[i];
			childNode.parentNode.removeChild(childNode);
		}
	}

	/* BEGIN leaflet Location */
	function onLocationFound(e) {
		var radius = e.accuracy / 2;
		var str = '{/literal}{'METERS_FROM_POINT'|@translate}{literal}';
		L.marker(e.latlng).addTo(map)
			.bindPopup(str.replace('%s', radius)).openPopup();

		L.circle(e.latlng, radius).addTo(map);
	}

	function onLocationError(e) {
		alert(e.message);
	}

	map.on('locationfound', onLocationFound);
	map.on('locationerror', onLocationError);

{/literal}
</script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="{$OSM_PATH}leaflet/jcarousel.responsive.css" />
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="{$OSM_PATH}leaflet/jquery.jcarousel.min.js"></script>
<script>
{literal}

	/* Init Jquery */
	(function($) {
		$(function() {
			var jcarousel = $('.jcarousel');

			jcarousel
				.on('jcarousel:reload jcarousel:create', function () {
					var width = jcarousel.innerWidth();

					if (width >= 600) {
						width = width / 3;
					} else if (width >= 350) {
						width = width / 2;
					}

					jcarousel.jcarousel('items').css('width', width + 'px');
				})
				.jcarousel({
					wrap: 'circular'
				});

			$('.jcarousel-control-prev')
				.jcarouselControl({
					target: '-=1'
				});

			$('.jcarousel-control-next')
				.jcarouselControl({
					target: '+=1'
				});
		});

		$('#dialog').dialog({autoOpen: false, minHeight: 150, minWidth: 600});

		$('#opener').click(function() {
			$('#dialog').dialog('open');
		});

	})(jQuery);

{/literal}
</script>

</body>
</html>
