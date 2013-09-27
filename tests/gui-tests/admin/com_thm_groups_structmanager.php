<?php
class ComThmGroupsStructManager extends JoomlaSeleniumTest {
    public function testLinkAddEntryAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Add Entry" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Add Entry" does not exist' );
        }

        $this->performBackendLogout ();
    }
    public function testLinkEditEntryAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Edit Entry" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Edit Entry" does not exist!' );
        }

        $this->performBackendLogout ();
    }
    public function testLinkDeleteEntryAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Delete" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Delete Entry" does not exist!' );
        }

        $this->performBackendLogout ();
    }
    public function testLinkOptionsAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Options" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Options" does not exist!' );
        }

        $this->performBackendLogout ();
    }
}