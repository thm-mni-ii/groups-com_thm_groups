<?php
class ComThmGroupsRolemanagerEnGb extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/");
  }

  public function testMyTestCase()
  {
    $this->open("/joomla25/administrator/index.php");
    $this->type("id=mod-login-username", "admin");
    $this->type("id=mod-login-password", "adminadmin");
    $this->select("id=lang", "value=en-GB");
    $this->click("link=Log in");
    $this->waitForPageToLoad("30000");
    $this->click("link=Rolemanager");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=Add Entry"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-moderate"));
    $this->assertTrue($this->isElementPresent("link=Edit Entry"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-edit"));
    $this->assertTrue($this->isElementPresent("link=Delete"));
    $this->assertEquals("", $this->getText("css=span.icon-32-delete"));
    $this->assertTrue($this->isElementPresent("link=Cancel"));
    $this->assertEquals("", $this->getText("css=span.icon-32-cancel"));
    sleep(1);
    $this->assertTrue($this->isElementPresent("link=Back"));
    sleep(1);
    $this->assertTrue($this->isElementPresent("css=span.icon-32-back"));
    sleep(1);
    $this->assertTrue($this->isElementPresent("link=Mitglied"));
    sleep(1);
    $this->assertTrue($this->isElementPresent("link=Moderator"));
    sleep(1);
    $this->click("css=span.icon-32-moderate");
    $this->waitForPageToLoad("30000");
    $this->type("id=role_name", "Testrole");
    $this->click("css=span.icon-32-save");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Data Saved!"));
    sleep(1);
    $this->assertTrue($this->isElementPresent("link=Testrole"));
    sleep(1);
    $this->click("id=cb2");
    $this->click("link=Delete");
    $this->assertEquals("Role deleted", $this->getConfirmation());
    sleep(1);
    $this->assertTrue($this->isTextPresent("Rolle(n) erfolgreich entfernt"));
    $this->assertFalse($this->isElementPresent("link=Testrole"));
    $this->click("link=Log out");
    $this->waitForPageToLoad("30000");
  }
}
?>