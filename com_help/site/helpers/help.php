<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Help Component Helper Class
 *
 * @since  2.0
 */
class HelpHelper
{
	/**
	 * Wiki API JUri object.  This is the base URI of the wiki API.
	 *
	 * @var    JUri
	 * @since  2.0
	 */
	private $api_uri = null;

	/**
	 * The last JHttpResponse object from the API call
	 *
	 * @var    JHttpResponse
	 * @since  3.0
	 */
	private $lastResponse;

	/**
	 * Current page for rendering.
	 *
	 * @var    string
	 * @since  2.0
	 */
	private $page = null;

	/**
	 * Wiki JUri object.  This is the base URI of the wiki (omitting any index.php).
	 *
	 * @var    JUri
	 * @since  2.0
	 */
	private $wiki_uri = null;

	/**
	 * Constructor.
	 *
	 * @param   string  $wiki_url  URL of help wiki.
	 *
	 * @since   1.0
	 */
	public function __construct($wiki_url)
	{
		$this->wiki_uri = new JUri($wiki_url);
		$this->api_uri  = new JUri($wiki_url . '/index.php');
	}

	/**
	 * Make a call to the remote MediaWiki API.
	 *
	 * @param   string  $keyref  Key reference of help page to retrieve.
	 * @param   string  $lang    An optional language code for requesting a translated page.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function call($keyref, $lang = null)
	{
		// Build the request URI.
		$query = [
			'action' => 'render',
			'title'  => $keyref
		];

		// Append the language code if present
		if ($lang !== null)
		{
			$query['title'] .= "/$lang";
		}

		$this->api_uri->setQuery($this->api_uri->buildQuery($query));

		$this->page = $this->requestData();

		return true;
	}

	/**
	 * Get the last response from the API
	 *
	 * @return  JHttpResponse
	 *
	 * @since   3.0
	 */
	public function getLastResponse()
	{
		return $this->lastResponse;
	}

	/**
	 * Request data from the remote server
	 *
	 * @return  mixed
	 *
	 * @since   2.0
	 */
	private function requestData()
	{
		$options = new Registry;
		$options->set('userAgent', 'HelpProxy/2.0');
		$options->set('follow_location', false);

		try
		{
			$response = JHttpFactory::getHttp($options)->get($this->api_uri->toString());
		}
		catch (Exception $e)
		{
			return '';
		}

		$this->lastResponse = $response;

		return $response->body;
	}

	/**
	 * Check if current page contains a REDIRECT.
	 *
	 * If a REDIRECT is present, returns the wiki page name to redirect to.
	 *
	 * @return  string|boolean  Page name to redirect to; false otherwise
	 *
	 * @since   1.0
	 */
	public function isRedirect()
	{
		$pattern = '!<li>REDIRECT <a href="' . $this->wiki_uri->getPath() . '/([^"]+)"!';

		if (preg_match($pattern, $this->page, $matches))
		{
			return $matches[1];
		}

		return false;
	}

	/**
	 * Add page title.
	 *
	 * @param   string   $title  Page title to add.
	 * @param   integer  $level  HTML header level.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addTitle($title, $level = 2)
	{
		if ((int) $level)
		{
			$this->page = '<h' . $level . '>' . $title . '</h' . $level . '>' . "\n" . $this->page;
		}
	}

	/**
	 * Remove links to pages that have not yet been written (replace with just the text instead).
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function removeRedLinks()
	{
		// Remove red links.
		$redlink = '!<a href="' . $this->wiki_uri->getPath() . '/index.php\?title=([^&]+)\&amp;action=edit&amp;redlink=1" class="new" title="([^"]+) \(([^)]+)\)">([^<]+)</a>!';
		$this->page = preg_replace($redlink, '$4', $this->page);
	}

	/**
	 * Amend or remove links from a wiki page.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function amendLinks()
	{
		// Remove links to wiki image information pages.
		$imglink = '!<a href="' . $this->wiki_uri->getPath() . '/([^>]+)" class="image">(.+)</a>!';
		//$this->page = preg_replace($imglink, '$2', $this->page);

		// Remove links for new image uploads
		$imgUploadlink = '!<a href="' . $this->wiki_uri->getPath() . '/([^>]+)" class="new"(.+)>(.+)</a>!';
		$this->page = preg_replace($imgUploadlink, '$3', $this->page);

		// Remove <translate> </translate> and translation markers from page output.
		$translationTags = '!(<(\/|)translate>|<!--T:\d+-->)+!';
		$this->page = preg_replace($translationTags, '', $this->page);

		// Remove Special:MyLanguage/ or Special:MyLanguage/: from page links.
		$specialMyLanguage = '!(Special:MyLanguage\/(:)?)+!';
		$this->page = preg_replace($specialMyLanguage, '', $this->page);

		// Replace links to other wiki pages with links to the proxy.
		$uri = JUri::getInstance();
		$replace = '<a href="' . $uri->toString(array('path')) . '?option=com_help&view=help&keyref=';
		$pattern = '<a href="//' . $this->wiki_uri->getHost() . '/';
		$this->page = str_replace($pattern, $replace, $this->page);

		// Replace relative links to images with absolute links to the wiki that bypass the proxy.
		$replace = $this->wiki_uri->toString(array('scheme', 'host', 'path')) . '/images/';
		$pattern = $this->wiki_uri->getPath() . '/images/';
		$this->page = str_replace($pattern, $replace, $this->page);

		// Remove [edit] links.
		$pattern = '!<span class="mw-editsection-bracket">\[</span>(.+)<span class="mw-editsection-bracket">\]</span></span>!msU';
		$this->page = preg_replace($pattern, '', $this->page);

		// Replace any anchor based links
		$pattern = '<a href="#';
		$replace = '<a href="' . $uri->toString() . '#';
		$this->page = str_replace($pattern, $replace, $this->page);
	}

	/**
	 * Remove table of contents.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function removeToc()
	{
		// Remove table of contents.
		$toc = '!<table id="toc" class="toc">(.+)</table>!msU';
		$this->page = preg_replace($toc, '', $this->page);

		// Remove navbox too.
		$toc = '!<table cellspacing="0" class="navbox"(.+)</table>!msU';
		$this->page = preg_replace($toc, '', $this->page);
	}

	/**
	 * Return page data for rendering.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getPage()
	{
		return $this->page;
	}
}
