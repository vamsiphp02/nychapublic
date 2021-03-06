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

/*
 * Redirects to the search results page upon submitting the search form
 *
*/

/** Submit handler for smart search
 * @param $form
 * @param $form_state
 */
function _checkbook_smart_search_submit($form, &$form_state){
  $solr_datasource = _checkbook_get_datasource();
  $search_term = trim($form['search_box']['#value']);
  if ($search_term == 'Type ahead search') {
    $search_term = '';
  }
  $search_term = ($search_term) ? $search_term : '';

  $form_state['redirect'] = array(
    'smart_search/' . $solr_datasource,
    array(
      'query' => array(
        'search_term' => urlencode($search_term)
      )
    )
  );
}

/**
 * @param string $solr_datasource
 * @throws Exception
 */
function _checkbook_smart_search_ajax_results(string $solr_datasource = 'citywide'){
  $solr_query = new CheckbookSolrQuery($solr_datasource, $_REQUEST['search_term'], 10, $_REQUEST['page'] ?? 0);

  $solr = CheckbookSolr::getInstance($solr_datasource);
  $search_results = $solr->request_phps('select/?' . $solr_query->buildQuery());

  print theme('ajax_results', [
    'solr_datasource' => $solr_datasource,
    'search_results' => $search_results
  ]);
}

/** Returns results for smart search
 * @param string $solr_datasource
 * @return mixed|string
 * @throws Exception
 */
function _checkbook_smart_search_get_results(string $solr_datasource = 'citywide'){
  $solr_query = new CheckbookSolrQuery($solr_datasource, $_REQUEST['search_term'], 10, $_REQUEST['page'] ?? 0);

  $datasource_facets = (array)CheckbookSolr::getFacetConfigByDatasource($solr_datasource);

  $selected_facets = $solr_query->getSelectedFacets();

  foreach ($datasource_facets as $facet_name => $facet) {
    if ($facet->intervals) {
      $solr_query->addInterval($facet_name, (array)$facet->intervals);
      continue;
    }
    $solr_query->addFacet($facet_name);
    if ($facet->sub && in_array($facet_name, array_keys($selected_facets))) {
      foreach ($selected_facets[$facet_name] as $facet_value) {
        if ($domain_facets = $facet->sub->$facet_value ?? false) {
          $solr_query->addFacets($domain_facets);
        }
      }
    }
  }

  $selected_facets = $solr_query->getSelectedFacets();

  $solr = CheckbookSolr::getInstance($solr_datasource);
  $query = $solr_query->buildQuery();

  //Registered Contracts Count
  $registeredContractsQuery = getRegisteredContractsQuery($query, $selected_facets);
  if($registeredContractsQuery) {
    $registeredContracts = $solr->request_phps('select/?' . $registeredContractsQuery);
    $registeredContractsCount = $registeredContracts['response']['numFound'];
  }

  //Active Contracts Count
  $activeContractsQuery = getActiveContractsQuery($query, $selected_facets);
  if($activeContractsQuery) {
    $activeContracts = $solr->request_phps('select/?' . $activeContractsQuery);
    $activeContractsCount = $activeContracts['response']['numFound'];
  }

  //Search Results
  $search_results = $solr->request_phps('select/?' . $query);
  _inject_smart_search_drupal_settings($search_results);

  $search_results = _ckbk_ss_sort_results($search_results, $datasource_facets);

  if ($selected_facets) {
    /**
     * Get unchecked facet numbers for selected facets
     */
    $solr_query
      ->setRows(0)
      ->tagFacets($selected_facets);
    $unchecked_results = $solr->request_phps('select/?' . $solr_query->buildQuery());
    $unchecked_results = array_merge(
      $unchecked_results['facet_counts']['facet_fields'] ?? [],
      $unchecked_results['facet_counts']['facet_intervals'] ?? []
    );

    $unchecked_values = [];

    foreach (array_keys($selected_facets) as $facet) {
      if (!sizeof($unchecked_results) || !isset($unchecked_results[$facet]) || !sizeof($unchecked_results[$facet])) {
        continue;
      }
      if (isset($search_results['facet_counts']['facet_fields'][$facet])) {
        /**
         * removing checked ones
         */
        $unchecked_values = array_diff_key($unchecked_results[$facet], array_flip($selected_facets[$facet]));
        /**
         * For multivalued facets overlap happens, we need to remove unchecked from selected manually
         */
        if ($search_results['facet_counts']['facet_fields'][$facet] ?? false) {
          $search_results['facet_counts']['facet_fields'][$facet] =
            array_intersect_key($search_results['facet_counts']['facet_fields'][$facet], array_flip($selected_facets[$facet]));
        }
      } elseif (isset($search_results['facet_counts']['facet_intervals'][$facet])) {
        // facet.mincount does not work with intervals/ranges in solr
        foreach ($search_results['facet_counts']['facet_intervals'][$facet] as $k => $v) {
          if (!$v) {
            unset($search_results['facet_counts']['facet_intervals'][$facet][$k]);
          }
          $unchecked_values = array_diff_key($unchecked_results[$facet], $search_results['facet_counts']['facet_intervals'][$facet]);
        }
      }

      // for sorted facet values like years DESC
      if ($sort = $datasource_facets[$facet]->sort_by_key ?? 0) {
        if ($sort > 0) {
          ksort($unchecked_values);
        } else {
          krsort($unchecked_values);
        }
      }

      if (isset($search_results['facet_counts']['facet_fields'][$facet])) {
        $search_results['facet_counts']['facet_fields'][$facet] += ($unchecked_values ?? []);
      } elseif (isset($search_results['facet_counts']['facet_intervals'][$facet])) {
        $search_results['facet_counts']['facet_intervals'][$facet] += ($unchecked_values ?? []);
      }
    }
  }

  $search_results = _ckbk_remove_empty_intervals($search_results);

  return theme('smart_search_results', [
    'solr_datasource' => $solr_datasource,
    'search_results' => $search_results,
    'selected_facet_results' => $selected_facets,
    'registered_contracts' => $registeredContractsCount,
    'active_contracts' => $activeContractsCount
  ]);
}

/**
 * @param array $query
 * @param array $selectedFacets
 * @return array
 */
function getRegisteredContractsQuery($query, $selectedFacets){
  if(isset($selectedFacets['domain']) && in_array('contracts', $selectedFacets['domain'])){
    //Registered contracts count
    $query = str_replace('&fq=contract_status:"pending"','',$query);
    $query = str_replace('&fq=facet_year_array','&fq=registered_fiscal_year',$query);
    $query .= (strpos($query, '&fq=contract_status:registered') !== false) ? '&fq=contract_status:registered' : '';
    return $query;
  }else{
    return;
  }
  return;
}

/**
 * @param array $query
 * @param array $selectedFacets
 * @return array
 */
function getActiveContractsQuery($query, $selectedFacets){
  if(isset($selectedFacets['domain']) && in_array('contracts', $selectedFacets['domain'])){
    //Active contracts count
    $query = str_replace('&fq=contract_status:"pending"','',$query);
    $query = str_replace('&fq=registered_fiscal_year','&fq=facet_year_array',$query);
    $query .= (strpos($query, '&fq=contract_status:registered') !== false) ? '&fq=contract_status:registered' : '';
    return $query;
  }else{
    return;
  }
  return;
}
/**
 * @param array $search_results
 * @param array $datasource_facets
 * @return array
 */
function _ckbk_ss_sort_results(array $search_results, array $datasource_facets): array{
  /**
   * Sorting facet by value, like Fiscal Year desc
   */
  foreach ($search_results['facet_counts']['facet_fields'] as $facet => $v) {
    if ($sort = $datasource_facets[$facet]->sort_by_key ?? 0) {
      if ($sort > 0) {
        ksort($search_results['facet_counts']['facet_fields'][$facet]);
      } else {
        krsort($search_results['facet_counts']['facet_fields'][$facet]);
      }
    }
  }
  return $search_results;
}

/**
 * @param array $search_results
 * @return array
 */
function _ckbk_remove_empty_intervals(array $search_results): array{
  /**
   * Manually getting rid of empty facet_intervals values because
   * facet.mincount does not work with intervals/ranges in solr
   */
  if (isset($search_results['facet_counts']['facet_intervals'])) {
    foreach ($search_results['facet_counts']['facet_intervals'] as $facet => $data) {
      foreach ($data as $k => $v) {
        if (!$v) {
          unset($search_results['facet_counts']['facet_intervals'][$facet][$k]);
        }
      }
      if (!sizeof($search_results['facet_counts']['facet_intervals'][$facet])) {
        unset($search_results['facet_counts']['facet_intervals'][$facet]);
      }
    }
    if (!sizeof($search_results['facet_counts']['facet_intervals'])) {
      unset($search_results['facet_counts']['facet_intervals']);
    }
  }
  return $search_results;
}

/**
 * @param array $search_results
 */
function _inject_smart_search_drupal_settings(array $search_results): void{
  $vendor_number = count($search_results['facet_counts']['facet_fields']['vendor_name'] ?? []);
  $vendor_pages = floor($vendor_number / 100);
  drupal_add_js(array('checkbook_smart_search' => array('vendor_pages' => $vendor_pages)), 'setting');

  $expense_categories_number = 0;
  if ($search_results['facet_counts']['facet_fields']['expenditure_object_name'] ?? false) {
    $expense_categories_number = sizeof($search_results['facet_counts']['facet_fields']['expenditure_object_name']);
  }

  $expense_category_pages = floor($expense_categories_number / 100);
  drupal_add_js(array('checkbook_smart_search' => array('expense_category_pages' => $expense_category_pages)), 'setting');
}

/** Exports the smart search export data
 * @param string $solr_datasource
 * @param $search_terms
 * @param $page_no
 * @param $no_of_results
 * @param string $fl
 * @param bool $skip_header
 * @return false|string
 */
function _checkbook_smart_search_get_results_csv(string $solr_datasource, string $search_terms, $page_no, $no_of_results, $fl = "*", $skip_header = false){
  $solr_query = new CheckbookSolrQuery($solr_datasource, $search_terms, $no_of_results, $page_no);
  $solr_query
    ->setWt('csv');

  $domain = $_REQUEST['domain'];
  $solr_query->setFq('domain', 'domain:' . $domain);

  $query = $solr_query->buildQuery();
  $query .= '&fl=' . $fl;

  /**
   * DANGER:
   * SOLR 7 (gptext) doesn't seem to pick this up
   */
  if ($skip_header) {
    $query = $query . "&csv.header=false";
  }

  $solr = CheckbookSolr::getInstance($solr_datasource);
  return $solr->request_csv('select/?' . $query);
}

/** Generates smart search export form
 * @param string $solr_datasource
 * @throws Exception
 */
function _checkbook_smart_search_export_form(string $solr_datasource){
  if (!$solr_datasource) {
    echo 'unknown datasource';
  } else {
    echo theme('checkbook_smart_search_export_form', [
      'solr_datasource' => $solr_datasource
    ]);
  }
}

/** Downloads the smart search export data
 * @param string $solr_datasource
 */
function _checkbook_smart_search_export_download(string $solr_datasource){
  $domain = ucfirst($_REQUEST['domain']);
  drupal_add_http_header("Content-Type", "text/csv");
  //drupal_add_http_header("Content-Length",9);
  drupal_add_http_header("Content-Disposition", "attachment; filename={$solr_datasource}{$domain}.csv");
  drupal_add_http_header("Pragma", "cache");
  drupal_add_http_header("Expires", "-1");
  _checkbook_smart_search_export_data($solr_datasource);
}

/**
 * Processes hyphen fields and special case values for export results
 * @param string $result
 * @param $domain
 * @return string $modified_result
 */
function adjustExportResults($result,$domain){
  // Write to browser export file
  $output = fopen('php://output', 'w');
  $lines = explode("\n", $result);
  $headers = str_getcsv(array_shift($lines));
  $lines = str_replace("\,", " ", $lines);
  $lines = preg_replace("/1970-01-01T05:00:00Z/", "N/A", $lines);
  $lines = preg_replace("/(\d{4})-(\d{2})-(\d{2})...:..:.../", "$2/$3/$1", $lines);
  $lines= str_replace("Registered,Expense", "Active,Expense", $lines);
  $lines = str_replace("Registered,Revenue", "Active,Revenue", $lines);
  foreach ($lines as $line) {
    $row = array();
    $data = str_getcsv($line);
    foreach ($data as $key => $field) {
      switch($domain) {
        case "nycha_contracts":
          if ($headers[$key] == 'agreement_type_name') {
            if (strtoupper($field) == 'PURCHASE ORDER') {
              $date_fields = array("start_date", "end_date");
            }
          }
          if ($headers[$key] == 'record_type') {
            if ($field == 'Agreement') {
              $hyphen_fields = array("release_number", "line_number", "commodity_category_name", "item_description", "item_qty_ordered", "shipment_number", "responsibility_center_name",
                "release_line_total_amount", "release_line_original_amount", "release_line_spend_to_date", "release_approved_date", "release_total_amount", "release_original_amount",
                "release_spend_to_date", "location_name", "grant_name", "expenditure_type_name", "funding_source_name", "program_phase_code", "gl_project_name");
            } elseif ($field == 'Release') {
              $hyphen_fields = array("number_of_releases", "line_number", "commodity_category_name", "item_description", "item_qty_ordered", "shipment_number", "responsibility_center_name", "release_line_total_amount",
                "release_line_original_amount", "release_line_spend_to_date", "agreement_total_amount", "agreement_original_amount", "agreement_spend_to_date", "location_name",
                "grant_name", "expenditure_type_name", "funding_source_name", "program_phase_code", "gl_project_name");
            } elseif ($field == 'Line') {
              $hyphen_fields = array("number_of_releases", "release_total_amount", "release_original_amount", "release_spend_to_date", "agreement_total_amount", "agreement_original_amount", "agreement_spend_to_date");
            }
          }
          break;
        case "nycha_spending":
          $hyphen_fields=[];
          array_push($hyphen_fields,"agreement_type_name", "contract_number", "release_number", "contract_purpose","invoice_number");
          $amount_hyphen_fields = array("check_invoice_net_amount", "distribution_line_number", "invoice_line_number");
          if ($headers[$key] == 'document_id') {
            if ($field == null) {
              $field = "N/A";
            }
          }
          if (($headers[$key] == 'spending_category')) {
            if (($field == 'Other') || ($field == 'Section 8')) {
              array_pop($hyphen_fields);
              $category_hyphen = array("industry_type_name");
            } elseif ($field == 'Payroll') {
              $category_hyphen = array( "contract_spent_amount","industry_type_name", "funding_source_name", "responsibility_center_name", "program_phase_name", "gl_project_name");
            } else{
              $hyphen_fields = [];
            }
          }
          break;
        case "nycha_payroll":
          $amount_hyphen_fields = array("annual_salary", "hourly_rate", "daily_wage");
          break;
        case "nycha_revenue":
          if ($headers[$key] == 'adopted_amount') {
            $adopted_amount = $field;
          }
          if ($headers[$key] == 'modified_amount') {
            $field = $adopted_amount;
          }
          break;
        case "nycha_budget":
          if ($headers[$key] == 'adopted_budget') {
            $adopted_amount = $field;
          }
          if ($headers[$key] == 'modified_budget') {
            $field = $adopted_amount;
          }
          break;
        case "citywide_payroll":
          // Display daily wage is hourly rate is zero or '-' if both are zero
          if ($headers[$key] == 'daily_wage') {
            if ($field <= 0) {
              $field = '-';
            }
            $daily_wage = $field;
          }
          if ($headers[$key] == 'hourly_rate') {
            if ($field <= 0) {
              //$field = $daily_wage;
              $field = isset($daily_wage) ? $daily_wage : '-';
            }
          }
          if ($headers[$key] == 'annual_salary' ){
            if ($field <= 0) {
              $field = '-';
            }
          }
          break;
         case "edc_contracts":
          if ($headers[$key] == 'contract_entity_contract_number') {
            // Add space between the number to display the comma correctly in excel
            $field = str_replace(",", " , ", $field); ;
          }
          break;

      }

      if(isset($hyphen_fields) && in_array($headers[$key], $hyphen_fields)) {
        if ($field == null) {
          $field = '-';
        }
      }
      if(isset($category_hyphen) && in_array($headers[$key], $category_hyphen)) {
        if ($field == null) {
          $field = '-';
        }
      }
      if(isset($date_fields) && in_array($headers[$key], $date_fields)) {
        $field = '-';
      }
      if(isset($amount_hyphen_fields) && in_array($headers[$key], $amount_hyphen_fields)) {
        if ($field == null || $field <= 0 ) {
          $field = '-';
        }
      }
      $row[$headers[$key]] = $field;
    }
    if($domain == "citywide_payroll") {
      unset($row['daily_wage']);
    }
    // Write processed results to output
    fputcsv($output, $row);
  }
  fclose($output);
}
/** Exports the smart search export data
 * @param string $solr_datasource
 */
function _checkbook_smart_search_export_data(string $solr_datasource){
  global $conf;
  $remaining = $conf['check_book']['datatables']['iTotalDisplayRecords'];
  $page_size = 10000;
  $page_num = 0;

  $domain = $solr_datasource.'_'.$_REQUEST['domain'];
  $fields = (array)CheckbookSolr::getExportFields($solr_datasource, $_REQUEST['domain']);
  $fl = implode(",", array_keys($fields));
  // Remove daily wage display for citywide payroll
  if($domain == "citywide_payroll") {
    unset($fields['daily_wage']);
  }
  // Write header content to output file
  echo implode(",", array_values($fields)) . "\n";

  while ($remaining > 0) {

    // Since SOLR 7 (gptext) doesn't seem to pick up "&csv.header=false" in _checkbook_smart_search_get_results_csv(), unset CSV header manually
    $result = _checkbook_smart_search_get_results_csv($solr_datasource, $_REQUEST['search_terms'] ?? '', $page_num, $page_size, $fl, true);

    $page_num += 1;
    $remaining = $remaining - $page_size;

//    last page reached => exit
    if (!$result) {
      $remaining = 0;
      continue;
    }

    // Process records before display
    adjustExportResults($result,$domain);
    ob_flush();
    flush();
  }
  exit();
}

/**
 *  Outputs the suggestions for the given input.
 *  Returns the matching words
 *
 * @param string $solr_datasource
 */
function _checkbook_smart_search_autocomplete_main_input($solr_datasource = 'citywide'){
  $search_term = htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES);
  $search_term = strtolower($search_term);

  // NYCCHKBK - 10039 handling smart search text box autocomplete with special characters.
  $autoCompleteSearchTerm ='';
  $terms = explode(' ', $search_term);
  if (count($terms) > 0) {
    foreach($terms as $value){
      $autoCompleteSearchTerm .= CheckbookSolrQuery::escape($value).' ';
    }
  }

  $search_term = '(.* ' . urldecode(trim($autoCompleteSearchTerm)). '.*)|(^' . urldecode(trim($autoCompleteSearchTerm)). '.*)';

  $query = new CheckbookSolrQuery($solr_datasource);

  $autocomplete_categories = (array)CheckbookSolr::getAutocompleteTerms($solr_datasource);

  $query
    ->addTermFields(array_keys($autocomplete_categories))
    ->setTermsRegex($search_term);

  $solr = new CheckbookSolr($solr_datasource);
  $contents = $solr->requestTerms($query);


  $smart_search_url = '/smart_search/' . $solr_datasource;

  $matches_render = [];

  foreach ($contents['terms'] as $term => $matches) {
    foreach ($matches as $match => $count) {
      if ('-' == $match) {
        continue;
      }
      $url = $smart_search_url . "?search_term=*!*{$term}=";
      $matches_render[] = [
        "url" => $url,
        "category" => $autocomplete_categories[$term],
        "label" => $match,
        "value" => urlencode($match)];
    }
  }

  if (!sizeof($matches_render)) {
    $matches_render = [
      "url" => "",
      "label" => '<span>' . "No matches found" . '</span>',
      'value' => 'No matches found'
    ];
  }

  return drupal_json_output($matches_render);
}

/**
 * @param string $solr_datasource
 * @param string $facet
 * @return array
 */
function _checkbook_autocomplete(string $solr_datasource, string $facet): array{
  $solr_query = new CheckbookSolrQuery($solr_datasource, $_REQUEST['search_term'] ?? '');

  $solr_query->setFqAutocompleteTerm($facet, $_REQUEST['term'] ?? '');

  $solr = new CheckbookSolr($solr_datasource);
  $results = $solr->request_phps('select/?' . $solr_query);

  if ($results['response']['numFound'] != 0 && ($results['facet_counts']['facet_fields'] ?? false)) {
    $facet_results = array_shift($results['facet_counts']['facet_fields']);
    //NYCCHKBK-9957 - The autocomplete displayed facets that were already selected. Hence, remove such facets.
    $selected_facets = $solr_query->getSelectedFacets()[$facet];
    $selected_facets_object = [];
    foreach($selected_facets as $key => $val) $selected_facets_object[$val] = $val;
    $facet_results = array_filter($facet_results, function($k) use ($selected_facets_object) {
                    return !isset($selected_facets_object[$k]);
                  }, ARRAY_FILTER_USE_KEY);

    $matches = [];
    foreach (array_keys($facet_results) as $key) {
      array_push($matches, ["url" => "", "category" => $facet, "label" => $key, "value" => $key]);
    }
    if(count($matches)>0) return $matches;
  }
  return [['label' => 'No Matches Found', 'value' => '']];
}

/**
 * @param string $solr_datasource
 * @param string $facet
 */
function _checkbook_smart_search_autocomplete(string $solr_datasource, string $facet){
  $data = _checkbook_autocomplete($solr_datasource, $facet);
  foreach ($data as &$line) {
    $line['value'] = urlencode($line['value']);
  }
  drupal_json_output($data);
}

/**
 * @param string $solr_datasource
 * @param string $facet
 */
function _checkbook_advanced_search_autocomplete(string $solr_datasource, string $facet){
  $data = _checkbook_autocomplete($solr_datasource, $facet);
  drupal_json_output($data);
}

/**
 * @param $results
 * @return array
 */
function _checkbook_vendor_type_prepare_labels($results){
  $pv = $results['p'] + $results['pm'];
  $sv = $results['s'] + $results['sm'];
  $mv = $results['sm'] + $results['pm'];
  $return = [];
  if ($pv) {
    $return['pv'] = ['Prime Vendor', $pv];
  }
  if ($sv) {
    $return['sv'] = ['Sub Vendor', $sv];
  }
  if ($mv) {
    $return['mv'] = ['M/WBE Vendor', $mv];
  }
  return $return;
}

/** Returns the URL for clear functionality for each Search Term
 * @param $facetName
 * @param null $facetValue
 * @param null $count
 * @return string
 */
function _checkbook_smart_search_clear_url(string $solr_datasource,$facetName, $facetValue = null, $count = null){

  $searchArgs = $_REQUEST['search_term'];
  $clearArgURL = "/smart_search/".$solr_datasource."?search_term=";
  $clearURL = "";
  $searchTerms = explode('*!*', $searchArgs);
  if ($facetName == 'search_term') {
    for ($i = 1; $i < count($searchTerms); $i++) {
      $search_params = explode('=', $searchTerms[$i]);
      //$search_params[1] = urldecode($search_params[1]);
      $clearURL .= "*!*" . $search_params[0] . '=' . urlencode($search_params[1]);
    }

    $clearArgURL .= $clearURL;
  } else {
    $clearArgURL .= $searchTerms[0];
    $contractsFlag = false;
    for ($i = 1; $i < count($searchTerms); $i++) {
      $facets = explode('=', $searchTerms[$i]);
      if ($facets[0] != $facetName) {
        if ($facetName == 'domains' && $facetValue == 'contracts') {
          if ($facets[0] == 'contract_categories' || $facets[0] == 'contract_status') {
            $clearArgURL .= "";
          } else {
            $clearArgURL .= '*!*' . $facets[0] . '=' . urlencode($facets[1]);
          }

        } else if ($facetName == 'domains' && $facetValue == 'spending') {
          if ($facets[0] == 'spending_categories') {
            $clearArgURL .= "";
          } else {
            $clearArgURL .= '*!*' . $facets[0] . '=' . urlencode($facets[1]);
          }
        } else {
          $clearArgURL .= '*!*' . $facets[0] . '=' . urlencode($facets[1]);
        }
      }

      if ($facets[0] == 'contract_categories' || $facets[0] == 'contract_status' || $facets[0] == 'spending_categories') {
        if ($facets[0] == $facetName)
          $remove_arg = $facets[1];
      }
      if ($facets[0] == 'domains' && ($facets[1] == 'contracts' || $facets[1] == 'spending')) {
        $contractsFlag = true;
      }
    }

    if ($contractsFlag && ($facetName != 'domains' && ($facetValue != 'contracts' || $facetName != 'spending'))) {
      if ($facetName == 'contract_categories' || $facetName == 'contract_status' || $facetName == 'spending_categories') {
        $contQueryString = "";

        $contArgs = explode('~', $remove_arg);
        foreach ($contArgs as $key => $value) {
          if ($value != urlencode($facetValue)) {
            $contQueryString .= urlencode($value) . "~";
          }
        }
        $contQueryString = substr($contQueryString, 0, -1);
        $clearArgURL .= ($count > 1) ? ('*!*' . $facetName . '=' . $contQueryString) : "";
      }
    }
  }

  return $clearArgURL;
}

/** Returns the html entities for string without replacing <em> and </em> tags.
 * @param $string
 * @return mixed|string $string.
 */
function _checkbook_smart_search_str_html_entities($string){
  $string = str_replace("<em>", "**!**", $string);
  $string = str_replace("</em>", "**||**", $string);
  $string = htmlentities($string);
  $string = str_replace("**!**", "<em>", $string);
  $string = str_replace("**||**", "</em>", $string);

  return $string;
}

/**
 * This function returns the current NYC year  ...
 * @return string    $string = str_replace("^", "\%5E", $string);
 */
function getCurrentYearID(){
  STATIC $current_nyc_year;
  if (!isset($current_nyc_year)) {
    $current_nyc_year = date("Y");
    $current_month = date("m");
    if ($current_month <= 6)
      $current_nyc_year -= 1;
    $current_nyc_year = _getYearIDFromValue($current_nyc_year);
  }
  return $current_nyc_year;
}

