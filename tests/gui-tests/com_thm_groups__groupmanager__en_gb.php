<?php
class ComThmGroupsGroupmanagerEnGb extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/");
  }

  public function testMyTestCase()
  {
    $this->open("/joomla/administrator/index.php");
    $this->type("id=mod-login-username", "admin");
    $this->type("id=mod-login-password", "adminadmin");
    $this->select("id=lang", "value=en-GB");
    $this->clickAndWait("link=Log in");
    $this->clickAndWait("link=Groupmanager");
    $this->assertTrue($this->isElementPresent("link=Add Entry"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-moderate"));
    $this->assertTrue($this->isElementPresent("link=Edit Entry"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-edit"));
    $this->assertTrue($this->isElementPresent("link=Delete"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-delete"));
    $this->assertTrue($this->isElementPresent("link=Cancel"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-cancel"));
    $this->assertTrue($this->isElementPresent("link=Back"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-back"));	sleep(1);
    $this->clickAndWait("link=Add Entry");
    $this->type("id=gr_name", "Testgroup");
    $this->removeSelection("id=gr_mode", "label=QUICKPAGE");
    $this->clickAndWait("css=span.icon-32-save");
    $this->assertTrue($this->isTextPresent("Data Saved!"));
    $this->assertTrue($this->isElementPresent("link=Testgroup"));
    $this->assertTrue($this->isTextPresent("profile"));				sleep(1);
    $this->click("id=cb8");
    $this->clickAndWait("css=span.icon-32-delete");
    $this->assertTrue((bool)preg_match('/^Are you sure to delete these groups[\s\S]$/',$this->getConfirmation()));	sleep(1);
    for ($second = 0; ; $second++) {
    	if ($second >= 60) $this->fail("timeout");
    	try {
    		if ($this->isTextPresent("Gruppe konnte nicht entfernt werden")) break;
    	} catch (Exception $e) {
    	}
    	echo $second;
    	sleep(1);
    }
    $this->assertTrue($this->isTextPresent("Gruppe konnte nicht entfernt werden"));	sleep(1);
    $this->assertTrue($this->isElementPresent("link=Testgroup"));
    $this->clickAndWait("link=Groups");
    $this->click("id=cb8");
    $this->clickAndWait("link=Delete");
    $this->assertTrue($this->isTextPresent("One User Group successfully deleted"));
    $this->clickAndWait("link=Groupmanager");
    $this->assertFalse($this->isElementPresent("link=Testgroup"));
    $this->clickAndWait("link=Log out");
  }
}
?>