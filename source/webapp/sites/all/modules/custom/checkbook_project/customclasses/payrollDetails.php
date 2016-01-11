<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 11/3/15
 * Time: 3:57 PM
 */

class payrollDetails {

    public function getData(&$node){

        $year = _getRequestParamValue("year");
        $yeartype = _getRequestParamValue("yeartype");
        $title = _getRequestParamValue("title");
        $agency = _getRequestParamValue("agency");

        $where = $sub_query_where = $agency_select = "";
        if(isset($year)) {
            $where .= $where == "" ? "WHERE fiscal_year_id = $year" : " AND fiscal_year_id = $year";
        }
        if(isset($yeartype)) {
            $where .= $where == "" ? "WHERE type_of_year = '$yeartype'" : " AND type_of_year = '$yeartype'";
        }
        $sub_query_where = $where;
        if(isset($title)) {
            $where .= $where == "" ? "WHERE civil_service_title = '$title'" : " AND civil_service_title = '$title'";
        }
        $dataset = 'aggregateon_payroll_employment_type_nyc';
        if(isset($agency)) {
            $dataset = 'aggregateon_payroll_employment_type';

            $where .= $where == "" ? "WHERE agency.agency_id = '$agency'" : " AND agency.agency_id = '$agency'";
            $sub_query_where .= $sub_query_where == "" ? "WHERE agency_id = '$agency'" : " AND agency_id = '$agency'";

            $ref_agency_join = "JOIN ref_agency agency ON agency.agency_id = emp.agency_id";
            $agency_select = ",agency_id, agency_name";
            $agency_sub_select = ",agency.agency_id, agency.agency_name";
            $agency_id_sub_select = ",agency_id AS agency_id_1";
            $agency_join = ' AND emp_type.agency_id_1 = emp.agency_id';
            $agency_sub_group_by = ',agency.agency_id,agency.agency_name';
            $agency_group_by = ',agency_id,agency_name';
        }

        $query = "
            SELECT
                type_of_employment,
                type_of_year,
                fiscal_year_id,
                SUM(total_annual_salary) as total_annual_salary,
                SUM(total_gross_pay) as total_gross_pay,
                SUM(total_base_pay) as total_base_pay,
                SUM(total_other_payments) as total_other_payments,
                SUM(total_overtime_pay) as total_overtime_pay,
                CASE WHEN type_of_employment = 'Salaried' THEN SUM(total_salaried_employees) ELSE SUM(total_non_salaried_employees) END AS number_employees
                {$agency_select}
            FROM (
                    SELECT
                    emp.type_of_year,
                    emp.fiscal_year_id,
                    SUM(emp.gross_pay) AS total_gross_pay,
                    SUM(emp.base_pay) AS total_base_pay,
                    SUM(emp.other_payments) AS total_other_payments,
                    SUM(emp.overtime_pay) AS total_overtime_pay,
                    MAX(emp.annual_salary) AS total_annual_salary,
                    COUNT(DISTINCT (CASE WHEN emp_type.type_of_employment = 'Salaried' THEN emp_type.employee_number_1 END)) AS total_salaried_employees,
                    COUNT(DISTINCT (CASE WHEN emp_type.type_of_employment = 'Non-Salaried' THEN emp_type.employee_number_1 END)) AS total_non_salaried_employees,
                    emp.employee_number,
                    emp_type.type_of_employment
                    {$agency_sub_select}
                    FROM aggregateon_payroll_employee_agency emp
                    {$ref_agency_join}
                    JOIN
                    (
                        SELECT
                        type_of_employment AS type_of_employment,
                        employee_number AS employee_number_1,
                        type_of_year AS type_of_year_1,
                        fiscal_year_id AS fiscal_year_id_1
                        {$agency_id_sub_select}
                        FROM {$dataset}
                        {$sub_query_where}
                    ) emp_type ON emp_type.employee_number_1 = emp.employee_number
                    AND emp_type.type_of_year_1 = emp.type_of_year
                    AND emp_type.fiscal_year_id_1 = emp.fiscal_year_id
                    {$agency_join}
                    {$where}
                    GROUP BY emp.fiscal_year_id, emp.type_of_year, emp.employee_number, emp_type.type_of_employment
                    {$agency_sub_group_by}
                ) employees
                GROUP BY type_of_employment, type_of_year, fiscal_year_id
                {$agency_group_by}
    ";

//        log_error('QUERY:' .$query);
        $results = _checkbook_project_execute_sql_by_data_source($query,"checkbook");
        $total_employees = 0;
        foreach($results as $result){
            $total_employees += $result['number_employees'];
        }
        $node->data = $results;
        $node->total_employees = $total_employees;
    }

}