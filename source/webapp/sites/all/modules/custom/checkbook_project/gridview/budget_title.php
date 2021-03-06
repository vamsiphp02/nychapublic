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
 
$title = 'New York City';
$domain= 'Budget';
$agency = RequestUtilities::get('agency');
$expcategory = RequestUtilities::get('expcategory');

if(!empty($expcategory)){
    $expName = _checkbook_project_get_name_for_argument('object_class_id', $expcategory);

        $title =  $expName;
}else if(!empty($agency)){
    $title = _checkbook_project_get_name_for_argument('agency_id', $agency) ;
}
