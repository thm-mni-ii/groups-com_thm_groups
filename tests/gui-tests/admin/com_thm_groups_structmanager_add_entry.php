<?php
class ComThmGroupsStructManagerAddEntry extends JoomlaSeleniumTest {
    public function testLinkSaveAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Save" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Save" does not exist!' );
        }

        $this->performBackendLogout ();
    }

    public function testLinkSaveAndCloseAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Save & Close" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Save & Close" does not exist!' );
        }

        $this->performBackendLogout ();
    }

    public function testLinkSaveAndNewAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Save & New" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Save & New" does not exist!' );
        }

        $this->performBackendLogout ();
    }

    public function testLinkCloseAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );

        try {
            $this->assertTrue ( $this->isElementPresent ( "link=Close" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, 'Button "Close" does not exist!' );
        }

        $this->performBackendLogout ();
    }

    public function testFieldFieldnameAvailable() {
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );
        $this->click ( "link=Add Entry" );
        $this->waitForPageToLoad ( "30000" );

        // Name of structure
        try {
            $this->assertTrue ( $this->isElementPresent ( "id=name" ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, "Element with id=name does not exist!" );
        }

        $this->click ( "link=Close" );
        $this->waitForPageToLoad ( "30000" );

        $this->performBackendLogout ();
    }

    public function testFieldTypeAvailable(){
        $this->performBackendLogin ();

        $this->click ( "link=Structuremanager" );
        $this->waitForPageToLoad ( "30000" );
        $this->click ( "link=Add Entry" );
        $this->waitForPageToLoad ( "30000" );

        // Type of structure
        try {
            $this->assertEquals ( "date,link,multiselect,number,picture,table,text,textfield", implode ( ',', $this->getSelectOptions ( "id=relation" ) ) );
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            array_push ( $this->verificationErrors, "Dropdown Error!" );
        }

        $this->click ( "link=Close" );
        $this->waitForPageToLoad ( "30000" );

        $this->performBackendLogout ();
    }
}