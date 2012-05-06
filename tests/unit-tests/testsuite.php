<?php

require_once 'framework_include.php';

require_once 'PHPUnit.php';
require_once 'site/THMGroupsModelAdvancedTest.php';
require_once 'site/THMGroupsModeleditTest.php';
require_once 'site/THMGroupsModelEditGroupTest.php';
require_once 'site/THMGroupsModelGroupsTest.php';
require_once 'site/THMGroupsModelListTest.php';
require_once 'site/THMGroupsModelProfileTest.php';
require_once 'site/THMGroupsControllerProfileTest.php';
require_once 'site/THMGroupsViewProfileTest.php';
require_once 'site/THMGroupsViewAdvancedTest.php';
require_once 'site/THMGroupsViewEditGroupTest.php';
require_once 'site/THMGroupsViewEditTest.php';
require_once 'site/THMGroupsViewGroupsTest.php';
require_once 'site/THMGroupsViewListTest.php';
require_once 'site/PicTransformTest.php';

require_once 'admin/THMGroupsViewTHMGroupsTest.php';
require_once 'admin/THMGroupsControllerAddGroupTest.php';
require_once 'admin/ConfDBTest.php';

class testsuite{}
echo "\n";

$suite = new PHPUnit_TestSuite("Component THM Groups site Test Suite");

echo "Component THM Groups Test Suite" . "\n";
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

$suiteAdmin = new PHPUnit_TestSuite("Component THM Groups admin Test Suite");

echo "\nAdmin tests ------------------";

//--- add testcases to admin test suite
$suiteAdmin->addTestSuite("THMGroupsViewTHMGroupsTest");
$suiteAdmin->addTestSuite("THMGroupsControllerAddGroupTest");
$suiteAdmin->addTestSuite("ConfDBTest");
//---
echo "\nNumber of test cases: " . $suiteAdmin->countTestCases() . "\n\n";

$result = PHPUnit::run($suiteAdmin);
echo $result -> toString();

?>