<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        attribute edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('thm_core.edit.model');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelAttribute_Edit extends THM_CoreModelEdit
{
    protected $form = false;

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to get the form
     *
     * @param   Array    $data      Data         (default: Array)
     * @param   Boolean  $loadData  Load data  (default: true)
     *
     * @return  A Form object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        if (empty($this->form))
        {
            $this->form = $this->loadForm('com_thm_groups.attribute_edit', 'attribute_edit', array('control' => 'jform', 'load_data' => $loadData));
        }

        return $this->form;
    }

    /**
     * Returns table object
     *
     * @param   string  $type    type
     * @param   string  $prefix  prefix
     * @param   array   $config  config
     *
     * @return  JTable|mixed
     */
    public function getTable($type = 'Attribute', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to load the form data
     *
     * @return  Object
     */
    protected function loadFormData()
    {
        $input = JFactory::getApplication()->input;
        $name = $this->get('name');
        $resource = str_replace('_edit', '', $name);
        $task = $input->getCmd('task', "$resource.add");
        $resourceArray = $input->get('cid', array(), 'array');
        $resourceID = empty($resourceArray)? $input->getInt('id', 0) : $resourceArray[0];

        $add = (($task != "$resource.edit") AND empty($resourceID));
        if ($add)
        {
            return $this->getItem(0);
        }

        $item = $this->getItem($resourceID);
        $options = json_decode($item->options);
        if (!empty($options))
        {
            $item->validate = empty($options->required)? 0 : 1;
            if (!empty($options->icon))
            {
                $item->iconpicker = $options->icon;
            }
        }
        return $item;
    }
}