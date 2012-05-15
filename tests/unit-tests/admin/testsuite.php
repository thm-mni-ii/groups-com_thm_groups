<?php

require_once 'framework_include.php';

require_once '_membermanagerTest.php';
require_once 'THMGroupsModelAddGroupAdminTest.php';
require_once 'THMGroupsModelAddRoleAdminTest.php';
require_once 'THMGroupsModelAddStructureAdminTest.php';
require_once 'THMGroupsModeleditAdminTest.php';
require_once 'THMGroupsModelEditGroupAdminTest.php';
require_once 'THMGroupsModelEditRoleAdminTest.php';
require_once 'THMGroupsModelEditStructureAdminTest.php';
require_once 'THMGroupsModelGroupmanagerAdminTest.php';
require_once 'THMGroupsModelmembermanagerAdminTest.php';
require_once 'THMGroupsModelRolemanagerAdminTest.php';
require_once 'THMGroupsModelStructureAdminTest.php';
require_once 'THMGroupsControllerAddGroupTest.php';
require_once 'THMGroupsViewTHMGroupsTest.php';
require_once 'ConfDBTest.php';
require_once 'MemberManagerDBTest.php';


class testsuite{}
echo "\n";

$suiteAdmin = new PHPUnit_TestSuite("Component THM Groups admin Test Suite");

echo "\nAdmin tests ------------------";

//--- add testcases to admin test suite
$suiteAdmin->addTestSuite("_membermanagerTest");
$suiteAdmin->addTestSuite("THMGroupsModelAddGroupAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelAddRoleAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelAddStructureAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModeleditAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelEditGroupAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelEditRoleAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelEditStructureAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelGroupmanagerAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelmembermanagerAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelRolemanagerAdminTest");
$suiteAdmin->addTestSuite("THMGroupsModelStructureAdminTest");
$suiteAdmin->addTestSuite("THMGroupsControllerAddGroupTest");
$suiteAdmin->addTestSuite("THMGroupsViewTHMGroupsTest");
$suiteAdmin->addTestSuite("ConfDBTest");
$suiteAdmin->addTestSuite("MemberManagerDBTest");

//---
echo "Number of test cases: " . $suiteAdmin->countTestCases() . "\n\n";

$result = PHPUnit::run($suiteAdmin);
echo $result -> toString();

?>