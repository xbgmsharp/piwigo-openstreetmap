{html_head}
<link rel="stylesheet" href="{$OSM_PATH}fontello/css/osm.css" />
{/html_head}

{html_style}
.showInfo { text-indent:5px; }
{/html_style}

Create place to allow reuse of location.
<br/><br/>
Refer to the <a href="https://github.com/xbgmsharp/piwigo-openstreetmap/wiki" target="_blanck">plugin documentation</a> for additional information. Create an <a href="https://github.com/xbgmsharp/piwigo-openstreetmap/issues" target="_blanck">issue</a> for support, or feedback, or feature request.

{footer_script require='jquery'}
jQuery('.showInfo').tipTip({
  'delay' : 0,
  'fadeIn' : 200,
  'fadeOut' : 200,
  'maxWidth':'300px',
  'keepAlive':true,
  'activation':'click'
});

function displayDeletionWarnings() {
  jQuery(".warningDeletion").show();
  jQuery("input[name=destination_tag]:checked").parent("label").children(".warningDeletion").hide();
}

displayDeletionWarnings();

jQuery("#mergeTags label").click(function() {
  displayDeletionWarnings();
});

jQuery("input[name=merge]").click(function() {
  if (jQuery("ul.tagSelection input[type=checkbox]:checked").length < 2) {
    alert("{'Select at least two places for merging'|@translate}");
    return false;
  }
});

$("#searchInput").on("keydown", function(e) {
  var $this = $(this),
      timer = $this.data("timer");

  if (timer) {
    clearTimeout(timer);
  }

  $this.data("timer", setTimeout(function() {
    var val = $this.val();
    if (!val) {
      $(".tagSelection>li").show();
      $("#filterIcon").css("visibility","hidden");
    }
    else {
      $("#filterIcon").css("visibility","visible");
      var regex = new RegExp( val.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"), "i" );
      $(".tagSelection>li").each(function() {
        var $li = $(this),
            text = $.trim( $("label", $li).text() );
        $li.toggle(regex.test(text));
      });
    }

  }, 300) );

  if (e.keyCode == 13) { // Enter
    e.preventDefault();
  }
});
{/footer_script}

<form action="" method="post" id="update">
  {if isset($EDIT_PLACES_LIST)}
  <fieldset>
    <legend>{'EDIT_PLACES_LEGEND'|@translate}</legend>
    <input type="hidden" name="edit_list" value="{$EDIT_PLACES_LIST}">
    <table class="table2">
      <tr class="throw">
        <th>{'EDIT_PLACE_CURRENT'|@translate}</th>
        <th>{'EDIT_PLACE_NEW'|@translate}</th>
      </tr>
      {foreach from=$places item=place}
      <tr>
        <td style="border-top: 1px dashed #005E89;">{$place.NAME}</td>
        <td style="border-top: 1px dashed #005E89;"><input type="text" name="place_name-{$place.ID}" value="{$place.NAME}" size="50"></td>
      <tr>
      </tr>
        <td>{$place.LATITUDE}</td>
        <td><input type="text" name="place_lat-{$place.ID}" value="{$place.LATITUDE}" size="40"></td>
      <tr>
      </tr>
        <td>{$place.LONGITUDE}</td>
        <td><input type="text" name="place_lon-{$place.ID}" value="{$place.LONGITUDE}" size="40"></td>
      </tr>
      {/foreach}
    </table>

    <p>
      <input type="submit" name="edit_submit" value="{'Submit'|@translate}">
      <input type="submit" name="edit_cancel" value="{'Cancel'|@translate}">
    </p>
  </fieldset>
  {/if}

  <fieldset>
    <legend>{'OSM_ADD_PLACE'|@translate}</legend>
    <ul>
	<li>
		<label>{'OSM_NEW_PLACE'|@translate} : </label>
		<input type="text" name="add_place" size="50" require="" placeholder="Home">
	</li>
	<li>
		<label>{'LATITUDE'|@translate} : </label>
		<input type="text" name="add_lat" size="40" require="" placeholder="48.858">
	</li>
	<li>
		<label>{'LONGITUDE'|@translate} : </label>
		<input type="text" name="add_lon" size="40" require="" placeholder="2.2942">
	</li>
    </ul>

    <p><input class="submit" type="submit" name="add" value="{'Submit'|@translate}"></p>
  </fieldset>

  <fieldset>
    <legend>{'PLACE_SELECTION'|@translate}</legend>

    {if count($all_places)}
    <div><label><span class="icon-filter" style="visibility:hidden" id="filterIcon"></span>{'Search'|@translate}: <input id="searchInput" type="text" size="12"></label></div>
    {/if}

    <ul class="tagSelection">
    {foreach from=$all_places item=place}
      <li>
        {capture name='showInfo'}{strip}
          <b>{$place.name}</b><br>
        {/strip}{/capture}
        <a class="icon-info-circled-1 showInfo" title="{$smarty.capture.showInfo|@htmlspecialchars}"></a>
        <label>
          <input type="checkbox" name="places[]" value="{$place.id}"> {$place.name}
        </label>
      </li>
    {/foreach}
    </ul>

    <p>
      <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
      <input type="submit" name="edit" value="{'EDIT_PLACES'|@translate}">
      <input type="submit" name="delete" value="{'DELETE_PLACES'|@translate}" onclick="return confirm('{'Are you sure?'|@translate}');">
    </p>
  </fieldset>

</form>
