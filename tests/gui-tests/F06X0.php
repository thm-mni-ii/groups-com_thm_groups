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
    $this->open("/administrator/index.php?option=com_thm_groups&view=rolemanager");
    $this->click("link=Eintrag hinzufügen");
    $this->waitForPageToLoad("30000");
    $this->type("id=role_name", "Testrolle");
    $this->click("link=Speichern & Schließen");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=Testrolle"));
    $this->click("id=cb19");
    $this->click("link=Löschen");
    $this->assertEquals("COM_THM_GROUPS_REALLY_DELETE", $this->getConfirmation());
    $this->assertFalse($this->isElementPresent("link=Testrolle"));
  }
}
?>