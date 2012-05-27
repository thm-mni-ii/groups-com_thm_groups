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
    $this->open("/administrator/index.php?option=com_thm_groups&view=editgroup&task=groupmanager.edit&cid=1");
    $this->assertTrue($this->isElementPresent("css=span.icon-32-apply"));
    $this->assertTrue($this->isElementPresent("link=Speichern & Schließen"));
    $this->assertTrue($this->isElementPresent("link=Speichern & Neu"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-cancel"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-back"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-back"));
  }
}
?>