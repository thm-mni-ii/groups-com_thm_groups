<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @description THMGroupsViewProfile file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewProfile class for component com_thm_groups
 *
 * @category    Joomla.Component.Site
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsViewProfile extends JView
{
	protected $form;

	/**
	 * Method to get extra
	 *
	 * @param   Int     $structId  StructID
	 * @param   String  $type      Type
	 *
	 * @return $extra
	 */
	public function getExtra($structId, $type)
	{
		$model = &$this->getModel();
		$extra = $model->getExtra($structId, $type);
		return $extra;
	}

	/**
	 * Method to get structe type
	 *
	 * @param   Int  $structId  StructID
	 *
	 * @return structureType
	 */
	public function getStructureType($structId)
	{
		$model = &$this->getModel();
		$structure = $model->getStructure();
		$structureType = null;
		foreach ($structure as $structureItem)
		{
			if ($structureItem->id == $structId)
			{
				$structureType = $structureItem->type;
			}
		}
		return $structureType;
	}

	/**
	 * Method to get display
	 *
	 * @param   Object  $tpl  template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$app	 = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathwayitems = $pathway->getPathWay();
		$document = & JFactory::getDocument();
		$document->addStyleSheet("administrator/components/com_thm_groups/css/membermanager/icon.css");

		$cid = JRequest::getVar('gsuid', 0);

		$model     = &$this->getModel();
		$items     = &$this->get('Data');
		$structure = &$this->get('Structure');
		$gsgid     = JRequest::getVar('gsgid');
		
		$name = "";
		foreach ($items as $val)
		{
			if ($val->structid == 2)
			{
				$name = $val->value . ', ' . $name;
			}
			else 
			{
				if ($val->structid == 1)
				{
					$name = $name . $val->value;
				}
				else 
				{
				}
			}
		}
		$pathLinks = array();
		foreach ($pathwayitems as $pwitem)
		{
			array_push($pathLinks, $pwitem->name);
		}
		$pageTitle = JRequest::getVar('pageTitle', '');
		if (!array_search($pageTitle, $pathLinks) && $pageTitle != '')
		{
			$pathway->addItem($pageTitle, 'index.php?option=com_thm_groups&view=list&Itemid=' . JRequest::getVar('Itemid', 0));
		}
		else 
		{
		}
		$backRef = $pathwayitems[count($pathwayitems) - 1]->link;
		if ($name != '')
		{
			$pathway->addItem($name, '');
		}
		else 
		{
		}
		
		// Daten für die Form
		$textField = array();
		foreach ($structure as $structureItem)
		{
			foreach ($items as $item)
			{
				if ($item->structid == $structureItem->id)
				{
					$value = $item->value;
				}
			}
			if ($structureItem->type == "TEXTFIELD")
			{
				$textField[$structureItem->field] = $value;
			}
		}

		// Daten für die Form
		$this->form = $this->get('Form');

		if (!empty($textField))
		{
			$this->form->bind($textField);
		}

		$itemid = JRequest::getVar('Itemid', 0);

		$this->assignRef('backRef', $backRef);
		$this->assignRef('items', $items);
		$this->assignRef('itemid', $itemid);
		$this->assignRef('canEdit', $model->canEdit());
		$this->assignRef('userid', $cid);
		$this->assignRef('structure', $structure);
		$this->assignRef('gsgid', $gsgid);

		parent::display($tpl);
	}
}
