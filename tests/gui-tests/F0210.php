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
    $this->open("/administrator/index.php?option=com_thm_groups");
    $this->click("css=div.icon");
    $this->waitForPageToLoad("30000");
    $this->click("css=#submenu > li > a");
    $this->waitForPageToLoad("30000");
    $this->click("link=Mitgliedsmanager");
    $this->waitForPageToLoad("30000");
    $this->type("id=search", "TestVorname");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("TestNachname"));
    $this->type("id=search", "TestNachname");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("TestNachname2"));
    $this->type("id=search", "gibt@es.net");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("TestNachname"));
  }
}
?>