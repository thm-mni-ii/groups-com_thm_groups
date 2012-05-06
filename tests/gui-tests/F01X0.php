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
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"roles\"]"));
    $this->assertTrue($this->isElementPresent("xpath=//*[@id=\"groups\"]"));
    $this->assertTrue($this->isElementPresent("xpath=/html/body/div[3]/div/div/div[5]/div[2]/form/div[2]/table"));
    $this->assertTrue($this->isElementPresent("css=html body#minwidth-body div#content-box div.border div.padding div#toolbar-box div.m div#toolbar.toolbar-list ul li#toolbar-moderate.button a.toolbar"));
    $this->assertTrue($this->isElementPresent("css=html body#minwidth-body div#content-box div.border div.padding div#toolbar-box div.m div#toolbar.toolbar-list ul li#toolbar-unmoderate.button a.toolbar"));
    $this->type("id=search", "TestNachname");
    $this->click("css=span.state.publish");
    $this->waitForPageToLoad("30000");
    $this->click("css=span.state.unpublish");
    $this->waitForPageToLoad("30000");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("//div[@id='editcell']/table/tbody/tr/td[8]/a/span"));
  }
}
?>