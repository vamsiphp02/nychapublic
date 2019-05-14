<?php

class NychaSpendingDataService extends DataService implements INychaSpendingDataService {

  /* Citywide Spending */
  function GetNychaSpendingByChecks($parameters, $limit = null, $orderBy = null) {
    return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
  }
    function  GetNychaSpendingByVendors($parameters, $limit = null, $orderBy = null){
        return $this->configureNycha(__FUNCTION__,$parameters,$limit,$orderBy);
    }
    function GetCountVendors($parameters) {
        return $this->configureNycha(__FUNCTION__,$parameters);
    }

  /**
   * Common function that automatically configures the Citywide Spending sql
   * @param $dataFunction
   * @param $parameters
   * @param null $limit
   * @param null $orderBy
   * @return DataService
   */
  private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null) {
    return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaSpending);
  }
}
