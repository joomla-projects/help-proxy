<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport. All rights reserved.
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
	 * Name of wiki page.
	 *
	 * @var    string
	 * @since  2.0
	 */
	private $pageName = null;

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
		$helper->call($this->getState('page'));

		// Follow wiki page redirections.
		$max = $params->get('max_redirects');
		$i = 0;

		while (($redirect = $helper->isRedirect()) && $i < $max)
		{
			$helper->call($redirect);
			$i++;
		}

		// If a non-English page was not found, try to fall back to English.
		if ($this->getState('lang') != 'en' && $helper->errorCode == 'missingtitle')
		{
			if (substr($this->pageName, -3, 1) == '/')
			{
				$this->pageName = substr($this->pageName, 0, -3);
				$helper->call($this->pageName);

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
		$helper->addTitle($this->pageName, $params->get('header_level'));

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
	 * Returns the current page name.
	 *
	 * @return	string	Wiki page name.
	 *
	 * @since   1.0
	 */
	public function getPage()
	{
		return $this->pageName;
	}
}
