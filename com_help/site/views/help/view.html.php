<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 Open Source Matters, Inc. All rights reserved.
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
	protected $data;

	/**
	 * Page name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $pageName;

	/**
	 * Component params
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  1.0
	 */
	protected $params;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   2.1
	 */
	public function display($tpl = null)
	{
		/** @var JObject $state */
		$state = $this->get('state');

		$this->data     = $this->get('data');
		$this->pageName = $state->get('page');
		$this->params   = $state->get('params');

		return parent::display($tpl);
	}
}
