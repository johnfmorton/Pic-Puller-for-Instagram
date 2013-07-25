<?php

 /**
 * Pic Puller for Instagram
 *
 * @package			Ig_pic_puller
 * @author 			John Morton
 * @copyright		Copyright (c) 2012 - 2013, John Morton
 *
 */

if ( ! defined('PP_IG_NAME')) {
	define('PP_IG_NAME', 'Pic Puller for Instagram');
	define('PP_IG_PACKAGE', 'ig_picpuller');
	define('PP_IG_VERSION', '1.5.1');
	define('PP_IG_DOCS', 'http://picpuller.com');
}

$config['name'] = PP_IG_NAME;
$config['version']= PP_IG_VERSION;
$config['nsm_addon_updater']['versions_xml']='http://static.supergeekery.com/nsm_addon_updater/changelog_ig_picpuller.xml';