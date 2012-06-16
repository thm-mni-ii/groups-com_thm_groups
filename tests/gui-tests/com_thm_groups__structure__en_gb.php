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
    $this->click("link=Log in");
    $this->waitForPageToLoad("30000");
    $this->click("link=Structure");
    $this->waitForPageToLoad("30000");
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
    $this->click("link=Add Entry");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("id=name"));
    $this->type("id=name", "Testfield");
    $this->assertTrue($this->isElementPresent("id=relation"));
    $this->select("id=relation", "label=text");
    $this->assertTrue($this->isElementPresent("id=TEXT_extra"));
    $this->type("id=TEXT_extra", "50");
    $this->click("link=Save & Close");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Data Saved!"));
    $this->assertTrue($this->isElementPresent("link=Testfield"));
    $this->click("link=Testfield");
    $this->waitForPageToLoad("30000");
    $this->type("id=name", "Testfield_test");
    $this->click("link=Save & Close");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Data Saved!"));
    $this->click("id=cb6");
    $this->click("link=Delete");
    $this->assertEquals("COM_THM_GROUPS_REALLY_DELETE", $this->getConfirmation());
    $this->assertFalse($this->isElementPresent("link=Testfield_test"));
    $this->click("link=Log out");
    $this->waitForPageToLoad("30000");
  }
}
?>