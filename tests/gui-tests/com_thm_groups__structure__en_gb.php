<?php
class ComThmGroupsStructureEnGb extends PHPUnit_Extensions_SeleniumTestCase
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
    $this->clickAndWait("link=Structure");
    $this->assertTrue($this->isElementPresent("link=Add Entry"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-moderate"));
    $this->assertTrue($this->isElementPresent("link=Edit Entry"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-edit"));
    $this->assertTrue($this->isElementPresent("link=Delete"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-delete"));
    $this->assertTrue($this->isElementPresent("link=Cancel"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-cancel"));
    $this->assertTrue($this->isElementPresent("link=Back"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-back"));
    $this->assertTrue($this->isTextPresent("Vorname"));
    $this->assertTrue($this->isTextPresent("Nachname"));
    $this->assertTrue($this->isTextPresent("Username"));
    $this->assertTrue($this->isTextPresent("EMail"));
    $this->assertTrue($this->isTextPresent("Titel"));
    $this->assertTrue($this->isTextPresent("Mode"));
    $this->clickAndWait("link=Add Entry");
    $this->assertTrue($this->isElementPresent("id=name"));
    $this->type("id=name", "Testfield");
    $this->assertTrue($this->isElementPresent("id=relation"));
    $this->select("id=relation", "label=text");
    $this->waitForCondition("selenium.browserbot.getCurrentWindow().document.getElementById('TEXT_extra')");
    $this->type("id=TEXT_extra", "50");
    $this->clickAndWait("link=Save & Close");
    $this->assertTrue($this->isTextPresent("Data Saved!"));
    $this->assertTrue($this->isElementPresent("link=Testfield"));
    $this->clickAndWait("link=Testfield");
    $this->type("id=name", "Testfield_test");
    $this->clickAndWait("link=Save & Close");
    $this->assertTrue($this->isTextPresent("Data Saved!"));
    $this->click("id=cb6");
    $this->click("link=Delete");
    $this->assertEquals("Role deleted", $this->getConfirmation());	sleep(1);
    $this->assertFalse($this->isElementPresent("link=Testfield_test"));				sleep(1);
    $this->clickAndWait("link=Log out");
  }
}
?>