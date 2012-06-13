<?php
class F0010 extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://127.0.0.1/joomla");
  }

  public function testMyTestCase()
  {
    $this->open("/administrator/");
    $this->type("id=mod-login-username", "admin");
    $this->type("id=mod-login-password", "adminadmin");
    $this->click("css=input.hidebtn");
    $this->waitForPageToLoad("30000");
    $this->click("link=THM Groups");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("css=h2"));
  }
}
?>