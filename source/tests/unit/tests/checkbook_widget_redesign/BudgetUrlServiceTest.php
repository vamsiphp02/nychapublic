<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/budget/BudgetUrlService.php';

use PHPUnit\Framework\TestCase;

/**
 * Class BudgetUrlServiceTest
 */
class BudgetUrlServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getFooterUrl() function
     */
    public function test_getFooterUrl()
    {
        $result = BudgetUrlService::getFooterUrl(null, 558);
        $this->assertEquals("/panel_html/budget_transactions/budget/transactions/dtsmnid/558", substr($result, 0, 63));

        $result = BudgetUrlService::getFooterUrl();
        $this->assertEquals("/panel_html/budget_transactions/budget/transactions", substr($result, 0, 51));
    }

    /**
     * Tests getPercentDiffFooterUrl() function
     */
    public function test_getPercentDiffFooterUrl()
    {
        $result = BudgetUrlService::getPercentDiffFooterUrl(BudgetUrlService::getFooterUrl(), 'agency_details');
        $this->assertEquals("/panel_html/budget_agency_perecent_difference_transactions/budget/agency_details", substr($result, 0, 80));

        $result = BudgetUrlService::getPercentDiffFooterUrl(BudgetUrlService::getFooterUrl());
        $this->assertEquals("/panel_html/budget_transactions/budget/transactions", substr($result, 0, 51));
    }

    /**
     * Tests committedBudgetUrl() function
     */
    public function test_committedBudgetUrl()
    {
        $result = BudgetUrlService::committedBudgetUrl();
        $this->assertEquals("/panel_html/budget_transactions/budget/transactions", substr($result, 0, 51));

        $result = BudgetUrlService::committedBudgetUrl(NULL, 558);
        $this->assertEquals("/panel_html/budget_transactions/budget/transactions/smnid/558", substr($result, 0, 61));
    }

    /**
     * Tests departmentUrl() function
     */
    public function test_departmentUrl(){
        $result = BudgetUrlService::departmentUrl(001);
        $this->assertEquals("/dept/001", substr($result, -9));
    }

     /**
     * Tests expenseCategoryUrl() function
     */
    public function test_expenseCategoryUrl(){
        $result = BudgetUrlService::expenseCategoryUrl(388);
        $this->assertEquals("/expcategory/388", substr($result, -16));
    }

     /**
     * Tests agencyNameUrl() function
     */
    public function test_agencyNameUrl(){
        $result = BudgetUrlService::agencyNameUrl(198);
        $this->assertEquals("/agency/198", substr($result, -12));
    }
}