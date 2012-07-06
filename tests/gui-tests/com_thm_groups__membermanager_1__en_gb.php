<?php
class ComThmGroupsMembermanager1EnGb extends JoomlaSeleniumTest
{
  public function testMyTestCase()
  {
    $this->performBackendLogin();
    
    $this->click("link=Membermanager");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("id=toolbar-moderate"));
    $this->assertTrue($this->isElementPresent("link=Add group(s)/role(s)"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-moderate"));
    $this->assertTrue($this->isElementPresent("id=toolbar-unmoderate"));
    $this->assertTrue($this->isElementPresent("link=Delete group(s)/role(s)"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-unmoderate"));
    $this->assertTrue($this->isElementPresent("id=toolbar-Wirklich löschen?"));
    $this->assertTrue($this->isElementPresent("link=Delete"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-delete"));
    $this->assertTrue($this->isElementPresent("id=toolbar-publish"));
    $this->assertTrue($this->isElementPresent("link=Publish"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-publish"));
    $this->assertTrue($this->isElementPresent("id=toolbar-unpublish"));
    $this->assertTrue($this->isElementPresent("link=Disable"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-unpublish"));
    $this->assertTrue($this->isElementPresent("id=toolbar-cancel"));
    $this->assertTrue($this->isElementPresent("link=Cancel"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-cancel"));
    $this->assertTrue($this->isElementPresent("id=toolbar-edit"));
    $this->assertTrue($this->isElementPresent("link=Edit"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-edit"));
    $this->assertTrue($this->isElementPresent("id=toolbar-back"));
    $this->assertTrue($this->isElementPresent("link=Back"));
    $this->assertTrue($this->isElementPresent("css=span.icon-32-back"));
    $this->assertTrue($this->isElementPresent("id=groups"));
    $this->assertTrue($this->isElementPresent("id=roles"));
    $this->assertTrue($this->isElementPresent("id=search"));
    $this->assertTrue($this->isElementPresent("id=groupFilters"));
    $this->assertTrue($this->isElementPresent("id=rolesFilters"));
    $this->assertTrue($this->isElementPresent("name=grcheck"));
    $this->assertEquals("Go", $this->getText("css=button"));
    $this->assertTrue($this->isElementPresent("//button[@onclick=\"this.form.getElementById('search').value='';this.form.getElementById('groupFilters').value='0';this.form.getElementById('rolesFilters').value='0';this.form.submit();\"]"));
    $this->assertTrue($this->isTextPresent("Id"));
    $this->assertTrue($this->isElementPresent("link=Title"));
    $this->assertTrue($this->isElementPresent("link=Last Name"));
    $this->assertTrue($this->isElementPresent("link=First Name"));
    $this->assertTrue($this->isElementPresent("link=Group(s)/Role(s)"));
    $this->assertTrue($this->isElementPresent("link=Published"));
    $this->assertTrue($this->isElementPresent("link=In Joomla!"));
    
    $this->performBackendLogout();
  }
}
?>