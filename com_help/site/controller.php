<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 Open Source Matters, Inc. All rights reserved.
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
		$viewName = $this->input->getCmd('view', 'help');

		/** @var HelpViewHelp $viewObject */
		$viewObject = $this->getView($viewName, 'html');

		// Populate the model state object
		$state = new JObject;
		$state->params = JComponentHelper::getParams('com_help');
		$state->page   = $this->input->getString('keyref', 'Main_Page');
		$state->lang   = $this->input->getString('lang', 'en');

		/** @var HelpModelHelp $model */
		$model = $this->getModel($viewName, '', array('state' => $state));

		// Load the data
		$data = $model->getData();

		// Register the data into the view
		$viewObject->data     = $data;
		$viewObject->params   = JComponentHelper::getParams('com_help');
		$viewObject->pageName = $this->input->getString('keyref', 'Main_Page');

		// Display the view.
		$viewObject->display();
	}
}
