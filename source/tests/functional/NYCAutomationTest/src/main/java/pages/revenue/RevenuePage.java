package pages.revenue;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;
import pages.home.HomePage;



public class RevenuePage {	

	public enum WidgetOption{
		 Top5Agencies, TopAgencies ,Top5RevenueCategories,TopRevenueCategories,RevenuebyFundingClasses, Top5AgenciesbyCrossYearCollections,TopAgenciesbyCrossYearCollections,
		 Top5RevenueCategoriesbyCrossYearCollections, TopRevenueCategoriesbyCrossYearCollections,RevenuebyFundingClassesbyCrossYearCollections
	}
	public static void GoTo() {
		navigation.TopNavigation.Revenue.Select();
	}
		
		   public static String GetRevenueAmount() {
	            WebElement revenueAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .revenue > .expense-container > a"));
	            return revenueAmt.getText().substring((revenueAmt.getText().indexOf("$")));
	        }
    
	public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .revenue"));
    	Boolean revenueSelected = (topTitleCont.getAttribute("class")).contains("active");	
        //WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));
        //Boolean totalSpendingSelected = h2title.getText().equals("Total Spending");    
        return revenueSelected;
    }
	
	public static Integer GetTop5WidgetTotalCount(WidgetOption option) {
		switch (option) {
			case Top5Agencies:
				return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
			case TopAgencies:
				return HomePage.GetWidgetTotalNumber("Top Agencies");
			case Top5RevenueCategories:	
				return HomePage.GetWidgetTotalNumber("Top 5 Revenue Categories");
			case TopRevenueCategories:
				return HomePage.GetWidgetTotalNumber("Top Revenue Categories");
			case RevenuebyFundingClasses:
				return HomePage.GetWidgetTotalNumber("Revenue by Funding Classes");
			case Top5AgenciesbyCrossYearCollections:
				return HomePage.GetWidgetTotalNumber("Top 5 Agencies by Cross Year Collections");
			case TopAgenciesbyCrossYearCollections:
				return HomePage.GetWidgetTotalNumber("Top Agencies by Cross Year Collections");
			case Top5RevenueCategoriesbyCrossYearCollections:
				return HomePage.GetWidgetTotalNumber("Top 5 Revenue Categories by Cross Year Collections");
			case TopRevenueCategoriesbyCrossYearCollections:
				return HomePage.GetWidgetTotalNumber("Top Revenue Categories by Cross Year Collections");
			case RevenuebyFundingClassesbyCrossYearCollections:
				return HomePage.GetWidgetTotalNumber("Revenue by Funding Classes by Cross Year Collections");			
			default:		
				return null;
		}
	}
	
	public static void GoToTop5DetailsPage(WidgetOption option) {
		WebElement detailsContainer = null;
		switch (option) {
			case Top5Agencies:	
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies");
				break;
			case TopAgencies:
				if(!HomePage.IsAtTop5DetailsPage("Top Agencies"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies");
				break;
			case Top5RevenueCategories:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Revenue Categories"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Revenue Categories");
				break;
			case TopRevenueCategories:
				if(!HomePage.IsAtTop5DetailsPage("Top Revenue Categories"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Revenue Categories");
				break;
		
			case RevenuebyFundingClasses:
				if(!HomePage.IsAtTop5DetailsPage("Revenue by Funding Classes"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Contracts Amount Modifications");
				break;
			case Top5AgenciesbyCrossYearCollections:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies by Cross Year Collections"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies by Cross Year Collections");
				break;
			case TopAgenciesbyCrossYearCollections:
				if(!HomePage.IsAtTop5DetailsPage("TopAgenciesbyCrossYearCollections"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("TopAgenciesbyCrossYearCollections");
				break;
			case Top5RevenueCategoriesbyCrossYearCollections:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Revenue Categories by Cross Year Collections"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Revenue Categories by Cross Year Collections");
				break;
			case TopRevenueCategoriesbyCrossYearCollections:
				if(!HomePage.IsAtTop5DetailsPage("Top Revenue Categories by Cross Year Collections"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Contracts Amount Modifications");
				break;
			case RevenuebyFundingClassesbyCrossYearCollections:
				if(!HomePage.IsAtTop5DetailsPage("Revenue by Funding Classes by Cross Year Collections"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Revenue by Funding Classes by Cross Year Collections");
				break;
		
			default:
				break;
		}
		WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
		detailsAnchor.click();	
		Driver.Instance.manage().timeouts().implicitlyWait(20, TimeUnit.SECONDS);
	}
	// details  page Transaction table  count
	
	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 10);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_280_info")));
		String count = (Driver.Instance.findElement(By.id("table_280_info"))).getText();
		return Helper.GetTotalEntries(count, 9);
	}
	
	
	///widget visualization titles   
	public static ArrayList<String> RevenueVisualizationTitles() {
		
		return HomePage.RevenueVisualizationTitles();
	}

	//////widget  titles   
	public static ArrayList<String> WidgetTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.className("tableHeader"));
		for (WebElement titleContainer : titleContainers) {
			WebElement titleHeaderContainer = titleContainer.findElement(By.cssSelector("h2"));
			titles.add(titleHeaderContainer.getText());
		}	
		return titles;
	}
}
