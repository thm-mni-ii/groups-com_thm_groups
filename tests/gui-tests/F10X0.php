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
    $this->open("/");
    $this->click("link=FACHBEREICH");
    $this->waitForPageToLoad("30000");
    $this->click("xpath=(//a[contains(text(),'Mitarbeiter')])[2]");
    $this->waitForPageToLoad("30000");
    $this->click("link=TestNachname");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=T"));
    $this->assertTrue($this->isElementPresent("link=TestNachname"));
    $this->assertTrue($this->isElementPresent("xpath=/html/body/div/div[3]/div/div[5]/div[2]/div/div/div/div"));
  }
}
?>