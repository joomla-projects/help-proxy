<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport. All rights reserved.
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
	 * Page output
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $data;

	/**
	 * Page name
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $pageName;

	/**
	 * Component params
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  1.0
	 */
	public $params;
}
