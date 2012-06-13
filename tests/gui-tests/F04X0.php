<?php
class F04X0 extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://127.0.0.1/joomla");
  }

  public function testMyTestCase()
  {
    $this->open("/administrator/index.php?option=com_thm_groups&view=groupmanager#");
    $this->click("css=span.icon-32-moderate");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"gr_name\"]"));
    $this->assertTrue($this->isElementPresent("xpath=/html/body/div[3]/div/div/div[4]/div[2]/form/div/fieldset/table/tbody/tr[2]/td[2]/select"));
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"groupinfo_tbl\"]"));
    $this->assertTrue($this->isElementPresent("//div[@id='element-box']/div[2]/form/div/fieldset/table/tbody/tr[4]/td[2]/img"));
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"gr_picture\"]"));
  }
}
?>