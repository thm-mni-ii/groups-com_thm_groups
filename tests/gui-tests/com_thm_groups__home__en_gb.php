<?php
class ComThmGroupsHomeEnGb extends JoomlaSeleniumTest
{
	public function testMyTestCase()
	{
		$this->performBackendLogin();

		$this->click("link=THM Groups");
		$this->waitForPageToLoad("30000");
		$this->assertTrue($this->isTextPresent("Home"));
		$this->assertTrue($this->isTextPresent("Membermanager"));
		$this->assertTrue($this->isTextPresent("Groupmanager"));
		$this->assertTrue($this->isTextPresent("Rolemanager"));
		$this->assertTrue($this->isTextPresent("Structure"));
		$this->assertEquals("", $this->getText("css=img[alt=\"Entries Manager\"]"));
		$this->assertEquals("", $this->getText("css=img[alt=\"Group Manager\"]"));
		$this->assertEquals("", $this->getText("css=img[alt=\"Role Manager\"]"));
		$this->assertEquals("", $this->getText("css=img[alt=\"Structure\"]"));
		$this->assertTrue($this->isTextPresent("Membermanager Info"));
		$this->assertTrue($this->isTextPresent("Groupmanager Info"));
		$this->assertTrue($this->isTextPresent("Rolemanager Info"));
		$this->assertTrue($this->isTextPresent("Structure Info"));

		$this->performBackendLogout();
	}
}
?>