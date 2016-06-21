<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 11:33 AM
 */

class SqlUtil {

    public function buildExecuteSqlQuery($parameters, $limit, $order_by, $sqlConfigName, $statementName) {

        try {

            $model = SqlModelFactory::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName);
            log_info("SQL Statement Name: ".$statementName."\nSQL Trace:\n".$model->query."\n");
            $results = self::executeSqlQuery($model);
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::buildExecuteSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    public function getSqlQuery($parameters, $limit, $order_by, $sqlConfigName, $statementName) {

        try {
            $model = self::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName);
            $query = $model->query;
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::getSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $query;
    }

    public function getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName) {

        try {
            $model = SqlModelFactory::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName);
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::getSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $model;
    }

    public static function executeSqlQuery(sqlStatementModel $model) {

        try {
            $results = self::executeSqlFetchAssoc($model->query, $model->datasource);
            log_info("SQL Statement Name: ".$model->name."\nSQL Trace:\n".$model->query."\n");
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::executeSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    public function executeCountSqlQuery(sqlStatementModel $model) {

        try {
            $results = self::executeSqlFetchAssoc($model->countQuery, $model->datasource);
            log_info("SQL Statement Name: ".$model->name."\nSQL Trace:\n".$model->countQuery."\n");
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::executeCountSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    public function executeSqlFetchAssoc($query, $data_source = 'checkbook', $db_name = "main")
    {
        $results = null;

        //Get connection
        try {
            $connection = Database::getConnection($db_name, $data_source);
        }
        catch(Exception $e) {
            LogHelper::log_error("Error getting connection. Exception is :".$e);
            throw $e;
        }

        //execute SQL
        try {
            $results = $connection->query($query);
        }
        catch (Exception $e) {
            LogHelper::log_error("Error executing DB query for generating: " . $query . ". Exception is: " . $e);
            throw $e;
        }

        return $results;
    }
} 