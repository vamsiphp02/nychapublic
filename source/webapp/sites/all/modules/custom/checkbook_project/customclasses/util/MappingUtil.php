<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 8/6/14
 * Time: 5:07 PM
 */

class MappingUtil {
  /**
   * @var array
   */
    private static $vendor_type_value_map = array(
        'pv' => 'P~PM',
        'sv' => 'S~SM',
        'mv' => 'SM~PM',
    );

  /**
   * @var array
   */
    private static $vendor_type_name_map = array(
        'sv' => 'Sub Vendor',
        'pv' => 'Prime Vendor',
        'mv' => 'M/WBE Vendor',
    );

  /**
   * @var array
   */
    public static $spendingMWBEParamMap = [
        "year" => "year_id",
        "yeartype" => "type_of_year",
        "agency" => "agency_id",
        "vendor" => "vendor_id",
    ];

  /**
   * @var array
   */
    public static $contractsMWBEParamMap = [
        "year" => "fiscal_year_id",
        "agency" => "agency_id",
        "yeartype" => "type_of_year",
        "vendor" => "vendor_id",
    ];

    /** Returns the vendor type value based on the vendor_type mapping
     * @param $vendor_types
     * @return array
     */
    public static function getVendorTypeValue($vendor_types): array
    {
        $param = "";
        foreach($vendor_types as $key=>$value){
            $param .= self::$vendor_type_value_map[$value].'~';
        }
        return explode('~',substr($param, 0, -1));
    }

    /** Returns the vendor type name based on the vendor_type mapping
     * @param $vendor_type
     * @return mixed
     */
    public static function getVendorTypeName($vendor_type) {
        return self::$vendor_type_name_map[$vendor_type];
    }

  /**
   * @var array
   */
    private static $minority_type_category_map = array(
        2 => 'Black American',
        3 => 'Hispanic American',
        4 => 'Asian American',
        5 => 'Asian American',
        6 => 'Native American',
        7 => 'Non-M/WBE',
        9 => 'Women (Non-Minority)',
        11 => 'Individuals and Others',
        99 => 'Emerging (Non-Minority)'
    );

  /**
   * @var array
   */
    private static $facet_minority_type_category_map = array(
      2 => 'Black American',
      3 => 'Hispanic American',
      4 => 'Asian American',
      5 => 'Asian American',
      6 => 'Native American',
      7 => 'Non-M/WBE',
      9 => 'Women (Non-Minority)',
      11 => 'Individuals and Others',
      99 => 'Emerging (Non-Minority)'
    );

  /**
   * @var array
   */
    private static $minority_type_category_map_by_name = array(
        'African American' => 'Black American',
        'Hispanic American' => 'Hispanic American',
        'Asian-Pacific' => 'Asian American',
        'Asian-Indian' => 'Asian American',
        'Non-Minority' => 'Non-M/WBE',
        'Women (Non-Minority)' => 'Women (Non-Minority)',
        'Caucasian Woman' => 'Women (Non-Minority)',
        'Individuals & Others' => 'Individuals and Others',
        'Native American' => 'Native American',
        'Emerging (Non-Minority)' => 'Emerging (Non-Minority)',
    );

  /**
   * @var array
   */
    private static $minority_type_category_map_multi = array(
        'Total M/WBE' => array(2,3,4,5,6,9,99),
        'Asian American' => array(4,5),
        'Black American' => array(2),
        'Women (Non-Minority)' => array(9),
        'Hispanic American' => array(3),
        'Native American' => array(6),
        'Emerging (Non-Minority)' => array(99),
        'Non-M/WBE' => array(7),
        'Individuals and Others' => array(11),
    );

  /**
   * @var string
   */
    static $mwbe_prefix = "M/WBE" ;

  /**
   * @var string
   */
    static $total_mwbe_cats = "2~3~4~5~6~9~99";

  /**
   * @var array
   */
    public static $minority_type_category_map_multi_chart = array(
        'Black American' => array(2),
        'Hispanic American' => array(3),
        'Asian American' => array(4,5),
        'Non-M/WBE' => array(7),
        'Women (Non-Minority)' => array(9),
        'Individuals and Others' => array(11),
        'Native American' => array(6),
        'Emerging (Non-Minority)' => array(99),
        'M/WBE' => array(2,3,4,5,6,9,99),
    );

  /**
   * @param $mwbe_cats
   * @return bool
   */
    public static function isMWBECertified($mwbe_cats): bool
    {
        if(count(array_intersect($mwbe_cats, self::$minority_type_category_map_multi_chart[self::$mwbe_prefix])) > 0){
            return true;
        }else{
            return false;
        }
    }

    /** Returns the M/WBE certified formatted ids
     *  * @param $param
    * @return ids
   */
    public static function getTotalMinorityIds($param = null) {
      if(isset($param) && $param = 'url') {
        return MappingUtil::$total_mwbe_cats;
      }
      else{
        $mwbe_ids = str_replace("~",",",MappingUtil::$total_mwbe_cats);
        return $mwbe_ids;
      }
    }

    /** Returns the M/WBE category name based on the minority_type_id mapping
     * @param $minority_type_id
     * @param $facet
     * @return mixed
     */
    public static function getMinorityCategoryById($minority_type_id, $facet = false) {
        if($facet){
          return self::$facet_minority_type_category_map[$minority_type_id];
        }else {
          return self::$minority_type_category_map[$minority_type_id];
        }
    }

    /** Returns the M/WBE category name based on the minority_type_id mapping
     * @param $minority_type_name
     * @return mixed
     */
    public static function getMinorityCategoryByName($minority_type_name) {
        return self::$minority_type_category_map_by_name[$minority_type_name];
    }

    /** Returns the M/WBE category name and it's minority_type_id mapping as an array */
    public static function getMinorityCategoryMappings(): array
    {
        return self::$minority_type_category_map_multi;
    }

    /**
     * @param null $minority_type_ids
     * @return string
     */
    public static function getCurrenEthnicityName($minority_type_ids = null)
    {
        $mwbe_url_params = $minority_type_ids ?? explode('~', RequestUtilities::get('mwbe'));
        foreach(self::$minority_type_category_map_multi_chart as $key=>$values){
            if(count(array_diff($mwbe_url_params, $values)) == 0){
                return $key;
            }
        }
    }

    private static function isDefaultMWBEDashboard(): bool
    {
        $currentURL = request_uri();
        if(strpos($currentURL, "?expandBottomContURL=")){
            $currentURL = explode("?expandBottomContURL=", $currentURL);
            $currentURL = $currentURL[0];
        }
        $filters_applied = strpos($currentURL, "agency/") || strpos($currentURL, "vendor/");
        return !$filters_applied;
    }

    /**
     * Function to populate the M/WBE dashboard drop down filters
     *
     * @param $active_domain_link
     * @param $domain
     * @return string
     */
    public static function getCurrentMWBETopNavFilters($active_domain_link, $domain): string
    {
        if(self::isDefaultMWBEDashboard()){
            $applicable_minority_types = self::$minority_type_category_map_multi_chart['M/WBE'];
        }
        else if(RequestUtil::isDashboardFlowSubvendor()){
            $applicable_minority_types = self::getCurrentSubMWBEApplicableFilters($domain);
        }else{
            $applicable_minority_types = self::getCurrentPrimeMWBEApplicableFilters($domain);
        }

        $active_domain_link =  preg_replace('/\/mwbe\/[^\/]*/','',$active_domain_link);

        $filters_html =  "<div class='main-nav-drop-down' style='display:none'>
  		<ul>
  			<li class='no-click title'>M/WBE Category</li>
  					";

        if(array_intersect($applicable_minority_types,array(4,5))){
            $filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/4~5'>Asian American</a></li>";
        }
        if(array_intersect($applicable_minority_types,array(2))){
            $filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/2'>Black American</a></li>";
        }
        if(array_intersect($applicable_minority_types,array(9))){
            $filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/9'>Women (Non-Minority)</a></li>";
        }
        if(array_intersect($applicable_minority_types,array(3))){
            $filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/3'>Hispanic American</a></li>";
        }

        if(array_intersect($applicable_minority_types,array(6))){
            $filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/6'>Native American</a></li>";
        }

        if(array_intersect($applicable_minority_types,array(99))){
            $filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/99'>Emerging (Non-Minority)</a></li>";

        }

        $total_mwbe_link = RequestUtil::getTotalMWBELink();
        $mwbe_total_link_html = '';
        if($total_mwbe_link !=  null && $total_mwbe_link != ""){
            $mwbe_total_link_html  ="<li class='no-click'><a href='" . $total_mwbe_link."'>Total M/WBE</a></li>";
        }

        //Set year value to 2011 for CY2010
        $year = CheckbookDateUtil::getFiscalYearIdForTopNavigation();
        $yearType = 'B';
        $filters_html .=  "
  			 " . $mwbe_total_link_html . "
			<li class='no-click'><a href='/" . RequestUtil::getLandingPageUrl($domain,$year,$yearType) . "/mwbe/" . MappingUtil::$total_mwbe_cats ."/dashboard/mp'>M/WBE Home</a></li>
  				</ul>
  		</div>";

        return $filters_html;
    }

    /**
     * Function to populate the Sub Vendors dashboard drop down filters
     *
     * @param $active_domain_link
     * @param $domain
     * @return string
     */
    public static function getCurrentSubVendorsTopNavFilters($active_domain_link, $domain): string
    {
        $mwbe_filters_html = "";
        $tm_wbe = RequestUtilities::get('tm_wbe');

        //M/WBE filters should be included in mp and sp dashboards
        if(RequestUtil::isDashboardFlowPrimevendor() || $tm_wbe == "Y") {
            if(self::isDefaultMWBEDashboard()){
                $applicable_minority_types = self::$minority_type_category_map_multi_chart['M/WBE'];
            }
            else{
                $applicable_minority_types = self::getCurrentSubMWBEApplicableFilters($domain);
            }

            $active_domain_link =  preg_replace('/\/mwbe\/[^\/]*/','',$active_domain_link);

            if(array_intersect($applicable_minority_types,array(4,5))){
                $mwbe_filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/4~5'>Asian American</a></li>";
            }
            if(array_intersect($applicable_minority_types,array(2))){
                $mwbe_filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/2'>Black American</a></li>";
            }
            if(array_intersect($applicable_minority_types,array(9))){
                $mwbe_filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/9'>Women (Non-Minority)</a></li>";
            }
            if(array_intersect($applicable_minority_types,array(3))){
                $mwbe_filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/3'>Hispanic American</a></li>";
            }

            if(array_intersect($applicable_minority_types,array(6))){
                $mwbe_filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/6'>Native American</a></li>";
            }

            if(array_intersect($applicable_minority_types,array(99))){
                $mwbe_filters_html .=  "<li class='no-click'><a href='/" . $active_domain_link . "/mwbe/99'>Emerging (Non-Minority)</a></li>";
            }

            if($mwbe_filters_html != "") {
              $mwbe_filters_html =  "<li class='no-click title'>M/WBE Category</li>" . $mwbe_filters_html;
            }
        }

        //Sub vendors home link
        $year = CheckbookDateUtil::getFiscalYearIdForTopNavigation();
        $yearType = 'B';
        $sub_vendors_home_link = RequestUtil::getLandingPageUrl($domain,$year,$yearType);
        $home_link_html = "<li class='no-click'><a href='/" . $sub_vendors_home_link . "/dashboard/ss'>Sub Vendors Home</a></li>";

        //Sub vendors total link
        $total_subven_link = RequestUtil::getTotalSubvendorsLink();
        $total_link_html = $total_subven_link !=  null && $total_subven_link != ""
            ? "<li class='no-click'><a href='" . $total_subven_link . "'>Total Sub Vendors</a></li>"
            : "";

        //Append all links
        $filters_html  =  "<div class='main-nav-drop-down' style='display:none'>";
        $filters_html .=    "<ul>";
        $filters_html .=        $mwbe_filters_html . $total_link_html . $home_link_html;
        $filters_html .=    "</ul>";
        $filters_html .=  "</div>";

        return $filters_html;
    }

  /**
   * @param $domain
   * @return array
   */
    public static function getCurrentPrimeMWBEApplicableFilters($domain): array
    {
        switch($domain){
            case "spending":
                $table = "aggregateon_mwbe_spending_coa_entities";
                $where_filters = array();

                foreach(self::$spendingMWBEParamMap as $param=>$value){
                    if(RequestUtilities::get($param) != null){
                        $paramValue = RequestUtilities::get($param);
                        if($param == 'yeartype' && $paramValue == 'C'){
                            $paramValue = 'B';
                        }
                        $where_filters[] = _widget_build_sql_condition(' a1.' . $value, $paramValue);
                    }
                }

                if(count($where_filters) > 0){
                    $where_filter = ' WHERE ' . implode(' AND ' , $where_filters);
                }

                $sql = 'SELECT a1.minority_type_id FROM ' . $table . ' a1 ' . $where_filter . ' GROUP BY a1.minority_type_id  ';
                $data = _checkbook_project_execute_sql($sql);
                break;
            case "contracts":
                $where_filters = array();
                foreach(self::$contractsMWBEParamMap as $param=>$value){
                    if(RequestUtilities::get($param) != null){
                        $paramValue = RequestUtilities::get($param);
                        if($param == 'yeartype' && $paramValue == 'C'){
                            $paramValue = 'B';
                        }
                        $where_filters[] = _widget_build_sql_condition(' a1.' . $value, $paramValue);
                    }
                }

                if(count($where_filters) > 0){
                    $where_filter = ' where ' . implode(' and ' , $where_filters);
                }

                $sql = 'SELECT a1.minority_type_id
                        FROM {aggregateon_mwbe_contracts_cumulative_spending} a1
                          JOIN {ref_document_code} rd ON a1.document_code_id = rd.document_code_id
                       ' . $where_filter . '
                        GROUP BY a1.minority_type_id';

                $data = _checkbook_project_execute_sql($sql);
                break;
        }
        $applicable_minority_types = array();
        foreach($data as $row){
            $applicable_minority_types[] = $row['minority_type_id'];
        }
        return $applicable_minority_types;
    }

  /**
   * @param $domain
   * @return array
   */
    public static function getCurrentSubMWBEApplicableFilters($domain): array
    {
        switch($domain){
            case "spending":
                $table = "aggregateon_subven_spending_coa_entities";
                $urlParamMap = array("year"=>"year_id","yeartype"=>"type_of_year","agency"=>"agency_id",
                    "subvendor"=>"vendor_id","vendor"=>"prime_vendor_id","category"=>"spending_category_id");

                $where_filters = array();

                foreach($urlParamMap as $param=>$value){
                    if(RequestUtilities::get($param) != null){
                        $paramValue = RequestUtilities::get($param);
                        if($param == 'yeartype' && $paramValue == 'C'){
                            $paramValue = 'B';
                        }
                        $where_filters[] = _widget_build_sql_condition(' a1.' . $value, $paramValue);
                    }
                }

                if(count($where_filters) > 0){
                    $where_filter = ' WHERE ' . implode(' AND ' , $where_filters);
                }

                $sql = 'SELECT a1.minority_type_id  FROM ' . $table. ' a1' . $where_filter . ' GROUP BY a1.minority_type_id  ';
                $data = _checkbook_project_execute_sql($sql);
                break;
            case "contracts":
                $table = "aggregateon_subven_contracts_cumulative_spending";
                $urlParamMap = array("year"=>"fiscal_year_id","agency"=>"agency_id","yeartype"=>"type_of_year","awdmethod"=>"award_method_id","vendor"=>"prime_vendor_id",
                    "subvendor"=>"vendor_id","status"=>"status_flag","csize"=>"award_size_id","cindustry"=>"industry_type_id");
                $where_filters = array();

                foreach($urlParamMap as $param=>$value){
                    if(RequestUtilities::get($param) != null){
                        $paramValue = RequestUtilities::get($param);
                        if($param == 'yeartype' && $paramValue == 'C'){
                            $paramValue = 'B';
                        }
                        $where_filters[] = _widget_build_sql_condition(' a1.' . $value, $paramValue);
                    }
                }

                if(count($where_filters) > 0){
                    $where_filter = ' WHERE ' . implode(' AND ' , $where_filters);
                }

                $sql = 'SELECT a1.minority_type_id FROM {' . $table . '} a1 JOIN {ref_document_code} rd ON a1.document_code_id = rd.document_code_id ' . $where_filter . '
                        GROUP BY a1.minority_type_id';
                $data  = _checkbook_project_execute_sql($sql);
                break;
        }
        $applicable_minority_types = array();
        foreach($data as $row){
          $applicable_minority_types[] = $row['minority_type_id'];
        }
        return $applicable_minority_types;
    }

  /**
   * @param $nodeData
   * @param $param
   * @return array
   */
    public static function getVendorTypes($nodeData, $param): array
    {
        $unchecked = array();
        $checked = array();
        $params = explode('~', $param);
        $vendor_counts = array();
        if (is_array($nodeData)) {
            foreach($nodeData as $row){
                if(in_array($row[0],array('P','PM'))){
                    $vendor_counts['pv'] = $vendor_counts['pv']+ $row[2];
                }
                if(in_array($row[0],array('S','SM'))){
                    $vendor_counts['sv'] = $vendor_counts['sv']+ $row[2];
                }
                if(in_array($row[0],array('PM','SM'))){
                    $vendor_counts['mv'] = $vendor_counts['mv']+ $row[2];
                }
            }
        }
        foreach($vendor_counts as $key=>$value){
            if(in_array($key, $params)) {
              array_push($checked, array($key, self::getVendorTypeName($key), $value));
            }else {
              array_push($unchecked, array($key, self::getVendorTypeName($key), $value));
            }
        }
        return array('unchecked'=>$unchecked, "checked"=>$checked);
    }

  /**
   * @param $vendor_type_name_id
   * @return string
   */
    public static function getMixedVendorTypeNames($vendor_type_name_id): string
    {
        switch($vendor_type_name_id) {
            case 'P~PM':
                return "PRIME VENDOR";
            case 'S~SM':
                return "SUB VENDOR";
            case 'PM~SM':
                return "M/WBE VENDOR";
        }
    }

  /**
   * @param $vendor_id
   * @param $domain
   * @param string $is_prime_or_sub
   * @return string
   */
    public static function getSubVendorEthinictyTitle($vendor_id, $domain, string $is_prime_or_sub = "S"): ?string
    {
        $title = NULL;
        switch($domain){
            case "spending":
                $current_ethnicity_from_filter = MappingUtil::getCurrenEthnicityName();
                if( $current_ethnicity_from_filter != null && $current_ethnicity_from_filter != "M/WBE" ){
                    $title = " <br/><span class=\"second-line\">M/WBE Category: " . $current_ethnicity_from_filter . "</span>";
                }else{
                    $ethnicity_id = SpendingUtil::getLatestMwbeCategoryByVendor($vendor_id, null, null, null, $is_prime_or_sub);
                    if($ethnicity_id > 0){
                        $title = " <br/><span class=\"second-line\">M/WBE Category: " . MappingUtil::getMinorityCategoryById($ethnicity_id). "</span>";
                    }
                }
                break;
            case "contracts":
                $current_ethnicity_from_filter = MappingUtil::getCurrenEthnicityName();
                if( $current_ethnicity_from_filter != null && $current_ethnicity_from_filter != "M/WBE" ){
                    $title = " <br/><span class=\"second-line\">M/WBE category: " . $current_ethnicity_from_filter . "</span>";
                }else{
                    $ethnicity_id = ContractUtil::getLatestMwbeCategoryByVendor($vendor_id, null, null, null, $is_prime_or_sub);
                    if(!$ethnicity_id){
                        $query = "SELECT year_id, minority_type_id
                      FROM contract_vendor_latest_mwbe_category
                      WHERE  vendor_id = ".$vendor_id
                            ." AND is_prime_or_sub = '" . $is_prime_or_sub . "'"
                            ." ORDER BY year_id DESC "
                            ." LIMIT 1 ";
                        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                        if($results) {
                          $ethnicity_id = $results[0]['minority_type_id'];
                        }
                    }
                    if($ethnicity_id != 7 && $ethnicity_id != 11 && $ethnicity_id!==null){
                        $title = " <br/><span class=\"second-line\">M/WBE Category: ".MappingUtil::getMinorityCategoryById($ethnicity_id) . "</span>";
                    }
                }
                break;
        }
        return $title;
    }

  /**
   * @param $vendor_id
   * @param $domain
   * @param string $is_prime_or_sub
   * @return string
   */
    public static function getPrimeVendorEthinictyTitle($vendor_id, $domain,$is_prime_or_sub = "P"): ?string
    {
        $title = NULL;
        $ethnicity_id = NULL;

        if(RequestUtilities::get('mwbe') != NULL){
            switch($domain){
                case "spending":
                    $ethnicity_id = SpendingUtil::getLatestMwbeCategoryTitleByVendor($vendor_id, null, null, $is_prime_or_sub);
                    if($ethnicity_id > 0){
                        $title = " <br/><span class=\"second-line\">M/WBE Category: " . MappingUtil::getMinorityCategoryById($ethnicity_id). "</span>";
                    }
                    break;
                case "contracts":
                    $query = "SELECT DISTINCT minority_type_id
                      FROM contract_vendor_latest_mwbe_category
                      WHERE  vendor_id = ".$vendor_id
                        ." AND is_prime_or_sub = '" . $is_prime_or_sub . "'"
                        ." AND type_of_year = '" . RequestUtilities::get('yeartype') . "'"
                        ." AND year_id = ". RequestUtilities::get('year')
                        ." AND latest_mwbe_flag = 'Y'"
                        ." LIMIT 1 ";

                    $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                    if($results) {
                      $ethnicity_id = $results[0]['minority_type_id'];
                    }

                    if($ethnicity_id != 7 && $ethnicity_id != 11 && $ethnicity_id!==null){
                        $title = " <br/><span class=\"second-line\">M/WBE Category: " .MappingUtil::getMinorityCategoryById($ethnicity_id) . "</span>";
                    }
                    break;
            }
        }
        return $title;
    }

  /**
   * @param $scntrc_status
   * @return string
   */
    public static function getscntrc_status_name($scntrc_status): string
    {
        switch($scntrc_status){
            case 1: return('No Data Entered');
                break;
            case 2: return('Yes');
                break;
            case 3: return('No');
                break;
            case 4: return('Not Required');
                break;
        }
    }

  /**
   * @param $aprv_sta
   * @return string
   */
    public static function getaprv_sta_name($aprv_sta): string
    {
        switch($aprv_sta){
            case 1: return('No Subcontract Payments Submitted');
                break;
            case 2: return('ACCO Rejected Sub Vendor');
                break;
            case 3: return('ACCO Reviewing Sub Vendor');
                break;
            case 4: return('ACCO Approved Sub Vendor');
                break;
            case 5: return('ACCO Canceled Sub Vendor');
                break;
            case 6: return('No Subcontract Information Submitted');
                break;

        }
    }
}
