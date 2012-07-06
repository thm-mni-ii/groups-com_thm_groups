<?php
class ComThmGroupsMembermanager2EnGb extends JoomlaSeleniumTest
{
  public function testMyTestCase()
  {
  	$this->performBackendLogin();
  	
    $this->click("link=Membermanager");
    $this->waitForPageToLoad("30000");
    $this->click("link=Add New User");
    $this->waitForPageToLoad("30000");
    $this->type("id=jform_name", "Test User");
    $this->type("id=jform_username", "testuser");
    $this->click("link=Save & Close");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=Test User"));
    $this->click("link=Membermanager");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Unpublished", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.6"));
    $this->click("//div[@id='editcell']/table/tbody/tr[2]/td[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Published", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.6"));
    $this->click("//div[@id='editcell']/table/tbody/tr[2]/td[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Unpublished", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.6"));
    $this->assertEquals("", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.5"));
    $this->select("id=groups", "label=- - Administrator");
    $this->removeSelection("id=roles", "label=Mitglied");
    $this->addSelection("id=roles", "label=Moderator");
    $this->click("id=cb1");
    $this->click("link=Add group(s)/role(s)");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Administrator: Moderator", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.5"));
    $this->click("id=cb1");
    $this->click("link=Delete group(s)/role(s)");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.5"));
    $this->click("id=cb1");
    $this->select("id=groups", "label=- - - Editor");
    $this->select("id=groups", "label=Public");
    $this->click("link=Add group(s)/role(s)");
    $this->waitForPageToLoad("30000");
    $this->click("id=cb1");
    $this->select("id=groups", "label=- - Administrator");
    $this->removeSelection("id=roles", "label=Mitglied");
    $this->addSelection("id=roles", "label=Moderator");
    $this->click("link=Add group(s)/role(s)");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("Administrator: Moderator", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.5"));
    $this->click("css=a.hasTip > img");
    $this->waitForPageToLoad("30000");
    $this->assertEquals("", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.5"));
    $this->click("id=cb1");
    $this->click("link=Delete");
    $this->assertTrue((bool)preg_match('/^Wirklich löschen[\s\S]$/',$this->getConfirmation()));
    $this->assertEquals("Test", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.4"));
    $this->click("link=User Manager");
    $this->waitForPageToLoad("30000");
    $this->click("id=cb1");
    $this->click("link=Delete");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("One user successfully deleted"));
    $this->click("link=Membermanager");
    $this->waitForPageToLoad("30000");
    $this->click("id=cb1");
    $this->click("link=Delete");
    $this->assertTrue((bool)preg_match('/^Wirklich löschen[\s\S]$/',$this->getConfirmation()));
    $this->assertNotEquals("Test", $this->getTable("xpath=(//div[@id='editcell']/table)[2].2.4"));

    $this->performBackendLogout();
  }
}
?>