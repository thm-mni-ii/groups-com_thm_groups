<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://127.0.0.1/joomla");
  }

  public function testMyTestCase()
  {
    $this->open("/");
    $this->type("id=username", "abkr85");
    $this->type("id=password", "testuser");
    $this->click("name=Submit");
    $this->waitForPageToLoad("30000");
    $this->click("link=FACHBEREICH");
    $this->waitForPageToLoad("30000");
    $this->click("xpath=(//a[contains(text(),'Mitarbeiter')])[2]");
    $this->waitForPageToLoad("30000");
    $this->click("link=Becker");
    $this->waitForPageToLoad("30000");
    $this->click("css=img[alt=\"bearbeiten\"]");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("id=Titel"));
    $this->assertTrue($this->isElementPresent("id=Vorname"));
    $this->assertTrue($this->isElementPresent("id=Nachname"));
    $this->assertTrue($this->isElementPresent("id=Username"));
    $this->assertTrue($this->isElementPresent("id=EMail"));
    $this->assertTrue($this->isElementPresent("id=Website"));
    $this->assertTrue($this->isElementPresent("id=Mode"));
    $this->assertTrue($this->isElementPresent("id=Tel"));
    $this->assertTrue($this->isElementPresent("id=Sprechzeiten"));
  }
}
?>