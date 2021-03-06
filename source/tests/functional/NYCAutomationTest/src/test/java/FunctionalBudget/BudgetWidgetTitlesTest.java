package FunctionalBudget;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;
import org.junit.Assert;
import static org.junit.Assert.*;
import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.spending.SpendingPage;
import pages.budget.BudgetPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

public class BudgetWidgetTitlesTest extends TestStatusReport{
	//public class BudgetWidgetTitlesTest  extends NYCBaseTest{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
    public void GoToPage(){
	   if (!BudgetPage.isAt())
		   BudgetPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
	@Test
    public void VerifyBudgetDomainVisualizationsTitles(){
	    String[] sliderTitles= {"Expense Budget", 
	    						"Fiscal Year Comparisons", 
	    						"Top Ten Expense Categories by Expense Budget", 
	    						"Top Ten Agencies by Expense Budget"};  
    	assertTrue(Arrays.equals(sliderTitles, BudgetPage.BudgetVisualizationTitles().toArray()));
    	//Assert.assertArrayEquals(sliderTitles, BudgetPage.BudgetVisualizationTitles().toArray());
    }
	 
	@Test
    public void VerifyBudgetWidgetTitles(){
	    String[] widgetTitles = {"Top 5 Agencies",
	    						"Top 5 Agencies by Committed Expense Budget",
	    						"Top 5 Agencies by Percent Difference",
	    						"Top 5 Expense Categories",
	    						"Top 5 Expense Categories by Committed Expense Budget",
	    						"Top 5 Expense Categories by Percent Difference"};  
    	//HomePage.ShowWidgetDetails();
     	//assertEquals("Budget Title did not match", widgetTitles,  BudgetPage.WidgetTitles().toArray());
	    System.out.println( BudgetPage.WidgetTitles()); 
		assertTrue(Arrays.equals(widgetTitles, BudgetPage.WidgetTitles().toArray()));
    	
    	/*try {
    		//assertTrue(Arrays.equals(widgetTitles, SpendingPage.GetAllWidgetText().toArray()));
    		System.out.println( BudgetPage.GetAllWidgetText()); 
    		System.out.println( BudgetPage.WidgetTitles()); 
    		assertTrue(Arrays.equals(widgetTitles, BudgetPage.WidgetTitles().toArray()));
    		 
    	    System.out.println("no errors in widget titles");
    	}  catch (Throwable e) {
            System.out.println("errors in widget titles");
            } 
            */
	}
    
	
	
}