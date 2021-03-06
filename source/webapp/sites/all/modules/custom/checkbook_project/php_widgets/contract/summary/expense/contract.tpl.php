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

$records = $node->data;
if(is_array($records)){
    $row = $records[0];

    $originalAmount = custom_number_formatter_format($row['original_amount_sum'],2,'$');
    $currentAmount = custom_number_formatter_format($row['current_amount_sum'],2,'$');
    $spentToDateAmount = custom_number_formatter_format($row['spending_amount_sum'],2,'$');
    $cont_id = WidgetUtil::getLabel("contract_id");
    $spnttodt = WidgetUtil::getLabel("spent_to_date");
    $oamnt = WidgetUtil::getLabel("original_amount");
    $camnt = WidgetUtil::getLabel("current_amount");
    $purpose = WidgetUtil::getLabel("contract_purpose");
    $agency = WidgetUtil::getLabel("contract_agency");
    $smnid = RequestUtilities::get('smnid');

    $dynamicLabel = $node->widgetConfig->summaryView->entityColumnLabel;
    $dynamicValue = strtoupper($row[$node->widgetConfig->summaryView->entityColumnName]);

    if(!isset($dynamicLabel))
        $dynamicLabel = WidgetUtil::getLabel("vendor_name");
    $agency_value = strtoupper($row['agency_agency_agency_name']);
    $purpose_value = strtoupper($row['contract_purpose_contract_purpose']);
    $summaryContent =  <<<EOD
<div class="contract-details-heading">
	<div class="contract-id">
		<h2 class="contract-title">{$node->widgetConfig->summaryView->templateTitle}</h2>
	</div>
	<div class="dollar-amounts">
		<div class="spent-to-date">
			{$spentToDateAmount}
            <div class="amount-title">{$spnttodt}</div>
		</div>
		<div class="original-amount">
		    {$originalAmount}
            <div class="amount-title">{$oamnt}</div>
		</div>
		<div class="current-amount">
		    {$currentAmount}
            <div class="amount-title">{$camnt}</div>
		</div>
	</div>
</div>
<div class="contract-information">
	<ul>
	    <li class="contractid">
	        <span class="gi-list-item">{$cont_id}:</span> {$row['contract_number_contract_number']}
	    </li>
		<li class="contract-purpose">
			<span class="gi-list-item">{$purpose}:</span> {$purpose_value}
        </li>
		<li class="agency">
			<span class="gi-list-item">{$agency}:</span> {$agency_value}
		</li>
		<li class="vendor">
			<span class="gi-list-item">{$dynamicLabel}:</span> {$dynamicValue}
		</li>
	</ul>
</div>
EOD;

    //Hide Contract summary on Contract Spending Transactions page (Total Spent to Date link under 'Sub Vendor Information' section)                    
    if($smnid == 721 && preg_match("/^contract\/spending\/transactions\/contnum/",$_GET['q'])) {
        print "<div><div class='contract-details-heading'>
	<div class='contract-id'>
		<h2 class='contract-title'>{$node->widgetConfig->summaryView->templateTitle}</h2>
	</div>
        </div></div>";
    }else{                 
        print $summaryContent;
    }

}
