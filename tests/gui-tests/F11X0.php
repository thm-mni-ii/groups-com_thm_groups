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
    $this->open("/administrator/index.php");
    $this->click("link=Hauptmenü");
    $this->waitForPageToLoad("30000");
    $this->click("css=span.icon-32-new");
    $this->waitForPageToLoad("30000");
    $this->click("css=input[type=\"button\"]");
    $this->waitForPageToLoad("30000");
    $this->click("link=THM-Groups - List View");
    $this->waitForPageToLoad("30000");
    $this->click("css=#advanced-options > a > span");
    $this->assertTrue($this->isElementPresent("id=jformparamsselGroup"));
    $this->assertTrue($this->isElementPresent("id=jform_params_showAll-lbl"));
    $this->assertTrue($this->isElementPresent("id=jform_params_linkTarget1"));
    $this->assertTrue($this->isElementPresent("id=jform_params_linkTarget0"));
    $this->assertTrue($this->isElementPresent("id=jform_params_linkTarget-lbl"));
    $this->assertTrue($this->isElementPresent("id=jform[params][alphabet_active_color]"));
    $this->assertTrue($this->isElementPresent("id=jform[params][alphabet_active_font_color]"));
    $this->assertTrue($this->isElementPresent("id=jform_params_lineSpacing"));
    $this->assertTrue($this->isElementPresent("id=jform_params_zSpacing"));
    $this->assertTrue($this->isElementPresent("id=jform[params][alphabet_exists_color]"));
    $this->assertTrue($this->isElementPresent("id=jform[params][alphabet_exists_font_color]"));
  }
}
?>