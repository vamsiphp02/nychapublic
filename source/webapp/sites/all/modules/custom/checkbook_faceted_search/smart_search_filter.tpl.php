<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @file
 * Template file to output the "Narrow Down Your Search" sidebar for Smart Search
 *
 * Available variables:
 * $facets
 * $active_contracts
 * $theme_hook_suggestions
 * $zebra
 * $id
 * $directory
 * $classes_array
 * $attributes_array
 * $title_attributes_array
 * $content_attributes_array
 * $title_prefix
 * $title_suffix
 * $user
 * $db_is_active
 * $is_admin
 * $logged_in
 * $is_front
 * $render_array
 * $facets_render
 */
?>

<div class="narrow-down-filter">
  <div class="narrow-down-title">Narrow Down Your Search:</div>
<?php
foreach ($facets_render??[] as $facet_name => $facet) {

    // skipping children (sub facets)
    if ($facet->child??false){
      continue;
    }

    $selected_facet_results['contract_status'] = is_array($selected_facet_results['contract_status']) ? $selected_facet_results['contract_status'] : [];
    if(in_array('registered', $selected_facet_results['contract_status']) && strtolower($facet_name) == 'facet_year_array'){
      continue;
    }
    if(!in_array('registered', $selected_facet_results['contract_status']) && strtolower($facet_name) == 'registered_fiscal_year'){
      continue;
    }

    // Donot display future year in the fiscal year facet
    $current_year = CheckbookDateUtil::getMaxDatasourceFiscalYear(DataSource::smartSearchDataSource());;
    if(strtolower($facet_name) == 'facet_year_array'){
      foreach($facet->results as $fvalue => $fcount) {
        if ($fvalue > $current_year) {
          unset($facet->results[$fvalue]);
        }
      }
    }

    $span='';
    $display_facet = 'none';

    // keep domain facet always open
    if ($facet->selected || in_array($facet_name,['domain'])) {
      $span = 'open';
      $display_facet = 'block';
      if($solr_datasource == Datasource::SOLR_NYCHA){
        foreach($facet->results as $facet_value => $count){
          if(strtolower($facet_value) == 'budget' || strtolower($facet_value) == 'revenue'){
            unset($facet->results[$facet_value]);
          }
        }
      }
    }

    echo '<div class="filter-content-' . $facet_name . ' filter-content">';
    echo '  <div class="filter-title"><span class="'.$span.'">By ' . htmlentities($facet->title) . '</span></div>';
    echo '    <div class="facet-content" style="display:'.$display_facet.'" ><div class="progress"></div>';

    if ($facet->autocomplete && sizeof($facet->results)>9) {
      $autocomplete_id = "autocomplete_" . $facet_name;
      $disabled = '';

      // Autocomplete's result(s) displays and allows to select options that are already selected
      // thereby counting an option twice. Hence, removing duplicates
      $facet->selected  = array_unique($facet->selected? $facet->selected:[]);

      //NYCCHKBK-9957 : Disable autocomplete search box if 5 or more options are selected
      $no_of_selected_options = count($facet->selected ? $facet->selected: []);
      if($no_of_selected_options >= 5) $disabled = " DISABLED=true";

      echo '<div class="autocomplete">
              <input id="' . $autocomplete_id . '" ' . $disabled . ' type="text" class="solr_autocomplete" facet="'.$facet_name.'" />
            </div>';
    }

    echo '<div class="options">';
    echo '<ul class="rows">';
    $index = 0;
    $lowercase_selected = [];
    if($facet->selected){
      foreach($facet->selected as $facet_val){
        $lowercase_selected[strtolower($facet_val)] = strtolower($facet_val);
      }
    }

    foreach($facet->results as $facet_value => $count) {

      $facet_result_title = $facet_value;
      if (is_array($count)) {
        list($facet_result_title, $count) = $count;
      }

      $id = 'facet_'.$facet_name.$index;
      $active='';
      echo <<<END

    <li class="row">
      <label for="{$id}">
        <div class="checkbox">

END;

      if ($lowercase_selected && isset($lowercase_selected[strtolower($facet_value)])) {
        $checked = ' checked="checked" ';
        $active = ' class="active" ';
        $disabled = '';
      }
      else{
        $checked = '';
        $active = '';
        $disabled = '';
      }

      //Disable unchecked options if 5 or more options from the same category are already selected
      if((!$checked || $checked == '')  && (count($lowercase_selected) >= 5)){
        $disabled = " DISABLED=true";
      }

      echo '<input type="checkbox" onclick="return applySearchFilters();" id="'.$id.'" '.$checked . $disabled.' facet="'.$facet_name.'" value="'.
        htmlentities(urlencode($facet_value)).'" />';
      echo <<<END

      <label for="{$id}" />
    </div>

END;

      echo '<div class="number"><span' . $active . '>' . number_format($count) . '</span></div>';
      echo '<div class="name">' . htmlentities($facet_result_title) . '</div>';
      echo '</label>';
      echo '</li>';

      if (($checked) && ($children = $facet->sub->$facet_value??false)){
        $sub_index=0;
        foreach($children as $child){
          $sub_facet = $facets_render[$child];
          if (!$sub_facet) {
            continue;
          }

          $sub_facet_name = $child;
          echo '<ul class="sub-category">';
          echo '<div class="subcat-filter-title">By '.htmlentities($sub_facet->title).'</div>';
          //Set Active and Registered Contracts Counts
          if($sub_facet_name == 'contract_status'){
            unset($sub_facet->results['registered']);
            unset($sub_facet->results['active']);
            if($registered_contracts > 0 ) {
              $sub_facet->results['registered'] = $registered_contracts;
            }
            if($active_contracts > 0) {
              $sub_facet->results['active'] = $active_contracts;
            }
          }

          foreach($sub_facet->results as $sub_facet_value => $sub_count){

            $facet_result_title = $sub_facet_value;
            if (is_array($sub_count)) {
              list($facet_result_title, $sub_count) = $sub_count;
            }

            $id = 'facet_'.$sub_facet_name.$sub_index;
            $active='';
            echo '<li class="row">';
            echo "<label for=\"{$id}\">";
            echo '<div class="checkbox">';
            $checked = '';
            if ($sub_facet->selected) {
              $checked = in_array($sub_facet_value, $sub_facet->selected);
            }

            $checked = $checked ? ' checked="checked" ' : '';
            $active = $checked ? ' class="active" ' : '';

            if(isset($sub_facet->input) && $sub_facet->input == 'radio'){
              echo '<input type="radio" name="' . htmlentities($sub_facet->title) . '" ' . 'id="' . $id . '" ' . $checked . ' facet="' . $sub_facet_name . '" value="' .
              htmlentities(urlencode($sub_facet_value)) . '" />';
            }else{
              echo '<input type="checkbox" onclick="return applySearchFilters();" id="' . $id . '" ' . $checked . ' facet="' . $sub_facet_name . '" value="' .
                htmlentities(urlencode($sub_facet_value)) . '" />';
            }
            echo "<label for=\"{$id}\" />";
            echo '</div>';

            echo '<div class="number"><span' . $active . '>' . number_format($sub_count) . '</span></div>';
            echo '<div class="name">' . htmlentities($facet_result_title) . '</div>';
            echo '</label>';

            echo '</li>';
            $sub_index++;
          }
          echo '</ul>';
        }
      }

      $index++;
    }

    echo '</ul></div>';

    echo '</div></div>';
}
?>
</div>
