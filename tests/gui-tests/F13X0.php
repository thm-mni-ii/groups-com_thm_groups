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
    $this->open("/administrator/index.php?option=com_menus&view=items");
    $this->click("css=span.icon-32-new");
    $this->waitForPageToLoad("30000");
    $this->click("css=input[type=\"button\"]");
    $this->waitForPageToLoad("30000");
    $this->click("link=THM Groups - Advanced List View");
    $this->waitForPageToLoad("30000");
    $this->selectWindow("null");
    $this->assertTrue($this->isElementPresent("id=jform_params_lineSpacing"));
    $this->assertTrue($this->isElementPresent("id=jform_params_zSpacing"));
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"jform[params][struct]\"]"));
  }
}
?>