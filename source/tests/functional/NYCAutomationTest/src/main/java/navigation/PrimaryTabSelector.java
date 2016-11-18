package navigation;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import utility.Driver;

import java.util.List;

class PrimaryTabSelector {
	static void Select(String primaryTabClass) {
		WebElement TabContainer = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr"));
		List<WebElement> tabs = TabContainer.findElements(By.tagName("td"));
		for (WebElement tab : tabs) {
			String tabclass = tab.getAttribute("class");
			if(tabclass.equals(primaryTabClass+" active")){
				break;
			}else if(tabclass.equals(primaryTabClass)){
				tab.findElement(By.className("expense-container")).click();
				break;
			}
		}
		
	}
}
