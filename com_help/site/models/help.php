<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::register('HelpHelper', dirname(__DIR__) . '/helpers/help.php');

/**
 * Help Component Model
 *
 * @since  2.0
 */
class HelpModelHelp extends JModelLegacy
{
	/**
	 * Wiki page data.
	 *
	 * @var    string
	 * @since  2.0
	 */
	private $data = null;

	/**
	 * Method to get a page from the wiki via the MediaWiki API.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getData()
	{
		if (!empty($this->data))
		{
			return $this->data;
		}

		// Get component parameters.
		/** @var \Joomla\Registry\Registry $params */
		$params = $this->getState('params');

		// Instantiate wiki API object.
		$helper = new HelpHelper($params->get('wiki_url', 'https://docs.joomla.org'));

		// Get page from the wiki.
		$helper->call($this->getState('page'), $this->getState('lang'));

		// Follow wiki page redirections.
		$max = $params->get('max_redirects');
		$i = 0;

		while (($redirect = $helper->isRedirect()) && $i < $max)
		{
			$helper->call($redirect);
			$i++;
		}

		// If a language coded page was not found, try to fall back to English.
		if ($helper->getLastResponse()->code !== 200)
		{
			if ($this->getState('lang') !== null)
			{
				$helper->call($this->getState('page'));

				// Follow wiki page redirections.
				$max = $params->get('max_redirects');
				$i = 0;

				while (($redirect = $helper->isRedirect()) && $i < $max)
				{
					$helper->call($redirect);
					$i++;
				}
			}
		}

		// Add page title.
		$helper->addTitle($this->getState('page'), $params->get('header_level'));

		// Remove links to unwritten articles.
		if ($params->get('remove_redlinks'))
		{
			$helper->removeRedLinks();
		}

		// Amend or remove links from wiki page.
		$helper->amendLinks();

		// Remove table of contents.
		if ($params->get('remove_toc'))
		{
			$helper->removeToc();
		}

		return $helper->getPage();
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   2.1
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		$this->setState('params', JComponentHelper::getParams('com_help'));

		$this->setState('page', $app->input->getString('keyref', 'Main_Page'));
		$this->setState('lang', $app->input->getString('lang', 'en'));
	}
}
