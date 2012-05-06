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
    $this->open("/administrator/index.php?option=com_thm_groups&view=structure");
    $this->assertFalse($this->isElementPresent("link=Vorname"));
    $this->assertFalse($this->isElementPresent("link=Nachname"));
    $this->assertFalse($this->isElementPresent("link=Username"));
    $this->assertFalse($this->isElementPresent("link=EMail"));
    $this->assertFalse($this->isElementPresent("link=Mode"));
    $this->assertFalse($this->isElementPresent("link=Titel"));
    $this->click("link=Eintrag hinzufügen");
    $this->waitForPageToLoad("30000");
    $this->type("id=name", "Testitem");
    $this->select("id=relation", "label=number");
    $this->click("link=Speichern & Schließen");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=Testitem"));
    $this->assertTrue($this->isTextPresent("NUMBER"));
    $this->click("id=cb15");
    $this->click("css=span.icon-32-delete");
    $this->assertEquals("COM_THM_GROUPS_REALLY_DELETE", $this->getConfirmation());
    $this->assertFalse($this->isElementPresent("link=Testitem"));
  }
}
?>