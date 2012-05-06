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
    $this->open("/administrator/index.php?option=com_thm_groups&view=membermanager");
    $this->click("xpath=(//a[contains(text(),'Gruppenmanager')])[2]");
    $this->waitForPageToLoad("30000");
    $this->click("link=Eintrag hinzufügen");
    $this->waitForPageToLoad("30000");
    $this->selectWindow("null");
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"gr_name\"]"));
    $this->assertTrue($this->isElementPresent("name=gr_parent"));
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"gr_mode\"]"));
    $this->click("link=Abbrechen");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("xpath=/html/body/div[3]/div/div/div[5]/div[2]/form/div/table"));
  }
}
?>