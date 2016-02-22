<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Help View Class
 *
 * @since  2.0
 */
class HelpViewHelp extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   2.0
	 */
	public function display($tpl = null)
	{
		$canDo = JHelperContent::getActions('com_help');

		JToolbarHelper::title(JText::_('COM_HELP'), 'joomla help');

		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_help');
		}

		return parent::display($tpl);
	}
}
