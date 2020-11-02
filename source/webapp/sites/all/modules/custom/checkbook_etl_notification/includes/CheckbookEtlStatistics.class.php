<?php

/**
 * Class CheckbookEtlStatistics
 */
class CheckbookEtlStatistics
{
  /**
 * @var string
 */
  public static $message_body = '';

  /**
   * @var string
   */
  public static $successSubject = 'Success';

  /**
   * @var string
   */
  public static $prodStatus = 'Success';

  /**
   * @var string
   */
  public static $uatStatus = 'Success';

  /**
   * Last ETL must successfully finish within last 12 hours
   */
  const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;


  /**
   * @param $format
   * @return false|string
   */
  public function get_date($format)
  {
    return date($format, self::timeNow());
  }

  /**
   * @param $url
   * @return bool|string
   */
  public function get_contents($url)
  {
    return file_get_contents($url);
  }

  /**
   * @return int
   */
  public function timeNow()
  {
    return time();
  }


  public function getEtlStatistics($environment){
    $sql = "SELECT *,
            CASE WHEN (process_abort_flag_yn = 'N' AND shard_refresh_flag_yn = 'Y' AND index_refresh_flag_yn = 'Y') 
                  THEN 'Success'
	               ELSE 'Fail'
            END AS status FROM jobs WHERE DATE(load_end_time) = CURRENT_DATE - 1 AND host_environment = '{$environment}'";
    $results = _checkbook_project_execute_sql_by_data_source($sql, 'etl_statistics');
    $databases = array('checkbook' => 'Citywide', 'checkbook_ogent' => 'NYC EDC', 'checkbook_nycha' => 'NYCHA');
    $etlStatus = [];
    foreach($results as $result){
      $etlStatus[$result['database_name']] = array(
        'Database' => $databases[$result['database_name']],
        'Environment' => $result['host_environment'],
        'Last Run Date' => $result['load_end_time'],
        'Last Run Success?' => $result['status'],
        'Last Success Date' => $result['load_end_time'],
        'Last File Load Date' => $result['last_successful_load_date'],
        'Shards Refreshed?' => $result['shard_refresh_flag_yn'],
        'Solr Refreshed?' => $result['index_refresh_flag_yn'],
        'Status' => $result['status'], );

      if($environment == 'prod'){
        self::$prodStatus = (self::$prodStatus == 'Fail') ? self::$prodStatus : $result['status'];
      }

      if($environment == 'uat'){
        self::$uatStatus = (self::$uatStatus == 'Fail') ? self::$uatStatus : $result['status'];
      }
    }
    $data = [];
    foreach($databases as $key => $value){
      $data[$key] = $etlStatus[$key];
    }
    return $data;
  }

  /**
   * @param $message
   * @return bool
   */
  public function gatherData(&$message)
  {
    global $conf;
    if (!self::$message_body) {
      $prodStats = self::getEtlStatistics('PROD');
      $uatStats = self::getEtlStatistics('UAT');
      //$formattedStatus = self::formatStatus();
      self::$successSubject = (self::$prodStatus == 'Fail' || self::$uatStatus == 'Fail') ? 'Fail' : self::$successSubject;
      self::$message_body =
        [
          'uat_stats' => $uatStats,
          'prod_stats' => $prodStats,
          'subject' => self::$successSubject,
        ];
    }

    $msg = [];
    $msg['body'] = self::$message_body;

    $date = self::get_date('Y-m-d');

    //$msg['subject'] = "[{$conf['CHECKBOOK_ENV']}] ETL Status: " . self::$successSubject . " ($date)";
    $msg['subject'] = "ETL Status: " . self::$successSubject . " ($date)";

    $message = array_merge($message, $msg);

    return $message;
  }

  /**
   * @param $data
   * @return string
   */
  /*public function formatStatus()
  {
    global $conf;
    $now = self::timeNow();

    if (!empty($data['success']) && true == $data['success']) {
      $data['hint'] = self::niceDisplayDateDiff($data['data']);

      if (($now - strtotime($data['data'])) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO) {
        $data['hint'] = 'Last success: ' . $data['hint'];
        $data['success'] = false;
        self::$successSubject = 'Fail';
      }
    } else {
      self::$successSubject = 'Fail';
      $data['success'] = false;
      $data['hint'] = 'Could not get data from server';
    }
    return $data;
  }*/

  /**
   * @param $date
   * @return false|string
   */
  public function niceDisplayDateDiff($date)
  {
    if (!$date) {
      return 'never';
    }
    if (is_numeric($date)) {
      $date = date('c', $date);
    }
    $date1 = date_create($date);
    $interval = date_diff($date1, date_create(self::get_date("Y-m-d H:i:s")));
    return $interval->format('%a day(s) %h hour(s) ago');
  }

}
