{html_head}
<script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.13/d3.min.js" charset="utf-8"></script>
<link href="{$OSM_PATH}leaflet/leaflet.css" rel="stylesheet">
<script src="{$OSM_PATH}leaflet/leaflet.js"></script>
<script src="{$OSM_PATH}leaflet/Leaflet.Elevation-0.0.2.min.js"></script>
<link rel="stylesheet" href="{$OSM_PATH}leaflet/Leaflet.Elevation-0.0.2.css" />
<script src="{$OSM_PATH}leaflet/kml.js"></script>
{/html_head}

{html_style}
{literal}
#mapgpx {
 height: {/literal}{$HEIGHT}{literal}px;
 width: 90%;
 max-width: 1280px;
 margin: 0px auto;
}
{/literal}
{/html_style}

<div id="mapgpx"></div>
<script type="text/javascript">{$OSMGPX}
{literal}
/*
		var map = new L.Map('mapgpx');

		var url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			attr ='Leaflet Plugin by xbgmsharp Tiles Courtesy of OSM.org (CC BY-SA) © OpenStreetMap contributors, (ODbL)',
			service = new L.TileLayer(url, {attribution: attr});
*/
		var el = L.control.elevation({theme: 'steelblue-theme', width: {/literal}{$WIDTH}{literal}});
		el.addTo(mapgpx);
		var g=new L.KML("{/literal}{$FILENAME}{literal}", {
			async: true
		});
		g.on('loaded', function(e) {
			mapgpx.fitBounds(e.target.getBounds());
		});
		g.on("addline",function(e){
			el.addData(e.line);
		});
		g.addTo(mapgpx);
		//mapgpx.addLayer(service);
{/literal}
</script>
