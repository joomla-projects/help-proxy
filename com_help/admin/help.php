<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$controller = JControllerLegacy::getInstance('Help');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
