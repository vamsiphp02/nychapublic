package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import utilities.TestStatusReport;
public class MWBEActiveRevenueContractsDetailsTest extends TestStatusReport{

	//public class MWBEActiveRevenueContractsDetailsTest extends NYCBaseTest{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before

	
	public void GoToPage(){
		//if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			navigation.TopNavigation.Contracts.ActiveRevenueContracts.Select();	
		//}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}


	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsModificationsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Modifications widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contract Amount Modifications Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	       
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount(); 
		assertEquals("Active Revenue Contracts Prime Vendors  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Prime Vendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Award Method  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Award Methods Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Award Method Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Agencies  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Agencies Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Industries  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Industries Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Industries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts size  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Size Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Contracts by Size Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
		
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Size Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}


/*
	@Test
	public void VerifyContractsBySizeTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	
	*/
}
