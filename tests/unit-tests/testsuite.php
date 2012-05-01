<?php

require_once 'framework_include.php';

require_once 'site/THMGroupsModelAdvancedTest.php';
//require_once 'THMGroupsModeleditTest.php';
require_once 'site/THMGroupsModelEditGroupTest.php';
//require_once 'THMGroupsModelGroupsTest.php';
//require_once 'THMGroupsModelListTest.php';
//require_once 'THMGroupsModelProfileTest.php';
class testsuite{}

$suite = new PHPUnit_TestSuite("Component THM Groups Site Model Test");
echo  $suite->getName() . "\n";
$suite->addTestSuite("THMGroupsModelAdvancedTest");
//$suite->addTestSuite("THMGroupsModeleditTest");
$suite->addTestSuite("THMGroupsModelEditGroupTest");
//$suite->addTestSuite("THMGroupsModelGroupsTest");
//$suite->addTestSuite("THMGroupsModelListTest");
//$suite->addTestSuite("THMGroupsModelProfileTest");

echo "Number of test cases: " . $suite->countTestCases() . "\n\n";

$result = PHPUnit::run($suite);
echo $result -> toString();

?>