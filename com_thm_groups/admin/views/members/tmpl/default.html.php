<?php
/**
 * @category    Joomla component
* @package     THM_Groups
* @subpackage  com_thm_groups.admin
* @name        include_default
* @description include_default file from com_thm_groups
* @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
* @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
* @copyright   2013 TH Mittelhessen
* @license     GNU GPL v.2
* @link        www.mni.thm.de
*/
defined('_JEXEC') or die ('Restricted access');

?>

<?php
$helper = new THMGroupsModelMembers;
$groupOptions = $helper->getGroupSelectOptions();
?>

<div id="THM_Plugin_Members_Content">
 <div id="THM_Plugin_Members_Parameters">
  <?php
  if ($personOrGroup == "person")
  {
   echo '<h2>';
   echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_PERSON");
   echo '</h2>';

   //    echo $callisto->getInput();

   echo '<div class="search_area">';

   echo '<input type="text" name="query" id="search_box" value="type some text" autocomplete="off">';
   echo '<input type="hidden" name="userID" id="userID" value=""/>';

   echo '<div id="search_advice_wrapper"></div>';
   echo '</div>';
   echo $callisto->getKeyword();

  }
  else
  {

  }
  if ($personOrGroup == "group")
  {
   echo '<h2>';
   echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_GROUP");
   echo '</h2>';
  ?>
   <select name="groups" id="groups" class="styled">
    <?php
     echo "<option value='' selected>" . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_GROUPS_LIST') . "</option>";
     foreach ($groupOptions as $groupOption)
     {
      $disabled = $groupOption->disable ? ' disabled="disabled"' : '';
      if (1 == $groupOption->value)
      {
       echo '<option value="' . $groupOption->value . '">' . $groupOption->text . '</option>';
      }
      else
      {
       echo '<option value="' . $groupOption->value . '"' . $disabled . '>' . $groupOption->text . '</option>';
      }
     }
    ?>
   </select>
   <?php
  }
  else
  {

  }
  ?>

  <table id="myTable">
   <tr>
    <td>
     <h3>
      <span class="hasTip"
       title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_DESCRIPTION'); ?>>
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS'); ?>
      </span>
     </h3>
    </td>
   </tr>
   <tr>
   </tr>
      <?php
    if ($personOrGroup == "group")
    {
        echo '<tr>';
        echo '<td>';
        echo '<span class="hasTip"
            title=' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PLAIN_LIST_DESCRIPTION') . '>';
        echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PLAIN_LIST');
        echo '</span>';
        echo '</td>';
        echo '<td>';
        echo '<input type="radio" name="plainList" id="plainListYes" value="1" />';
        echo JText::_("JYES");
        echo '&nbsp';
        echo '<input type="radio" name="plainList" id="plainListNo" value="0" checked />';
        echo JText::_("JNO");
        echo '</td>';
        echo '</tr>';

        // Show roles
        echo '<tr>';
        echo '<td>';
        echo '<span class="hasTip"
            title=' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW_ROLES_DESCRIPTION') . '>';
        echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW_ROLES');
        echo '</span>';
        echo '</td>';
        echo '<td>';
        echo '<input type="radio" name="groupRoles" id="groupRolesYes" value="1" disabled />';
        echo JText::_("JYES");
        echo '&nbsp';
        echo '<input type="radio" name="groupRoles" id="groupRolesNo" value="0" checked />';
        echo JText::_("JNO");
        echo '</td>';
        echo '</tr>';
    }
      ?>
   <tr>
   </tr>
   <tr>
    <td><span class="hasTip"
     title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_FLOAT_DESCRIPTION'); ?>>
     <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_POSITION'); ?>
    </span>
    </td>
    <td><select name="<?php echo $personOrGroup?>Position"
     id="<?php echo $personOrGroup?>Position" class="styled">
      <option selected value="default">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_DEFAULT'); ?>
      </option>
      <option value="left">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_POSITION_LEFT'); ?>
      </option>
      <option value="right">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_POSITION_RIGHT'); ?>
      </option>
    </select>
    </td>

   </tr>
   <tr>
   </tr>
   <tr>
    <td><span class="hasTip"
     title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_BORDER_DESCRIPTION'); ?>>
     <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER'); ?>
    </span>
    </td>
    <td><select name="<?php echo $personOrGroup?>Border"
     id="<?php echo $personOrGroup?>Border" class="styled">
      <option selected value="default">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_DEFAULT'); ?>
      </option>
      <option value="none">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_NONE'); ?>
      </option>
      <option value="solid">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_SOLID'); ?>
      </option>
      <option value="dotted">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_DOTTED'); ?>
      </option>
      <option value="dashed">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_DASHED'); ?>
      </option>
      <option value="double">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_DOUBLE'); ?>
      </option>
    </select>
    </td>

   </tr>
   <tr>
    <td><span class="hasTip"
     title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_CLEAR_DESCRIPTION'); ?>>
     <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT'); ?>
    </span>
    </td>
    <td><select name="<?php echo $personOrGroup?>Float"
     id="<?php echo $personOrGroup?>Float" class="styled">
      <option selected value="default">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_DEFAULT'); ?>
      </option>
      <option value="left">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT_LEFT'); ?>
      </option>
      <option value="right">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT_RIGHT'); ?>
      </option>
      <option value="both">
       <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT_BOTH'); ?>
      </option>
    </select>
    </td>
   </tr>
   <tr>
    <td><span class="hasTip"
     title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_WIDTH_DESCRIPTION'); ?>>
     <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_WIDTH'); ?>
    </span>
    </td>
    <td><a class="minus" id="<?php echo $personOrGroup?>Minus" href="#">&nbsp-&nbsp</a><input type="text"
     value="200" id="<?php echo $personOrGroup?>DivWidth"
     class="styled_number" /><a class="plus" id="<?php echo $personOrGroup?>Plus" href="#">&nbsp+&nbsp</a></td>
   </tr>
   <?php
   if ($personOrGroup == "group")
   {
    echo '<tr>';
    echo '<td><span class="hasTip" title=';
    echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_COLUMN_NUMBER_DESCRIPTION");
    echo '>';
    echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_COLUMN_NUMBER");
    echo '</span>';
    echo '</td>';

    //     echo '<td><input type="number" id="group_column" min="1" max="5" class="styled_number"></td>';

    echo '<td><a class="minus" id="' . $personOrGroup . 'MinusColumn" href="#">&nbsp-&nbsp</a><input type="text" value="1" id="group_column" class="styled_number" />'
          . '<a class="plus" id="' . $personOrGroup . 'PlusColumn" href="#">&nbsp+&nbsp</a></td>';
    echo '</tr>';
   }
   else
   {

   }
   ?>
   <tr>
    <td><span class="hasTip"
     title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_LINK_DESCRIPTION'); ?>>
     <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_LINK'); ?>
    </span>
    </td>
    <td><input type="checkbox" name="<?php echo $personOrGroup?>Link"
     id="<?php echo $personOrGroup?>LinkName" value="2"> <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_LINK_NAME'); ?>
     &nbsp; <input type="checkbox"
     name="<?php echo $personOrGroup?>Link"
     id="<?php echo $personOrGroup?>LinkFirstName" value="1">
    <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_LINK_FIRST_NAME');?>
    </td>
   </tr>
  </table>
 </div>
 <?php
 if ($personOrGroup == "group")
 {
  echo '<div id="button_single_user" class="button">';
  echo '<h3>';
  echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SINGLE_PERSON");
  echo '</h3>';
  echo '</div>';
  echo '<div id="Group_Select">';
  echo  '<div style="display:block; width:150px; float:left;">';
  echo  '<select multiple class="select_user" id="selectFrom">';

  echo  '</select> <a class="link" href="#" id="add">add &gt;&gt;</a>';
  echo '</div>';
  echo '<div style="display:block; width:150px; float:right;">';
  echo '<select multiple class="select_user" id="selectTo">';

  echo '</select> <a class="link" href="#" id="remove">&lt;&lt; remove</a>';
  echo '</div>';
  echo '</div>';
 }
 ?>
 <div id="THM_Plugin_Members_Attributes">
  <?php
  if ($personOrGroup == "person")
  {
   echo $callisto->getInputParams(1);
  }
  else
  {
   echo $callisto->getInputParams(2);
  }
  ?>
 </div>
 <div id="THM_Plugin_Members_AddButton">
  <?php
  if ($personOrGroup == "person")
  {
   echo '<button onclick="insert(1);">';
  }
  else
  {

  }
  if ($personOrGroup == "group")
  {
   echo '<button onclick="insert(2);">';
   echo '<input type="hidden" name="single_user" id="single_user" value="false" />';
  }
  else
  {

  }
  ?>
  <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ADD'); ?>
  </button>
 </div>
</div>
