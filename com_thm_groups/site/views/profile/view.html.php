<?php
/**
 * @version     v3.0.2
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
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');
jimport('thm_groups.data.lib_thm_groups_user');
JHtml::_('bootstrap.framework');
JHtml::_('behavior.modal');

/**
 * THMGroupsViewProfile class for component com_thm_groups
 *
 * @category    Joomla.Component.Site
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THM_GroupsViewProfile extends JViewLegacy
{

    protected $form;

    protected $links;

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
        $model = $this->getModel();
        $extra = $model->getExtra($structId, $type);
        return $extra;
    }

    /**
     * Method to get structe type
     *
     * @param   Int  $aid  content the Artikel ID
     *
     * @return String $result  content the artikel name
     * @deprecated
     */
    public function getArtikelname($aid)
    {

        $tempaid = intval($aid);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("title")->from("#__content")->where("id =" . $tempaid);
        $db->setQuery($query);
        $db->execute();

        $artikel = $db->loadObject();


        $result = $artikel->title;

        return $result;

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
        $model = $this->getModel();
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
        $app	 = JFactory::getApplication()->input;
        $mainfarme = JFactory::getApplication();
        $pathway = $mainfarme->getPathway();
        $pathwayitems = $pathway->getPathWay();
        $document = JFactory::getDocument();

        //include Picturefill 2.0 fallback
        //$document->addScript(JUri::root() . 'libraries/thm_groups_responsive/assets/js/picturefill_min.js','text/javascript" defer="false" async="true');

        // Include Bootstrap
        JHtmlBootstrap::loadCSS();

        //todo: comment library path in when lib is pushed to Gerrit.
        $document->addStyleSheet($this->baseurl . '/libraries/thm_groups_responsive/assets/css/respBaseStyles.css');
        //$document->addStyleSheet($this->baseurl . '/components/com_thm_groups/css/responsiveGroups.css');
        $document->addStyleSheet("administrator/components/com_thm_groups/css/membermanager/icon.css");

        $cid = $app->get('gsuid', 0);

        $model     = $this->getModel();
        $items     = $this->get('Data');
        $structure = $this->get('Structure');
        $gsgid     = $app->get('gsgid');
        $gsuid     = $app->get('gsuid');

        $var = array();
        if (isset($_GET))
        {
            $var = $_GET;
            $attribut = "";
            foreach ($var as $index => $value)
            {
                $pos = strpos($index, '_back');

                if ($pos !== false)
                {
                    $temp = explode('_back', $index);
                    $attribut .= $temp[0] . "=" . $value . '&';
                }
            }
        }
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

        $backRef = (count($pathwayitems) > 0)? $pathwayitems[count($pathwayitems) - 1]->link : " ";

        if (isset($attribut))
        {
            $this->links = JURI::base() . 'index.php?' . $attribut . '&gsuid=' . $gsuid;
            $old_option = $app->get("option_back");
            switch ($old_option)
            {
                case "com_content":
                    $artikleId = $app->get("id_back");
                    $artikelname = (JFactory::getConfig()->getValue('config.sef') == 1)? $this->getArtikelname($artikleId) : explode(":", $artikleId);
                    if (isset($artikelname))
                    {
                        $pathway->addItem($artikelname, JURI::base() . 'index.php?' . $attribut . '&gsuid=' . $gsuid);
                    }
                    else
                    {
                        $pathway->addItem(JFactory::getDocument()->get('title'), JURI::base() . 'index.php?' . $attribut . '&gsuid=' . $gsuid);
                    }

                    break;

                case "com_thm_groups":
                    $layout = $app->get("layout_back");
                    if ($layout == 'singlearticle')
                    {
                        $pathway->addItem(JFactory::getDocument()->get('title'), JURI::base() . 'index.php?' . $attribut . '&gsuid=' . $gsuid);
                    }
                    break;
            }
            $pathway->addItem($name);
        }
        else
        {
            $this->links = JURI::base() . 'index.php';
            $pathway->addItem($name);
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

        $this->backAttribute = $attribut;
        $itemid = $app->get('Itemid', 0);
        $this->backRef = $backRef;
        $this->items = $items;
        $this->itemid = $itemid;
        $canedit = $model->canEdit($gsgid);
        $this->canEdit =  $canedit;
        $this->userid = $cid;
        $this->structure = $structure;
        $this->gsgid =$gsgid;
        $this->model = $this->getModel("profile");
        parent::display($tpl);
    }

}
