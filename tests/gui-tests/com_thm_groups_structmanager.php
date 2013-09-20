<?php
class ComThmGroupsStructManager extends JoomlaSeleniumTest
{
    public function testThmGroupsStructOptionsAvailable()
    {
        $this->performBackendLogin();
        $this->click("link=Strukturmanager");
        $this->waitForPageToLoad("30000");
        $this->click("css=span.icon-32-new");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertTrue($this->isElementPresent("id=name"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
        try {
            $this->assertEquals("date,link,multiselect,number,picture,table,text,textfield", implode(',', $this->getSelectOptions("id=relation")));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, $e->toString());
        }
    }
}