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

//Transactions Page main title
$title = NychaSpendingUtil::getTransactionsTitle();

//Transactions Page sub title
$url = $_REQUEST['expandBottomContURL'];
$url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);

if(isset($url)) {
  $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
  // Display static content ytd link transaction pages
  if (strpos($widget, 'ytd_') !== false) {
    $aggregatedYtdTitle = WidgetUtil::getLabel("ytd_spending");
    $aggregatedAmountTitle = WidgetUtil::getLabel("total_contract_amount");
    $subTitle = NychaSpendingUtil::getTransactionsSubTitle($widget, $url);
    $summaryDetails = NychaSpendingUtil::getTransactionsStaticSummary($widget, $url);
    $ytdAmount = '$' . custom_number_formatter_format($summaryDetails['check_amount_sum'], 2);
    $aggregatedAmount = '$' . custom_number_formatter_format($summaryDetails['total_contract_amount_sum'], 2);

    if (isset($widget) && $widget == 'ytd_contract') {
      $contractDetails = NychaSpendingUtil::getTransactionsStaticSummary($widget, $url);
      $ytdAmount = '$' . custom_number_formatter_format($contractDetails['check_amount_sum'], 2);
      $aggregatedAmount = '$' . custom_number_formatter_format($contractDetails['total_contract_amount'], 2);
      $contractSummary = "<div class='contract-information contract-summary-block'>
                        <ul>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Contract ID:</b></span> {$contractDetails['contract_id']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Purpose:</b></span> {$contractDetails['contract_purpose']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
                              <span class=\"gi-list-item\"><b>Vendor:</b></span> {$contractDetails['vendor_name']}
                          </li>
                        </ul>
                      </div>";
      $subTitle = $contractSummary;
    }
  }
  // Display static content for details link transaction pages
  else{
    $subTitle = "<div class='spending-tx-subtitle'>{$subTitle}</div>";
    $aggregatedAmountTitle = "Total Spending Amount";
    $totalSpendingAmount = '$' . custom_number_formatter_format($node->data[0]['check_amount_sum'], 2);

  }
}

//Title section
$titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2>
                    {$subTitle}
                  </div>";
if (strpos($widget, 'ytd_') !== false) {
  if (isset($widget) && ($widget == 'ytd_contract' || $widget == 'ytd_vendor')) {
    $amountsSummary = "<div class='dollar-amounts'>
                        <div class='total-spending-amount'>{$aggregatedAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle}</div>
                        </div>
                        <div class='ytd-spending-amount'>{$ytdAmount}
                          <div class='amount-title'>{$aggregatedYtdTitle}</div>
                        </div>
                      </div></div>";
  }
  else
  {
    $amountsSummary = "<div class='dollar-amounts'>
                        <div class='ytd-spending-amount'>{$ytdAmount}
                          <div class='amount-title'>{$aggregatedYtdTitle}</div>
                        </div>
                      </div></div>";
  }
}
else{
  $amountsSummary = "<div class='dollar-amounts'>
                        <div class='total-spending-amount'>{$totalSpendingAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle}</div>
                        </div>
                      </div></div>";
}
echo $titleSummary . $amountsSummary;
