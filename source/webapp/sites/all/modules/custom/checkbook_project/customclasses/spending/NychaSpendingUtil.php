<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
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


class NychaSpendingUtil
{
  static $widget_titles = array('wt_checks' => 'Checks', 'ytd_check' => 'Check','wt_vendors' => 'Vendors', 'ytd_vendor' => 'Vendor',
    'wt_contracts' => 'Contracts', 'ytd_contract' => 'Contract', 'wt_expense_categories' => 'Expense Categories',
    'ytd_expense_category' => 'Expense Category', 'wt_industries' => 'Industries', 'ytd_industry' => 'Industry',
    'wt_funding_sources' => 'Funding Sources', 'ytd_funding_source' => 'Funding Source', 'wt_departments' => 'Departments',
    'ytd_department' => 'Department');

  static $categories = array(3 => 'Contracts', 2 => 'Payroll', 1 => 'Section 8', 4 => 'Others', null => 'Total');

  /**
   * @return null|string -- Returns transactions title for NYCHA Spending
   */
  static public function getTransactionsTitle($url = null){
    $url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
    $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
    $widget_titles = self::$widget_titles;

    //Transactions Page main title
    $title = isset($widget) ? $widget_titles[$widget]: "";
    $categoryName = self::getCategoryName();
    $title = $title .' '. $categoryName . " Spending Transactions";

    //Transactions Page sub title for YTD Spending Transactions
    $subTitle = NULL;
    if(strpos($widget, 'ytd_') !== false){
      $subTitle = self::getTransactionsSubTitle($widget, $url);
    }

    $titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2>
                    <div class='spending-tx-subtitle'>{$subTitle}</div>
                  </div>
                </div>";

    //Transactions Page Aggegated Amounts Summary
    $nid = 1026;
    $node = node_load($nid);
    $aggregatedAmount = '$'.custom_number_formatter_format($node->data[0]['check_amount_sum'],2);
    $aggregatedAmountTitle = $categoryName. " Spending Amount";
    $amountsSummary = "<div class='dollar-amounts'>
                        <div class='total-spending-amount'>{$aggregatedAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle}</div>
                        </div>
                      </div>";

    return $titleSummary . $amountsSummary;
  }

  /**
   * @return null|string -- Returns Spending Category
   */
  static public function getCategoryName(){
    $categories = self::$categories;
    $category_id = RequestUtilities::get('category');
    return $categories[$category_id];
  }

  /**
   * @param $widget Widget Name
   * @param $bottomURL
   * @return null|string -- Returns Sub Tiltle for TYD Spending Transactions Details
   */
  static public function getTransactionsSubTitle($widget, $bottomURL){
    $WidgetTitles = self::$widget_titles;
    $subTitle = '<b>'.$WidgetTitles[$widget]. ':</b> ';
    switch($widget){
      case 'ytd_vendor':
        $rqParam = RequestUtil::getRequestKeyValueFromURL('vendor', $bottomURL);
        $title = $subTitle . _checkbook_project_get_name_for_argument("vendor_id", $rqParam);
        break;
      case 'ytd_contract':
        break;
      case 'ytd_industry':
        $rqParam = RequestUtil::getRequestKeyValueFromURL('industry', $bottomURL);
        $title = $subTitle . _checkbook_project_get_name_for_argument("industry_type_id", $rqParam);
        break;
      case 'ytd_expense_category':
        $rqParam = RequestUtil::getRequestKeyValueFromURL('exp_cat', $bottomURL);
        $title = $subTitle . _checkbook_project_get_name_for_argument("expenditure_object_id", $rqParam);
        break;
      case 'ytd_funding_source':
        $rqParam = RequestUtil::getRequestKeyValueFromURL('fundsrc', $bottomURL);
        $title = $subTitle . _checkbook_project_get_name_for_argument("funding_source_id", $rqParam);
        break;
      case 'ytd_department':
        $rqParam = RequestUtil::getRequestKeyValueFromURL('dept', $bottomURL);
        $title = $subTitle . _checkbook_project_get_name_for_argument("department_id", $rqParam);
        break;
    }
    return $subTitle;
  }
}
