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
?>
<?php
$contactStatus = _getRequestParamValue('contstatus');
$contactStatusLabel = 'Active';
if ($contactStatus == 'R') {
  $contactStatusLabel = 'Registered';
}
$contactCategory = _getRequestParamValue('contcat');
$contactCategoryLabel = 'Expense';
if ($contactCategory == 'revenue') {
  $contactCategoryLabel = 'Revenue';
}
$summaryTitle = NodeSummaryUtil::getInitNodeSummaryTitle();
print "<h2 class='contract-title' class='title'>{$summaryTitle}<br/>{$contactStatusLabel} {$contactCategoryLabel} Contracts Transactions</h2>";

global $checkbook_breadcrumb_title;
$checkbook_breadcrumb_title =  "$summaryTitle $contactStatusLabel $contactCategoryLabel Contracts Transactions";  
