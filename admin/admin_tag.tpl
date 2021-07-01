{html_head}
<link rel="stylesheet" href="{$OSM_PATH}fontello/css/osm.css" />
<style>
  {literal}
    .osm_layout {
      text-align: left;
      border: 2px solid rgb(221, 221, 221);
      padding: 1em;
      margin: 1em;
    }
  {/literal}
</style>
{/html_head}

Create tags from the address base on the GPS (latitude, longitude) metadata information.
<br/><br/>
Refer to the <a href="https://github.com/xbgmsharp/piwigo-openstreetmap/wiki" target="_blanck">plugin documentation</a> for additional information. Create an <a href="https://github.com/xbgmsharp/piwigo-openstreetmap/issues" target="_blanck">issue</a> for support, or feedback, or feature request.

<div class="osm_layout">
  <legend>{'Statistics'|@translate}</legend>
  <ul>
    <li class="update_summary_new">{$NB_GEOTAGGED} geotagged items in your gallery</li>
  </ul>
</div>

{if isset($metadata_result)}
<div class="osm_layout">
  <legend>Synchronization results</legend>
  <ul>
	<li>{$metadata_result.NB_ELEMENTS_DONE} {'tags updated in the database'|@translate}</li>
	<li>{$metadata_result.NB_ELEMENTS_CANDIDATES} {'photos candidates for tags synchronization'|@translate}</li>
	<li>{$metadata_result.NB_WARNINGS} {'warnings during synchronization'|@translate}</li>
	<li>{$metadata_result.NB_ERRORS} {'errors during synchronization'|@translate}</li>
  </ul>

{if not empty($sync_errors)}
  <h3>{'Error list'|@translate}</h3>
  <div class="errors">
    <ul>
      {foreach from=$sync_errors item=error}
      <li>{$error}</li>
      {/foreach}
    </ul>
  </div>
{/if}

{if not empty($sync_warnings)}
  <h3>{'Warnings'|@translate}</h3>
  <div class="warnings">
    <ul>
      {foreach from=$sync_warnings item=warning}
      <li>{$warning}</li>
      {/foreach}
    </ul>
  </div>
{/if}

{if not empty($sync_infos)}
  <h3>{'Detailed informations'|@translate}</h3>
  <div class="infos">
    <ul>
      {foreach from=$sync_infos item=info}
      <li>{$info}</li>
      {/foreach}
    </ul>
  </div>
{/if}

</div>
{/if}

<form action="" method="post" id="update">

  <fieldset id="tags">
	<legend>{'Manage tags'|@translate}</legend>
	<ul>
		<li>
			<label>
				{'NAME_OF_TAG_GROUP'|@translate}:
				<input type="text" name="osm_taggroup" value="{$sync_options.osm_tag_group}" placeholder="location" required="" size="30"/>
			</label>
		</li>
		<li>
			<label>{'USE_AS_TAG'|@translate} : </label><br/>
			<div style="padding-left: 25px">
				<input type="checkbox" name="osm_tag_address_suburb" value="true" {if $sync_options.osm_tag_address_suburb}checked="checked"{/if}/> {'SUBURB'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_city_district" value="true" {if $sync_options.osm_tag_address_city_district}checked="checked"{/if}/> {'CITY_DISTRICT'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_city" value="true" {if $sync_options.osm_tag_address_city}checked="checked"{/if}/> {'CITY'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_county" value="true" {if $sync_options.osm_tag_address_county}checked="checked"{/if}/> {'COUNTY'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_state" value="true" {if $sync_options.osm_tag_address_state}checked="checked"{/if}/> {'STATE'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_country" value="true" {if $sync_options.osm_tag_address_country}checked="checked"{/if}/> {'COUNTRY'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_postcode" value="true" {if $sync_options.osm_tag_address_postcode}checked="checked"{/if}/> {'POSTCODE'|@translate}<br />
				<input type="checkbox" name="osm_tag_address_country_code" value="true" {if $sync_options.osm_tag_address_country_code}checked="checked"{/if}/> {'COUNTRY_CODE'|@translate}<br />
			</div>
			<small>{'USE_AS_TAG_HELP'|@translate}</small>
		</li>

		<li>
			<label>{'Language'|@translate} :</label><br/>
			<div id="action_language" class="bulkAction">
			      <select name="language" size="1">
			        {html_options options=$language_options selected=$language_selected}
			      </select>
			</div>
			<small>{'LANGUAGE_HELP'|@translate}</small>
		</li>
	</ul>

   </fieldset>

  <fieldset id="syncSimulation">
    <legend>{'Simulation'|@translate}</legend>
    <ul>
      <li><label><input type="checkbox" name="simulate" value="1" checked="checked" /> {'only perform a simulation (no change in database will be made)'|@translate}</label></li>
    </ul>
  </fieldset>

  <fieldset id="catSubset">
    <legend>{'reduce to single existing albums'|@translate}</legend>
    <ul>
    <li>
    <select class="categoryList" name="cat_id" size="10">
    	{html_options options=$categories selected=$categories_selected}
    </select>
    </li>

    <li><label><input type="checkbox" name="subcats_included" value="1" {$SUBCATS_INCLUDED_CHECKED} /> {'Search in sub-albums'|@translate}</label></li>
    </ul>
  </fieldset>

  <p>
    <input type="submit" value="{'Submit'|@translate}" name="osm_tag_submit">
  </p>
</form>
