<?php
class ComThmGroupsAdministrationHomeEnGb extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost:4444/");
  }

  public function testMyTestCase()
  {
    $this->open("/joomla/administrator/index.php");
    $this->type("id=mod-login-username", "admin");
    $this->type("id=mod-login-password", "adminadmin");
    $this->select("id=lang", "value=en-GB");
    $this->click("link=Log in");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("id=menu-com-thm-groups"));
    $this->assertTrue($this->isElementPresent("link=THM Groups"));
    $this->assertTrue($this->isElementPresent("link=Home"));
    $this->assertTrue($this->isElementPresent("link=Membermanager"));
    $this->assertTrue($this->isElementPresent("link=Groupmanager"));
    $this->assertTrue($this->isElementPresent("link=Rolemanager"));
    $this->assertTrue($this->isElementPresent("link=Structure"));
    $this->click("link=Log out");
    $this->waitForPageToLoad("30000");
  }
}
?>