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
    $this->open("/administrator/index.php?option=com_thm_groups");
    $this->click("css=div.icon");
    $this->waitForPageToLoad("30000");
    $this->click("css=#submenu > li > a");
    $this->waitForPageToLoad("30000");
    $this->click("link=Mitgliedsmanager");
    $this->waitForPageToLoad("30000");
    $this->select("id=groupFilters", "label=- - - - team (I-MSc)");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
    $this->select("id=limit", "label=alle");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("TestNachname2"));
    $this->select("id=groupFilters", "label=- - - - - core (I-MSc)");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("TestNachname"));
    $this->select("id=rolesFilters", "label=Mitglied");
    $this->select("id=groupFilters", "label=Alle");
    $this->click("css=button");
    $this->waitForPageToLoad("30000");
  }
}
?>