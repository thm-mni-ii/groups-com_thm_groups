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
    $this->open("/administrator/index.php?option=com_thm_groups&view=structure");
    $this->click("link=Test");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("id=name"));
    $this->assertTrue($this->isElementPresent("id=relation"));
    $this->assertTrue($this->isElementPresent("name=TABLE_extra"));
  }
}
?>