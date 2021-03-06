package utilities;

import org.junit.Rule;
import org.junit.rules.TestRule;
import org.junit.rules.TestWatcher;
import org.junit.runner.Description;
import org.junit.runners.model.Statement;

import utilities.NYCBaseTest;
import utilities.OGENYCBaseTest;
import utilities.TestMethodResults;
import utilities.InterfaceExcel;

public class TestStatusReport {
	
	@Rule
	public TestRule watchman = new TestWatcher() {
		@Override
		public Statement apply(Statement base, Description description){
			return super.apply(base, description);
		}
		
		@Override
		protected void succeeded(Description description) {
			try {
				NYCBaseTest.writer.write(description.getClassName()+" - "+description.getMethodName() + " "
						+ "success!");
				NYCBaseTest.writer.write("<br/>");
				
				String testName = description.getClassName()+" - "+description.getMethodName();
				String testResults = " ";// description.
				boolean testPassed = true;
				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(testName, testResults, testPassed));
				
//				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(description.getClassName()+" - "+description.getMethodName(), " " ,true));
			} catch (Exception e1) {
				System.out.println(e1.getMessage());
			}
			try {
				OGENYCBaseTest.writer.write(description.getClassName()+" - "+description.getMethodName() + " "
						+ "success!");
				OGENYCBaseTest.writer.write("<br/>");
				
				String testName = description.getClassName()+" - "+description.getMethodName();
				String testResults = " ";// description.
				boolean testPassed = true;
				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(testName, testResults, testPassed));
				
//				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(description.getClassName()+" - "+description.getMethodName(), " " ,true));
			} catch (Exception e1) {
				System.out.println(e1.getMessage());
			}
		}
 
		@Override
		protected void failed(Throwable e, Description description) {
			try {
				NYCBaseTest.writer.write(description.getClassName()+" - "+description.getMethodName()+ " "
						+ e.getClass().getSimpleName());
				NYCBaseTest.writer.write("<br/>");
//				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(description.getClassName()+" - "+description.getMethodName(), e.getClass().getSimpleName(),false));

				
				String testName = description.getClassName()+" - "+description.getMethodName();
				String testResults = e.getMessage();
				boolean testPassed = false;
				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(testName, testResults, testPassed));

			} catch (Exception e2) {
				System.out.println(e2.getMessage());
			}
			
			try {
				OGENYCBaseTest.writer.write(description.getClassName()+" - "+description.getMethodName()+ " "
						+ e.getClass().getSimpleName());
				OGENYCBaseTest.writer.write("<br/>");
//				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(description.getClassName()+" - "+description.getMethodName(), e.getClass().getSimpleName(),false));

				
				String testName = description.getClassName()+" - "+description.getMethodName();
				String testResults = e.getMessage();
				boolean testPassed = false;
				InterfaceExcel.AddTestResultsToExcelFile(new TestMethodResults(testName, testResults, testPassed));

			} catch (Exception e2) {
				System.out.println(e2.getMessage());
			}
		}
	};
}
