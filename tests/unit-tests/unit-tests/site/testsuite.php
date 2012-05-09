<?php

require_once 'framework_include.php';

require_once 'PHPUnit.php';
require_once 'THMGroupsModelAdvancedTest.php';
require_once 'THMGroupsModeleditTest.php';
require_once 'THMGroupsModelEditGroupTest.php';
require_once 'THMGroupsModelGroupsTest.php';
require_once 'THMGroupsModelListTest.php';
require_once 'THMGroupsModelProfileTest.php';
require_once 'THMGroupsControllerProfileTest.php';
require_once 'THMGroupsViewProfileTest.php';
require_once 'THMGroupsViewAdvancedTest.php';
require_once 'THMGroupsViewEditGroupTest.php';
require_once 'THMGroupsViewEditTest.php';
require_once 'THMGroupsViewGroupsTest.php';
require_once 'THMGroupsViewListTest.php';
require_once 'PicTransformTest.php';


class testsuite{}
echo "\n";

$suite = new PHPUnit_TestSuite("Component THM Groups site Test Suite");

echo  "Component THM Groups Test Suite" . "\n";
echo "\nSite tests -------------------";

//--- add testcases to site test suite
$suite->addTestSuite("THMGroupsControllerProfileTest");
$suite->addTestSuite("THMGroupsViewProfileTest");
$suite->addTestSuite("THMGroupsViewAdvancedTest");
$suite->addTestSuite("THMGroupsViewEditGroupTest");
$suite->addTestSuite("THMGroupsViewEditTest");
$suite->addTestSuite("THMGroupsViewGroupsTest");
$suite->addTestSuite("THMGroupsViewListTest");
$suite->addTestSuite("PicTransformTest");
$suite->addTestSuite("THMGroupsModelAdvancedTest");
$suite->addTestSuite("THMGroupsModeleditTest");
$suite->addTestSuite("THMGroupsModelEditGroupTest");
$suite->addTestSuite("THMGroupsModelGroupsTest");
$suite->addTestSuite("THMGroupsModelListTest");
$suite->addTestSuite("THMGroupsModelProfileTest");
//---

echo "Number of test cases: " . $suite->countTestCases() . "\n\n";

$result = PHPUnit::run($suite);
echo $result -> toString();

?>