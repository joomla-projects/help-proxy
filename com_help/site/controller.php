<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Help Component Base Controller
 *
 * @since  2.0
 */
class HelpController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see JFilterInput::clean().
	 *
	 * @return  $this
	 *
	 * @see     JFilterInput::clean()
	 * @since   2.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$cachable  = JFactory::getUser()->guest;
		$urlparams = ['keyref' => 'STRING', 'lang' => 'STRING'];

		return parent::display($cachable, $urlparams);
	}
}
