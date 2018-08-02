<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/categories.php';

/**
 * Class retrieves information about content for the profile's content category
 */
class THM_GroupsModelContent_Manager extends JModelList
{
    public $canEditAll = false;

    public $canEditOne = false;

    private $canPotentiallyEdit = false;

    public $categoryID;

    /**
     * Constructor
     *
     * @param   array $config config array
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [];
        }

        $profileID = JFactory::getApplication()->input->getInt('profileID', JFactory::getUser()->id);

        $this->categoryID = THM_GroupsHelperCategories::getIDByProfileID($profileID);

        $user       = JFactory::getUser();
        $this->canEditAll = $user->authorise('core.edit', 'com_content.category.' . $this->categoryID);

        if ($this->canEditAll) {
            $this->canPotentiallyEdit = true;
            $this->canEditOne         = true;
        } else {
            $this->canPotentiallyEdit = $user->authorise('core.edit.own', 'com_content.category.' . $this->categoryID);
        }

        parent::__construct($config);
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $items = parent::getItems();

        $user       = JFactory::getUser();
        $canEditAll = true;
        $canEditOwn = $user->authorise('core.edit.own', 'com_content.category.' . $this->categoryID);

        foreach ($items as $item) {
            if ($this->canEditAll) {
                $item->canEdit = true;
            } else {
                $item->canEdit = ($canEditOwn and $item->created_by == $user->id);
                $canEditAll = ($canEditAll and $item->canEdit);
                $this->canEditOne = ($this->canEditOne or $item->canEdit);
            }
        }

        return empty($items) ? [] : $items;
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('content.*')
            ->select('users.name AS author_name')
            ->select('pContent.featured AS featured')
            ->from('#__content AS content')
            ->innerJoin('#__thm_groups_content AS pContent ON pContent.id = content.id')
            ->innerJoin('#__categories AS cCats ON cCats.id = content.catid')
            ->innerJoin('#__thm_groups_categories AS pCats ON pCats.id = cCats.id')
            ->innerJoin('#__users AS users ON users.id = pCats.profileID')
            ->where("cCats.id = '$this->categoryID'");

        // User cannot edit anything => only show published
        if (!$this->canPotentiallyEdit) {
            $date       = JFactory::getDate();
            $quotedDate = $dbo->quote($date->toSql());

            $query->where("content.state = '1'");
            $query->where("pContent.featured = '1'");
            $query->where("content.publish_up <= $quotedDate");
            $query->where("( content.publish_down >= $quotedDate OR content.publish_down = '0' OR content.publish_down = '0000-00-00 00:00:00')");

        }

        $query->order('ordering ASC');

        return $query;
    }

    /**
     * Overwrites the JModelList populateState function
     *
     * @param   string $ordering  the column by which the table is should be ordered
     * @param   string $direction the direction in which this column should be ordered
     *
     * @return  void  sets object state variables
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $session = JFactory::getSession();
        $session->set($this->context . '.ordering', "ordering ASC");

        $this->setState('list.ordering', $ordering);
        $this->setState('list.direction', $direction);
        $this->setState('list.start', 0);
        $this->setState('list.limit', 0);
    }
}
