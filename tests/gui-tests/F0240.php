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
    $this->open("/administrator/index.php");
    $this->click("link=Titel");
    $this->waitForPageToLoad("30000");
    $this->click("link=Nachname");
    $this->waitForPageToLoad("30000");
    $this->click("link=Vorname");
    $this->waitForPageToLoad("30000");
    $this->click("link=Gruppe(n)/Rolle(n)");
    $this->waitForPageToLoad("30000");
  }
}
?>