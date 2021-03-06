package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import utilities.TestStatusReport;
public class MWBERegisteredExpenseContractsDetailsTest extends TestStatusReport{
	//	public class MWBERegisteredExpenseContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		//if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			navigation.TopNavigation.Contracts.RegisteredExpenseContracts.Select();	
		//}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}

	/* ***************** Test Widget Transaction Total Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB = NYCDatabaseUtil.getMWBEREContractsMasterDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals(" Registered Expense master contracts widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Master Agreements Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsMasterContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Master Agreement  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	     
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB = NYCDatabaseUtil.getMWBEREContractsMasterModificationsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals(" Registered Expense master Contracts modification widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Master Agreement Modifications Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
		assertEquals("Registered Expense Contracts Master Agreement Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
		    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsMasterModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Master Agreement Modifications  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB = NYCDatabaseUtil.getMWBEREAllContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals(" Registered Expense Contracts  widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB = NYCDatabaseUtil.getMWBEREContractsModificationsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals(" REgistered Expense Contracts modification widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contract Amount Modifications Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered Expense Contracts ContractAmountModifications  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEREAll1ContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals(" Registered Expense Contracts Prime Vendors widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered Expense Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEREAll1ContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals(" Registered Expense Contracts Award Method widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Award Methods Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts AwardMethods  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEREAll1ContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("  Registered Expense Contracts Agencies widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Agencies Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEREAll1ContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("  Registered Expense Contracts by Industies widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Industries Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEREAll1ContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals(" Registered Expense Contracts by size  widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Size Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBEREContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts ContractsBySize  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}

	/* ***************** Test Widget Transaction Count ****************** */
	/*
	 
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }

	/* ***************** Test Widget Transaction Amount *************** 
	@Test
	public void VerifyTop5MasterAgreementsTransactionAmount(){
		Float transactionAmt = 6.16f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);}

	*/

}
