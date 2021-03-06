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


class CheckbookDateUtil{
  /**
   * @var
   */
  private static $currentFiscalYear;
  /**
   * @var
   */
  private static $currentFiscalYearId;
  /**
   * @var
   */
  private static $currentCalendarYear;
  /**
   * @var
   */
  private static $currentCalendarYearId;
  /**
   * @var
   */
  private static $startingFiscalYear;
  /**
   * @var
   */
  private static $startingFiscalYearId;
  /**
   * @var
   */
  private static $startingCalendarYear;
  /**
   * @var
   */
  private static $startingCalendarYearId;


  /**
   * Sets up current and beginning year values if Drupal variables are not set
   */
  private static function setCurrentYears(){
    if (self::$currentCalendarYear) {
      return;
    }
    self::$currentFiscalYear = self::$currentCalendarYear = date('Y');

    // Calendar year is used for Payroll Domain (Citywide and NYCHA)
    self::$currentCalendarYearId = self::year2yearId(self::$currentCalendarYear);

    // Fiscal year starts from July for NYC non-federal agencies
    if (6 < date('m')) {
      self::$currentFiscalYear++;
    }
    self::$currentFiscalYearId = self::year2yearId(self::$currentFiscalYear);

    self::$startingFiscalYear = self::$currentFiscalYear-11;
    self::$startingFiscalYearId = self::year2yearId(self::$startingFiscalYear);

    self::$startingCalendarYear = self::$currentCalendarYear-11;
    self::$startingCalendarYearId = self::year2yearId(self::$startingCalendarYear);
  }

  /**
   * @param $year
   * @return int
   */
  public static function year2yearId($year){
    return $year < 1900 ? $year : $year - 1899;
  }

  /**
   * @param $id
   * @return int
   */
  public static function yearId2Year($id){
    return $id > 1900 ? $id : $id + 1899;
  }

  /**
   * @param $data_source
   *
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush vset default_checkbook_fy 2021
   * drush vset default_checkbook_oge_fy 2021
   * drush vset default_checkbook_nycha_fy 2021
   *
   * @return string
   */
  public static function getMaxDatasourceFiscalYear(string $data_source){
    self::setCurrentYears();
    $key = 'current_' . $data_source . '_fy';
    if ($year = _checkbook_dmemcache_get($key)) {
      return $year;
    }
    $year = variable_get($key, FALSE);
    isset($year) ? $year : null;
    if (FALSE === $year) {
      LogHelper::log_warn('Drush variable ' . $key . ' not found!');
      $year = self::$currentFiscalYear;
    }
    _checkbook_dmemcache_set($key, $year);
    return isset($year) ? $year : null;
  }

  /**
   * @param $data_source
   *
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush vset current_checkbook_fy 2022
   * drush vset current_checkbook_oge_fy 2022
   * drush vset current_checkbook_nycha_fy 2021
   *
   * @return string
   */
  public static function getCurrentDatasourceFiscalYear(string $data_source){
    self::setCurrentYears();
    $key = 'default_' . $data_source . '_fy';
    if ($year = _checkbook_dmemcache_get($key)) {
      return $year;
    }
    $year = variable_get($key, FALSE);
    isset($year) ? $year : null;
    if (FALSE === $year) {
      LogHelper::log_warn('Drush variable ' . $key . ' not found!');
      $year = self::$currentFiscalYear;
    }
    _checkbook_dmemcache_set($key, $year);
    return isset($year) ? $year : null;
  }

  /**
   * @param $data_source
   * @param $domain
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush vset min_checkbook_fy 2011
   * drush vset min_checkbook_oge_fy 2011
   * drush vset min_checkbook_nycha_fy 2010
   * drush vset min_checkbook_nycha_budget_fy 2018
   *
   * @return string
   */
  public static function getCurrentDatasourceStartingYear(string $data_source, $domain = NULL){
    self::setCurrentYears();
    if($domain == Domain::$NYCHA_BUDGET || $domain == Domain::$NYCHA_REVENUE ||
      ($data_source == Datasource::NYCHA && ($domain == Domain::$REVENUE || $domain == Domain::$BUDGET))){
      $key = 'min_' . $data_source . '_budget_fy';
    }else{
      $key = 'min_' . $data_source . '_fy';
    }

    if ($year = _checkbook_dmemcache_get($key)) {
      return $year;
    }
    $year = variable_get($key, FALSE);
    if (FALSE === $year) {
      LogHelper::log_warn('Drush variable ' . $key . ' not found!');
      $year = self::$startingFiscalYear;
    }
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @param $data_source
   *
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush vset min_cy 2010
   * @return string
   */
  public static function getStartingCalendarYear(){
    self::setCurrentYears();
    $key = 'min_cy';
    if ($year = _checkbook_dmemcache_get($key)) {
      return $year;
    }
    $year = variable_get($key, FALSE);
    if (FALSE === $year) {
      LogHelper::log_warn('Drush variable ' . $key . ' not found!');
      $year = self::$startingCalendarYear;
    }
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @param string $data_source
   * @return mixed
   */
  public static function getCurrentFiscalYear($data_source = Datasource::CITYWIDE){
    self::setCurrentYears();
    $data_source = ($data_source == Datasource::NYCHA || Datasource::isNYCHA()) ? Datasource::NYCHA : $data_source;
    $year_value = self::getCurrentDatasourceFiscalYear($data_source);
    return isset($year_value) ? $year_value : null;
  }

  /**
   * @param string $data_source
   * @return string
   */
  public static function getCurrentFiscalYearId($data_source = Datasource::CITYWIDE){
    $year_value = self::year2yearId(self::getCurrentFiscalYear($data_source));
    return isset($year_value) ? $year_value : null ;
  }


  /**
   * @param string $data_source
   * @param string $domain
   * @return mixed
   */
  public static function getStartingFiscalYear($data_source = Datasource::CITYWIDE, $domain = NULL){
    self::setCurrentYears();
    $data_source = ($data_source == Datasource::NYCHA || Datasource::isNYCHA()) ? Datasource::NYCHA : $data_source;
    return self::getCurrentDatasourceStartingYear($data_source, $domain);
  }

  /**
   * @param string $data_source
   * @return mixed
   */
  public static function getStartingFiscalYearId($data_source = Datasource::CITYWIDE){
    return self::year2yearId(self::getStartingFiscalYear($data_source));
  }

  /**
   * @return mixed
   * drush vset current_calendar_year_cy 2020
   */
  public static function getCurrentCalendarYear(){
    self::setCurrentYears();
    $key = 'current_calendar_year_cy';
    if ($year = _checkbook_dmemcache_get($key)) {
      return $year;
    }
    $year = variable_get($key, FALSE);
    if (FALSE === $year) {
      LogHelper::log_warn('Drush variable ' . $key . ' not found!');
      $year = self::$currentCalendarYear;
    }
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @return mixed
   */
  public static function getCurrentCalendarYearId(){
    self::setCurrentYears();
    return self::year2yearId(self::getCurrentCalendarYear());
  }


  /**
   * @return mixed
   */
  public static function getStartingCalendarYearId(){
    self::setCurrentYears();
    return self::year2yearId(self::getStartingCalendarYear());
  }

  /**
   * @return array
   * @param $data_source
   */
  function getCurrentYears($data_source = Datasource::CITYWIDE){
    $maxYear = self::getMaxDatasourceFiscalYear($data_source);
    return [
      'year_value' => $maxYear,
      'year_id' => self::year2yearId($maxYear),
      'cal_year_value' => self::getCurrentCalendarYear(),
      'cal_year_id' => self::getCurrentCalendarYearId()
    ];
  }

  /**
   * @param $data_source
   * @param $domain
   * @return array
   */
  public static function getFiscalYearsRange($data_source = Datasource::CITYWIDE, $domain = null){
    $last = self::getMaxDatasourceFiscalYear($data_source);
    $first = self::getStartingFiscalYear($data_source, $domain);
    $results = [];
    for ($i = $last; $i >= $first; $i--) {
      $results[$i] = $i;
    }
    return $results;
  }

  /**
   * @param string $data_source
   * @param string $domain
   * @return array
   */
  public static function getFiscalYearOptionsRange($data_source, $domain = NULL){
    $last = self::getMaxDatasourceFiscalYear($data_source);
    $first = self::getStartingFiscalYear($data_source, $domain);

    $results = [];
    for ($year = $last; $year >= $first; $year--) {
      $results[] = [
        'year_id' => self::year2yearId($year),
        'year_value' => $year
      ];
    }
    return $results;
  }

  /**
   * @return array
   */
  public static function getCalendarYearOptionsRange($data_source){
    $last = self::getCurrentCalendarYear();
    $first = self::getStartingCalendarYear();

    $results = [];
    for ($year = $last; $year >= $first; $year--) {
      $results[] = [
        'year_id' => self::year2yearId($year),
        'year_value' => $year
      ];
    }
    return $results;
  }

  /**
   * @param $monthId
   * @return mixed|null
   */
  static function getMonthDetails($monthId){
    if (!isset($monthId)) {
      return NULL;
    }
    $monthDetails = _checkbook_project_querydataset('checkbook:month', array('month_id', 'month_value', 'month_name', 'month_short_name'), array('month_id' => $monthId));
    return $monthDetails;
  }

  /**
   * return full year text value for a give year id
   * @param $yearId
   * @param $yearType
   * @return string
   */
  public static function getFullYearString($yearId = null, $yearType=null){
    $yearId = isset($yearId) ? $yearId : RequestUtilities::get('year');
    $yearId = empty(((empty($yearId))) ? RequestUtilities::get('calyear') : $yearId) ? self::getCurrentFiscalYearId() :  $yearId;
    $yearType = isset($yearType) ? $yearType: RequestUtilities::get('yeartype');
    $yearType = (empty($yearType)) ? 'B' : $yearType;
    $yearValue = _getYearValueFromID($yearId);
    $yearString = ($yearType == 'B') ? "FY $yearValue" : "CY $yearValue";
    if (RequestUtilities::get('datasource') == Datasource::NYCHA) {
      $yearString .= "(January 1, " . ($yearValue) . " - Decemeber 31, $yearValue)";
    } else {
      $yearString .= ($yearType == 'B') ? " (July 1, " . ($yearValue - 1) . " - June 30, $yearValue)" : " (January 1, $yearValue - December 31, $yearValue)";
    }
    return $yearString;
  }

  /** CITYWIDE Top Navigation
   * Returns Year ID for Spending, Contracts, Budget and Revenue domains navigation URLs from Top Navigation
   * @return integer $fiscalYearId
   */
  public static function getFiscalYearIdForTopNavigation()
  {
    $year = RequestUtilities::get("year");
    if (!$year) {
      $year = CheckbookDateUtil::getCurrentFiscalYearId();
    }

    //For CY 2010 Payroll selection, other domains should be navigated to FY 2011
    $fiscalYearId = ($year == self::getStartingCalendarYearId() && strtoupper(RequestUtilities::get("yeartype")) == 'C') ? self::getStartingFiscalYearId() : $year;
    return $fiscalYearId;
  }

  /** CITYWIDE Top Navigation
   * Returns Year ID for Payroll domain navigation URLs from Top Navigation
   * @return integer $calYearId
   */
  public static function getCalYearIdForTopNavigation()
  {
    $year = null;
    if (RequestUtilities::get("year") != NULL) {
      $year = RequestUtilities::get("year");
    }
    $currentCalYear = CheckbookDateUtil::getCurrentCalendarYearId();
    if (is_null($year) || $year > $currentCalYear) {
      $year = $currentCalYear;
    }
    return $year;
  }

}
