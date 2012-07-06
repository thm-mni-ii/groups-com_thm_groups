<?php
class ComThmGroupsAdministrationHomeEnGb extends JoomlaSeleniumTest
{
	public function testMyTestCase()
	{
		$this->performBackendLogin();
		
		$this->assertTrue($this->isElementPresent("id=menu-com-thm-groups"));
		$this->assertTrue($this->isElementPresent("link=THM Groups"));
		$this->assertTrue($this->isElementPresent("link=Home"));
		$this->assertTrue($this->isElementPresent("link=Membermanager"));
		$this->assertTrue($this->isElementPresent("link=Groupmanager"));
		$this->assertTrue($this->isElementPresent("link=Rolemanager"));
		$this->assertTrue($this->isElementPresent("link=Structure"));
		
		$this->performBackendLogout();
	}
}
?>