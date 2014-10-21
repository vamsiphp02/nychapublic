<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php


$contracts_parameter_mapping = _checkbook_smart_search_domain_fields('contracts', $IsOge);

if(strtolower($contracts_results['contract_status']) == 'registered'){

   $current_date = date("c").'Z';
   $start_date = date("c", strtotime($contracts_results['start_date']));
   $end_date = date("c", strtotime($contracts_results['end_date']));

   if($start_date <= $current_date && $end_date >= $current_date){
           $contracts_results['status'] = 'Active';
           $status = "A";
   }else{
           $contracts_results['status'] =  'Registered';
           $status = "R";
   }
    $effective_end_year_id = _getYearIDFromValue($contracts_results['effective_end_year']);
    if($effective_end_year_id < _getCurrentYearID()){
        $fiscal_year_id = $effective_end_year_id;
    }
    else{
        $fiscal_year_id = _getCurrentYearID();
    }

    if(strtolower($contracts_results['contract_category_name']) == 'expense'){
        if($IsOge){
            $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/datasource/checkbook_oge/year/". _getFiscalYearID() . '/agency/' . $contracts_results['agency_id'] .'/vendor/'.$contracts_results['vendor_id'];
            $agency_link = "/contracts_landing/status/" .$status."/yeartype/B/datasource/checkbook_oge/year/"._getFiscalYearID().'/agency/'.$contracts_results['agency_id'];

        }
        else {
            if($contracts_results["is_prime_or_sub"] == 'Yes' && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results['vendor_id'], $fiscal_year_id,'B') == ''){
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/subvendor/'.$contracts_results['vendor_id']."/dashboard/ss";
            }else if($contracts_results["is_prime_or_sub"] == 'No' && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results['vendor_id'],  $fiscal_year_id,'B') != ''){
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/vendor/'.$contracts_results['vendor_id']."/dashboard/mp";
            }else if($contracts_results["is_prime_or_sub"] == 'Yes' && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results['vendor_id'],  $fiscal_year_id,'B') != ''){
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/subvendor/'.$contracts_results['vendor_id']."/dashboard/ms";
            }
            else{
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/vendor/'.$contracts_results['vendor_id'];
            }
            $agency_link = "/contracts_landing/status/" .$status."/yeartype/B/year/".$fiscal_year_id.'/agency/'.$contracts_results['agency_id'];
        }
        $contract_Id_link = "/contracts_landing/status/" .$status;

    }else{
        if($IsOge){
            $vendor_link = "/contracts_revenue_landing/status/" .$status."/yeartype/B/datasource/checkbook_oge/year/"._getFiscalYearID() . '/agency/' . $contracts_results['agency_id'] .'/vendor/'.$contracts_results['vendor_id'];
            $agency_link = "/contracts_revenue_landing/status/" .$status."/yeartype/B/datasource/checkbook_oge/year/"._getFiscalYearID().'/agency/'.$contracts_results['agency_id'];
        }
        else{
            if($contracts_results["is_prime_or_sub"] == 'Yes' && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results['vendor_id'],  $fiscal_year_id,'B') == ''){
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/subvendor/'.$contracts_results['vendor_id']."/dashboard/ss";
            }else if($contracts_results["is_prime_or_sub"] == 'No' && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results['vendor_id'],  $fiscal_year_id,'B') != ''){
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/vendor/'.$contracts_results['vendor_id']."/dashboard/mp";
            }else if($contracts_results["is_prime_or_sub"] == 'Yes' && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results['vendor_id'],  $fiscal_year_id,'B') != ''){
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/subvendor/'.$contracts_results['vendor_id']."/dashboard/ms";
            }
            else{
                $vendor_link = "/contracts_landing/status/" .$status."/yeartype/B/year/". $fiscal_year_id .'/vendor/'.$contracts_results['vendor_id'];
            }
            $agency_link = "/contracts_revenue_landing/status/" .$status."/yeartype/B/year/".$fiscal_year_id.'/agency/'.$contracts_results['agency_id'];
        }
        $contract_Id_link = "/contracts_revenue_landing/status/" .$status;
    }
    if($contracts_results['is_prime_or_sub'] == 'Yes'){
        $contract_Id_link .= _checkbook_project_get_year_url_param_string(). (($IsOge) ? '/datasource/checkbook_oge/agency/'.$contracts_results['oge_agency_id'] : '') ."/dashboard/ss?expandBottomContURL=/panel_html/contract_transactions/"."/contract_details";
    }else{
        $contract_Id_link .= _checkbook_project_get_year_url_param_string(). (($IsOge) ? '/datasource/checkbook_oge/agency/'.$contracts_results['oge_agency_id'] : '') ."?expandBottomContURL=/panel_html/contract_transactions/"."/contract_details";
    }
    if($contracts_results['document_code'] == 'MA1' || $contracts_results['document_code'] == 'MMA1' || $contracts_results['document_code'] == 'RCT1'){
        $contract_Id_link .= "/magid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
    }else{
    	$master_contract_Id_link = $contract_Id_link . "/magid/".$contracts_results['master_agreement_id']."/doctype/MMA1";
        if($contracts_results['is_prime_or_sub'] == 'Yes'){
            $contract_Id_link .= "/agid/".$contracts_results['contract_original_agreement_id']."/doctype/".$contracts_results["document_code"];
        }
        else{
            $contract_Id_link .= "/agid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
        }
    }
    $contract_Id_link = ($IsOge) ? $contract_Id_link.'/datasource/checkbook_oge' : $contract_Id_link;

    if($contracts_results['original_agreement_id']){
        $contracts_results['contract_number'] = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
        $master_contract_Id_link = ($IsOge) ? $master_contract_Id_link.'/datasource/checkbook_oge' : $master_contract_Id_link;
        $contracts_results['parent_contract_number'] = "<a href='". $master_contract_Id_link."'>".$contracts_results['parent_contract_number']."</a>";
    }


}else if(strtolower($contracts_results['contract_status']) == 'pending'){
    if(strtolower($contracts_results['contract_category_name']) == 'expense'){
        $agency_link = "/contracts_pending_exp_landing/agency/".$contracts_results['agency_id'];
        $vendor_link = "/contracts_pending_exp_landing/vendor/".$contracts_results['vendor_id'];
        $contract_Id_link = "/contracts_pending_exp_landing/";

    }else{
        $agency_link = "/contracts_pending_rev_landing/agency/".$contracts_results['agency_id'];
        $vendor_link = "/contracts_pending_rev_landing/vendor/".$contracts_results['vendor_id'];
        $contract_Id_link = "/contracts_pending_rev_landing/";
    }

    if($contracts_results['original_agreement_id']){
        $contract_Id_link .= _checkbook_project_get_year_url_param_string() . (($IsOge) ? '/datasource/checkbook_oge/agency/'.$contracts_results['agency_id'] : '') ."?expandBottomContURL=/panel_html/contract_transactions/"."/contract_details";
        if($contracts_results['document_code'] == 'MA1' || $contracts_results['document_code'] == 'MMA1' || $contracts_results['document_code'] == 'RCT1'){
            $contract_Id_link .= "/magid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
        }else{
        	$master_contract_Id_link = $contract_Id_link . "/magid/".$contracts_results['master_agreement_id']."/doctype/MMA1";        	 
        	$contract_Id_link .= "/agid/".$contracts_results['original_agreement_id']."/doctype/".$contracts_results["document_code"];
        }
        $contracts_results['contract_number'] = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
        $contracts_results['parent_contract_number'] = "<a href='". $master_contract_Id_link."'>".$contracts_results['parent_contract_number']."</a>";
    }else{
       $contract_Id_link .= _checkbook_project_get_year_url_param_string()."?expandBottomContURL=/minipanels/pending_contract_transactions/contract/".
                            $contracts_results['fms_pending_contract_number']."/version/".$contracts_results['document_version'];
       $contracts_results['contract_number'] = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
       $contracts_results['parent_contract_number'] = "<a href='". $master_contract_Id_link."'>".$contracts_results['parent_contract_number']."</a>";
    }

    $contracts_results['status'] =  "Pending";
}


if($IsOge && !in_array($contracts_results['contract_type_code'],array('MMA1', 'MA1'))){
    $linkable_fields = array("oge_contracting_agency_name" => $agency_link,
                             "agency_name" => $agency_link,
                             "vendor_name" => $vendor_link,
                            );
}elseif(!$IsOge){
	$linkable_fields = array(
			"agency_name" => $agency_link,
			"vendor_name" => $vendor_link,
	);
	
}

if($IsOge && in_array($contracts_results['contract_type_code'],array('MMA1'))){
	$contracts_parameter_mapping['oge_contracting_agency_name'] = "Contracting Agency";
}

$contracts_results["registration_date"] = ($IsOge)? "N/A" : $contracts_results["registration_date"];

$date_fields = array("start_date_orig","end_date_orig","received_date","registration_date");
$amount_fields = array("current_amount", "original_amount");

$name_fields = array("agency_name", "vendor_name", "award_method_name", "contract_purpose", "expenditure_object_name");

$count = 1;
$rows = array();
$row = array();
foreach ($contracts_parameter_mapping as $key => $title){
  if($key == 'expenditure_object_name'){
     $value = "";
     foreach($contracts_results[$key] as $a => $b){
         $value .= strip_tags($b).',';
     }
     $value = substr($value, 0, -1);
  }else{
    $value = $contracts_results[$key];
  }
  if(is_array($value)){
  	$value = implode(', ' , $value);
  }
  $temp = substr($value, strpos(strtoupper($value), strtoupper($SearchTerm)),strlen($SearchTerm));
  if($key =="contract_number"){
  	$value = "<a href='".$contract_Id_link ."'>".$contracts_results['contract_number']."</a>";
  }else if($key =="parent_contract_number"){
  	$value = "<a href='".$master_contract_Id_link ."'>".$contracts_results['parent_contract_number']."</a>";    	
  }else{
  	$value = str_ireplace($SearchTerm,'<em>'. $temp . '</em>', $value);
  }
  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }else if(in_array($key, $date_fields)){
    if($value != null && $value != "N/A" ){
      $value = date("F j, Y", strtotime($value));
    }
  }else if(array_key_exists($key, $linkable_fields)){
    $value = "<a href='" . $linkable_fields[$key]. "'>". $value ."</a>";
  }
  if($title == "Prime Vendor"){
      $title = "Vendor";
  }

//  if($key == "minority_type_name" && $contracts_results["minority_type_name"]){
//      $id = $contracts_results["minority_type_id"];
//      if($id == '4' || $id == '5'){
//          $id = '4~5';
//      }
//      if($id == '7' || $id == '11'){
//        $value = $contracts_results["minority_type_name"];
//      }
//      else{
//        $value = "<a href='/contracts_landing/status/A/yeartype/B/year/". _getFiscalYearID() ."/mwbe/".$id ."'>" .$contracts_results["minority_type_name"] ."</a>";
//      }
//  }
    if($key == "minority_type_name" && !$contracts_results["minority_type_name"]){
        $value = 'N/A';
    }
    elseif($key == "minority_type_name" && ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results["vendor_id"], $fiscal_year_id, 'B') != ''){
            $id = ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results["vendor_id"], $fiscal_year_id, 'B');
            if($id == '4' || $id == '5'){
                $id = '4~5';
            }
        if($contracts_results["is_prime_or_sub"] == 'Yes'){
            $value = "<a href='/contracts_landing/status/A/yeartype/B/year/". _getFiscalYearID() ."/mwbe/".$id ."/dashboard/ms'>" . MappingUtil::getMinorityCategoryById(ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results["vendor_id"], $fiscal_year_id, 'B'))."</a>";
        }else{
            $value = "<a href='/contracts_landing/status/A/yeartype/B/year/". _getFiscalYearID() ."/mwbe/".$id ."/dashboard/mp'>" . MappingUtil::getMinorityCategoryById(ContractUtil::getLatestMwbeCategoryByVendorByTransactionYear($contracts_results["vendor_id"], $fiscal_year_id, 'B'))."</a>";
        }
    }elseif($key == "minority_type_name" && $contracts_results["minority_type_name"]){
        $value = $contracts_results["minority_type_name"];
    }

  if ($count % 2 == 0){
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'. $value .'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
        $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'. $value .'</div>';
  }
  $count++;
}
print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));
