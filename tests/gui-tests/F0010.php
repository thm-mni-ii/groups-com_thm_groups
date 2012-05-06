<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://webmedia06.mni.fh-giessen.de/");
  }

  public function testMyTestCase()
  {
    $this->open("/administrator/");
    $this->type("id=mod-login-username", "abkr85");
    $this->type("id=mod-login-password", "testuser");
    $this->click("css=input.hidebtn");
    $this->waitForPageToLoad("30000");
    $this->click("link=THM Groups");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("css=h2"));
  }
}
?>