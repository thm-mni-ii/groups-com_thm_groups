<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @description THMGroupsViewAdvanced file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
jimport('joomla.application.component.view');
jimport('thm_groups.data.lib_thm_groups');


/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewAdvanced extends JViewLegacy
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $mainframe = Jfactory::getApplication();
        $app  = JFactory::getApplication()->input;
        // $layout = $this->getLayout();
        $model = $this->getmodel('advanced');

        // Mainframe Parameter
        $params = $mainframe->getParams();
        $userid = $app->get('gsuid', 0);
        $pagetitle = $params->get('page_title');
        $showpagetitle = $params->get('show_page_heading');
        if ($showpagetitle)
        {
            $title = $pagetitle;
        }
        else
        {
            $title = "";
        }
        $pathway = $mainframe->getPathway();
        if ($userid)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('value');
            $query->from($db->qn('#__thm_groups_users_attribute'));
            $query->where('usersID = ' . $userid);
            $query->where('attributeID = 1');

            $db->setQuery($query);
            $firstname = $db->loadObjectList();
            $name = $app->get('name', '') . ', ' . $firstname[0]->value;
            $pathway->addItem($name, '');
        }
        else
        {
        }
        $this->title = $title;
        $this->app = $app;
        $itemId = $app->get('Itemid', 0, 'get');
        $viewparams = $model->getViewParams();
        $this->params =  $viewparams;
        $groupnumber = $model->getGroupNumber();
        $this->gsgid = $groupnumber;
        $this->itemid = $itemId;
        $canEdit = $model->canEdit();
        $this->canEdit = $canEdit;
        $tempdata = $model->getData();
        $this->data = $tempdata;
        $gettable = $model->getDataTable();
        $this->dataTable = $gettable;
      /*  $getStructur = $model->getStructure();
        $this->structure = $getStructur;*/
        $advancedView = $model->getAdvancedView();

        $this->view = $advancedView;

        // Long Info Truncate
        $truncateLongInfo = !$params->get('longInfoNotTruncated', false);
        $this->truncateLongInfo = $truncateLongInfo;

        // Load test
        $scriptDir = str_replace(JPATH_SITE, '', "/libraries/thm_groups/assets/js/");

        // Load Dynamic CSS
        $mycss = $this->getCssView($params, $advancedView);
        $document = JFactory::getDocument();
        $document->addStyleDeclaration($mycss);

        // Notify Preview Observer
        $token = $app->get('notifytoken', false);
        if (!empty($token))
        {
            $model->notifyPreviewObserver($itemId, $token);
        }

        parent::display($tpl);
    }

    /**
     * Method to generate table
     *
     * @param   Object  $data  Data
     *
     * @return String table
     */
    public function make_table($data)
    {
        $jsonTable = json_decode($data);
        $table = "<table class='table'><tr>";
        foreach ($jsonTable[0] as $key => $value)
        {
            $headItem = str_replace("_", " ", $key);
            $table = $table . "<th>" . $headItem . "</th>";
        }
        $table = $table . "</tr>";
        foreach ($jsonTable as $item)
        {
            $table = $table . "<tr>";
            foreach ($item as $value)
            {
                $table = $table . "<td>" . $value . "</td>";
            }
            $table = $table . "</tr>";
        }
        $table = $table . "</table>";
        return $table;
    }



    /**
     * Add px Suffix to numeric value (for css)
     *
     * @param   Mixed  $value  Value
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  String   	$value  	CSS-Value
     */
    public function addPxSuffixToNumeric($value)
    {
        if (is_numeric($value))
        {
            $value .= 'px';
        }
        return $value;
    }



    /**
     * Get the Stylesheet for Advance View List
     *
     * @param   Array    $params        Contains the Paramter for the View
     * @param   Boolean  $advancedView  Show multiple Containers in one Row
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  String   $result  the HTML code of te view
     */
    public function getCssView($params, $advancedView = 0)
    {

        // Container Wrapper Width - DO NOT CHANGE
        $containerWrapperWidth = (empty($advancedView)) ? '100%': '50%';



        // LOAD PARAMS START

        // Container Dimensions
        $containerWidth = $params->get('containerWidth', '100%');
        $containerHeight = $params->get('containerHeight', 'auto');

        // Container Padding
        $containerPadding = $params->get('containerPadding', 10);

        // Container Margin Bottom
        $containerMarginBottom = $params->get('containerMarginBottom', 10);

        // Container Background
        $containerBackgroundOdd = $params->get('containerBackgroundOdd', '#f9f9f9');
        $containerBackgroundEven = $params->get('containerBackgroundEven', '#f1f1f1');

        // Font Params
        $fontFamily = $params->get('fontFamily', 'inherit');
        $fontSize = $params->get('fontSize', 'inherit');
        $fontColorOdd = $params->get('fontColorOdd', '#000000');
        $fontColorEven = $params->get('fontColorEven', '#000000');
        $longInfoColorOdd = $params->get('longInfoColorOdd', '#525252');
        $longInfoColorEven = $params->get('longInfoColorEven', '#525252');

        // Profile Image Dimensions
        $imgWidth = $params->get('profileImageWidth', '66');
        $imgHeight = $params->get('profileImageHeight', 'auto');
        $imgBordered = $params->get('profileImageBorderd', false);
        if ($imgBordered)
        {
            $imgBordered = 'border: 1px solid #ffffff;
                            -webkit-border-radius: 4px;
                            border-radius: 4px;
                            -webkit-box-shadow: 0px 0px 3px 0px #999999;
                            box-shadow: 0px 0px 3px 0px #999999;';
        }
        else
        {
            $imgBordered = '';
        }
        $imgPositionLeft = $params->get('profileImageFloatedLeft', false);
        if ($imgPositionLeft)
        {
            $imgPosition = 'margin:0px 10px 0px 0px!important;float:left;';
        }
        else
        {
            $imgPosition = 'margin:0px 0px 0px 10px!important;float:right;';
        }

        // Addition individuell Styles
        $profileContainerStyles = $params->get('profileContainerStyles', false);
        $profileImageStyles = $params->get('profileImageStyles', false);
        $textLineStyles = $params->get('textLineStyles', false);
        $textLineLabelStyles = $params->get('textLineLabelStyles', false);
        $linksStyles = $params->get('linksStyles', false);
        $showMoreButtonStyles = $params->get('showMoreButtonStyles', false);
        $longInfoStyles = $params->get('longInfoStyles', false);



        // LOAD PARAMS END

        $out = 'div#thm_groups_profile_container_list {
                    font-family: ' . $fontFamily . ';
                    font-size: ' . $this->addPxSuffixToNumeric($fontSize) . ';
                }

                div#thm_groups_profile_container_list a {
                    font-family: inherit;
                    font-size: inherit;
                    ' . $linksStyles . '
                }

                div.thm_groups_profile_container_list_row_odd, div.thm_groups_profile_container_list_row_even {
                    width: 100%;
                    margin-bottom: ' . $this->addPxSuffixToNumeric($containerMarginBottom) . ';
                    clear: both;
                    ' . $profileContainerStyles . '
                }

                div.thm_groups_profile_container_list_row_odd {
                    color: ' . $fontColorOdd . ';
                    background: ' . $containerBackgroundOdd . ';
                }

                div.thm_groups_profile_container_list_row_even {
                    color: ' . $fontColorEven . ';
                    background: ' . $containerBackgroundEven . ';
                }

                div.clearfix {
                    clear:both;
                }

                div.thm_groups_profile_container_list_coloumn_wrapper {
                    width: ' . $containerWrapperWidth . ';
                }

                div.thm_groups_profile_container_list_coloumn_wrapper_left {
                    float: left;
                }

                div.thm_groups_profile_container_list_coloumn_wrapper_right {
                    float: right;
                }

                div.thm_groups_profile_container_list_coloumn {
                    width: ' . $this->addPxSuffixToNumeric($containerWidth) . ';
                    height: ' . $this->addPxSuffixToNumeric($containerHeight) . ';
                    margin: auto;
                }

                div.thm_groups_profile_container_list_coloumn_content_wrapper {
                    padding: ' . $this->addPxSuffixToNumeric($containerPadding) . ';
                }

                img.thm_groups_profile_container_profile_image {
                    max-width: ' . $this->addPxSuffixToNumeric($imgWidth) . ';
                    max-height: ' . $this->addPxSuffixToNumeric($imgHeight) . ';
                    display: block;
                    ' . $imgBordered . '
                    ' . $imgPosition . '
                    ' . $profileImageStyles . '
                }

                input#thm_groups_profile_container_preview_button {
                    cursor: pointer;
                }

                span.thm_groups_profile_container_profile_read_more {
                    text-decoration: underline;
                    cursor: pointer;
                    ' . $showMoreButtonStyles . '
                }

                div.thm_groups_profile_container_list_row_odd div.thm_groups_profile_container_profile_long_info,
                div.thm_groups_profile_container_list_row_odd div.thm_groups_profile_container_profile_long_info li {
                    color: ' . $longInfoColorOdd . ';
                }

                div.thm_groups_profile_container_list_row_even div.thm_groups_profile_container_profile_long_info,
                div.thm_groups_profile_container_list_row_even div.thm_groups_profile_container_profile_long_info li {
                    color: ' . $longInfoColorEven . ';
                }

                div.thm_groups_profile_container_profile_long_info {
                    ' . $longInfoStyles . '
                }

                div.thm_groups_profile_container_line {
                    ' . $textLineStyles . '
                }

                span.thm_groups_profile_container_line_label {
                    ' . $textLineLabelStyles . '
                }';
        return $out;
    }
}
