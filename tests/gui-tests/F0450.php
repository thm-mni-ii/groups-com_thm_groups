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
    $this->open("/administrator/index.php?option=com_thm_groups&view=groupmanager");
    $this->click("css=span.icon-32-moderate");
    $this->waitForPageToLoad("30000");
    $this->type("id=gr_name", "Testgruppe");
    $this->click("link=Speichern & Schließen");
    $this->waitForPageToLoad("30000");
    $this->select("id=limit", "label=alle");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Testgruppe"));
    $this->click("id=cb62");
    $this->click("css=span.icon-32-delete");
    $this->assertTrue((bool)preg_match('/^Sind Sie sicher, dass diese Gruppe\(n\) gelöscht werden soll\(en\)[\s\S]$/',$this->getConfirmation()));
    $this->assertFalse($this->isTextPresent("Testgruppe"));
  }
}
?>