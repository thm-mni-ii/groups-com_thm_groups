<?php
class ComThmGroupsHomeEnGb extends PHPUnit_Extensions_SeleniumTestCase
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
    $this->click("link=Log out");
    $this->waitForPageToLoad("30000");
  }
}
?>