<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelAttribute_Manager
 * @description THM_GroupsModelAttribute_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('thm_core.list.model');

/**
 * THM_GroupsModelAttribute_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelAttribute_Manager extends THM_CoreModelList
{

    protected $defaultOrdering = 'attribute.id';

    protected $defaultDirection = 'ASC';

    /**
     * Constructor
     *
     * @param   array  $config  The config
     */
    public function __construct($config = array())
    {

        // If change here, change then in default_head
        $config['filter_fields'] = array(
            'attribute.id',
            'attribute.name',
            'dynamic.name'
        );

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('attribute.id')
            ->select('attribute.name')
            ->select('dynamic.name as dynamic_type_name')
            ->select('attribute.options')
            ->select('attribute.published')
            ->select('attribute.ordering')
            ->select('attribute.description')
            ->from('#__thm_groups_attribute AS attribute')
            ->innerJoin('#__thm_groups_dynamic_type AS dynamic ON attribute.dynamic_typeID = dynamic.id');


        $this->setIDFilter($query, 'attribute.published', array('filter.published'));

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $query->where("(attribute.name LIKE '%" . implode("%' OR attribute.name LIKE '%", explode(' ', $search)) . "%')");
        }

        $dynamic = $this->getState('filter.dynamic');
        if (!empty($dynamic) && $dynamic != '*')
        {
            $query->where("attribute.dynamic_typeID = '$dynamic'");
        }

        $this->setOrdering($query);

        return $query;
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $items = parent::getItems();
        $return = array();
        if (empty($items))
        {
            return $return;
        }

        $index = 0;
        $return['attributes'] = array('class' => 'ui-sortable');
        foreach ($items as $item)
        {
            // TODO check user rights to sort attributes
            // $canChange = $this->hasUserRightTo('EditState', $item);
            $canChange = true;
            $listOrder = $this->state->get('list.ordering');
            $saveOrder = $listOrder == 'attribute.ordering';
            $iconClass = '';
            if (!$canChange)
            {
                $iconClass = ' inactive';
            }
            elseif (!$saveOrder)
            {
                $iconClass = ' inactive tip-top hasTooltip';
            }

            $order = '';

            if ($canChange && $saveOrder)
            {
                $order = '<input type="text" style="display:none" name="order[]" size="5" value="'
                    . $item->ordering . '" class="width-20 text-area-order " />';
            }

            $url = "index.php?option=com_thm_groups&view=attribute_edit&cid[]=$item->id";
            $return[$index] = array();

            $return[$index]['attributes'] = array( 'class' => 'order nowrap center', 'id' => $item->id);
            $return[$index]['ordering']['attributes'] = array( 'class' => "order nowrap center", 'style' => "width: 40px;");
            $return[$index]['ordering']['value']
                = "<span class='sortable-handler$iconClass'><i class='icon-menu'></i></span>" . $order;

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = JHtml::_('link', $url, $item->name);
            $return[$index][3] = $this->getToggle($item->id, $item->published, 'attribute', '', 'published');
            $return[$index][4] = $item->dynamic_type_name;
            $return[$index][5] = $item->description;
            $index++;
        }
        return $return;
    }

    /**
     * Function to get table headers
     *
     * @return array including headers
     */
    public function getHeaders()
    {
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers = array();
        $headers['order'] =  JHtml::_('searchtools.sort', '', 'attribute.ordering', $direction, $ordering , null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'attribute.id', $direction, $ordering);
        $headers['attribute'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ATTRIBUTE_NAME', 'attribute.name', $direction, $ordering);
        $headers['published'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ATTRIBUTE_PUBLISHED', 'attribute.published', $direction, $ordering);
        $headers['dynamic'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_DYNAMIC_TYPE_NAME', 'dynamic.name', $direction, $ordering);
        $headers['description'] = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }

    /**
     * populates State
     *
     * @param   null  $ordering   ?
     * @param   null  $direction  ?
     *
     * @return void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout'))
        {
            $this->context .= '.' . $layout;
        }

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $static = $app->getUserStateFromRequest($this->context . '.filter.static', 'filter_static');
        $this->setState('filter.dynamic', $static);

        parent::populateState("attribute.id", "ASC");
    }

    /**
     * Deletes pictures from folder
     *
     * @param   Object  $attribute  Object of attribute
     *
     * @return  boolean
     */
    private function deletePictures($attribute)
    {
        // Get all Pictures from attribute
        $dbo = JFactory::getDbo();

        $usersAttributeQuery = $dbo->getQuery(true);
        $usersAttributeQuery->select($dbo->qn(array('ID', 'value', 'attributeID')))
            ->from($dbo->qn('#__thm_groups_users_attribute'))
            ->where($dbo->qn('attributeID') . ' = ' . $attribute->id . '');
        $dbo->setQuery($usersAttributeQuery);
        $pictures = $dbo->loadObjectList();

        // Get path
        $path = json_decode($attribute->options)->path;

        // Delete files
        if (($path != null) || ($path != false))
        {
            foreach (scandir($path) as $folderPic)
            {
                foreach ($pictures as $pic)
                {
                    $picName = $pic->value;
                    var_dump($picName, $folderPic);
                    if ($folderPic == $picName)
                    {
                        unlink($path . $folderPic);
                    }

                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Deletes attribute from database and removes all related
     * entries from users_attribute
     *
     * @return bool
     */
    public function delete()
    {
        $jinput = JFactory::getApplication()->input;
        $postVariables = $jinput->post->getArray(array());
        $attributeID = $postVariables['cid'][0];

        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        // Remove related pictures from folder:
        $query->select('*')
              ->from($dbo->qn('#__thm_groups_attribute'))
              ->where('id = ' . (int) $attributeID);
        $dbo->setQuery($query);
        $attribute = $dbo->loadObject();

        if ($this->deletePictures($attribute))
        {
            // Delete attribute from database:
            $query = $dbo->getQuery(true);

            $query->delete($dbo->qn('#__thm_groups_attribute'))
                ->where($dbo->qn('id') . ' = ' . $attributeID);

            $dbo->setQuery($query);

            $result = $dbo->execute();

            if (!$result)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }
    /**
     * Returns custom hidden fields for page
     *
     * @return array
     */
    public function getHiddenFields()
    {
        return array();
    }
}
