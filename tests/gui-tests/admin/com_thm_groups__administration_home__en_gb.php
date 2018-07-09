<?php

class ComThmGroupsAdministrationHomeEnGb extends JoomlaSeleniumTest
{
    public function testThmGroupsLinkAvailable()
    {
        $this->performBackendLogin();
        $this->assertTrue($this->isElementPresent("link=THM Groups"));

        $this->performBackendLogout();
    }

    public function testHomeLinkAvailable()
    {
        $this->performBackendLogin();
        $this->assertTrue($this->isElementPresent("link=Home"));

        $this->performBackendLogout();
    }

    public function testMembermanagerLinkAvailable()
    {
        $this->performBackendLogin();
        $this->assertTrue($this->isElementPresent("link=Membermanager"));

        $this->performBackendLogout();
    }

    public function testGroupmanagerLinkAvailable()
    {
        $this->performBackendLogin();
        $this->assertTrue($this->isElementPresent("link=Groupmanager"));

        $this->performBackendLogout();
    }

    public function testRolemanagerLinkAvailable()
    {
        $this->performBackendLogin();
        $this->assertTrue($this->isElementPresent("link=Rolemanager"));

        $this->performBackendLogout();
    }

    public function testStructureLinkAvailable()
    {
        $this->performBackendLogin();
        $this->assertTrue($this->isElementPresent("link=Structuremanager"));

        $this->performBackendLogout();
    }
}

?>