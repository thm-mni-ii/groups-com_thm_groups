<?php

class THM_GroupsBootstrap_Helper extends JHtmlBootstrap
{
	/**
	 * Loads CSS files needed by Bootstrap
	 *
	 * @param   boolean $includeMainCss If true, main bootstrap.css files are loaded
	 * @param   string  $direction      rtl or ltr direction. If empty, ltr is assumed
	 * @param   array   $attribs        Optional array of attributes to be passed to JHtml::_('stylesheet')
	 *
	 * @return  void
	 *
	 */
	public static function loadCss($includeMainCss = true, $direction = 'ltr', $attribs = array())
	{
		// Load Bootstrap main CSS
		if ($includeMainCss)
		{
			JHtml::_('stylesheet', 'jui/bootstrap.min.css', $attribs, true);
			JHtml::_('stylesheet', 'jui/bootstrap-responsive.min.css', $attribs, true);
			//JHtml::_('stylesheet', 'jui/bootstrap-extended.css', $attribs, true);
		}

		// Load Bootstrap RTL CSS
		if ($direction === 'rtl')
		{
			JHtml::_('stylesheet', 'jui/bootstrap-rtl.css', $attribs, true);
		}
	}
}