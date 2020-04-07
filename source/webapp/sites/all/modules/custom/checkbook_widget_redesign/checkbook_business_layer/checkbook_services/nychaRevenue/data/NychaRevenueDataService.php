<?php

class NychaRevenueDataService extends DataService implements INyhcaRevenueDataService {

    /**
     * Common function that automatically configures the Nycha Budget sql
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @return DataService
     */
    private function configureNycha($dataFunction, $parameters, $limit = null, $orderBy = null) {
        return $this->configure($dataFunction,$parameters,$limit,$orderBy,SqlConfigPath::NychaRevenue);
    }
}
