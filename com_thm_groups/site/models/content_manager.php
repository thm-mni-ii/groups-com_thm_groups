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

require_once HELPERS . 'categories.php';

/**
 * Class retrieves information about content for the profile's content category
 */
class THM_GroupsModelContent_Manager extends JModelList
{
    public $categoryID;

    /**
     * Constructor
     *
     * @param   array $config config array
     *
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $user             = JFactory::getUser();
        $profileID        = JFactory::getApplication()->input->getInt('profileID', $user->id);
        $this->categoryID = THM_GroupsHelperCategories::getIDByProfileID($profileID);

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     * @throws Exception
     */
    protected function getListQuery()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('content.*, pContent.featured AS featured')
            ->from('#__content AS content')
            ->innerJoin('#__thm_groups_content AS pContent ON pContent.id = content.id')
            ->innerJoin('#__categories AS cCats ON cCats.id = content.catid')
            ->innerJoin('#__thm_groups_categories AS pCats ON pCats.id = cCats.id')
            ->where("cCats.id = '$this->categoryID'");

        // User cannot edit anything => only show published and featured
        if (!THM_GroupsHelperCategories::canEdit($this->categoryID)) {
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
