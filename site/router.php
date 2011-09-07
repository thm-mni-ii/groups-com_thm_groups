<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/

//erstellt die Route für SEF
function THM_GroupsBuildRoute(&$query)
{
	//enthält nachher alle SEF Elemente als Liste
	$segments = array();
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	};

	if(isset($query['layout']))
	{
		$segments[] = $query['layout'];
		unset($query['layout']);
	};
	//var_dump($query);
	if(isset($query['gsuid']))
	{
		if(isset($query['name'])){
			if(isset($query['gsgid'])){
				$segments[] = $query['gsgid']."-".$query['gsuid'] ."-". $query['name'];
				unset($query['gsgid']);
			}
			else
				$segments[] = $query['gsuid'] ."-". $query['name'];
			unset($query['gsuid']);
			unset($query['name']);
		}
		else{
			$segments[] = $query['gsuid'];
			unset($query['gsuid']);
		}
	};
	return $segments;
}

//berechnet wieder den parametrisierten Teil aus SEF
function THM_GroupsParseRoute($segments)
{
	$vars = array();
	if($segments[0]!=""){
		if(isset($segments[2]))
			$arrVar1=explode(':',$segments[2]);
		$vars['view']=$segments[0];
		$vars['layout']=$segments[1];
		if(isset($arrVar1[0])) {
			$vars['gsgid']=$arrVar1[0];
			$arrVar2 = explode('-',$arrVar1[1]);
			$vars['gsuid']=$arrVar2[0];
			$vars['name']=$arrVar2[1];
		}
	}
return $vars;
}
