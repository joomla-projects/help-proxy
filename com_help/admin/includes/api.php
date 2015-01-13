<?php
/**
 * @version		$Id:$
 * @package		Joomla
 * @subpackage	Help
 * @copyright	Copyright (C) 2009-2014 Chris Davenport. All rights reserved.
 * @license		GNU/GPL version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class HelpApi
{
	/**
	 * Wiki JURI object.
	 * This is the base URI of the wiki (omitting any index.php).
	 */
	protected $_wiki_uri = null;

	/**
	 * Wiki API JURI object.
	 * This is the base URI of the wiki API.
	 */
	protected $_api_uri = null;

	/**
	 * Response data.
	 */
	protected $_data = null;

	/**
	 * Current page for rendering.
	 */
	protected $_page = null;

	/**
	 * Array of categories that the current page belongs to.
	 */
	protected $_categories = null;

	/**
	 * Error code.
	 */
	public $errorCode = '';

	/**
	 * Error message.
	 */
	public $errorMessage = '';

	/**
	 * Constructor.
	 *
	 * @param	string	URL of help wiki.
	 */
	public function __construct( $wiki_url = 'http://docs.joomla.org' )
	{
		// TODO grab the URI from the component parameters instead of hard-wiring as below.
		$this->_wiki_uri = new JURI( $wiki_url );
		$this->_api_uri  = new JURI( $wiki_url . '/api.php' );
	}

	/**
	 * Make a call to the remote MediaWiki API.
	 *
	 * @param	string	Key reference of help page to retrieve.
	 * @param	integer	Cache lifetime (in seconds).
	 *
	 * @return	Boolean	True if call was successful; false otherwise.
	 */
	public function call( $keyref, $cache_lifetime = 0 )
	{
		$this->errorCode = '';
		$this->errorMessage = '';
		$this->_page = null;
		$this->_categories = null;

		// Build the request URI.
		$query = array(
					'action' => 'parse',
					'format' => 'json',
					'page'	 => $keyref
					);
		$this->_api_uri->setQuery( $this->_api_uri->buildQuery( $query ) );

		if ($cache_lifetime)
		{
			// Get JCache object.
			$cache = JFactory::getCache();

			// Set cache lifetime (in seconds).
			$cache->setLifeTime( (int) $cache_lifetime );

			// Make a cachable call to the wiki API.
			$this->_data = $cache->call( array( 'HelpApi', '_call' ), $this->_api_uri );
		}
		else
		{
			// Make a non-cachable call to the wiki API.
			$this->_data = $this->_call( $this->_api_uri );
		}

		// Extract error code/message if there was one.
		if (isset($this->_data->error))
		{
			$this->errorCode = $this->_data->error->code;
			$this->errorMessage = $this->_data->error->info;

			return false;
		}

		// Extract the page text itself.
		$this->_page = $this->_data->parse->text->{'*'};

		// Extract the categories this page belongs to.
		$this->_categories = $this->_data->parse->categories;

		return true;
	}

	/**
	 * Make a call to the remote MediaWiki API.
	 *
	 * @param	object	JURI object containing URI to retrieve.
	 *
	 * @return	object	Wiki data object.
	 */
	public function _call( $uri )
	{
		// Include the HTTP client class.
		$option = JRequest::getCmd( 'option' );
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'includes'.DS.'httpclient'.DS.'http.php' );

		// Instantiate the HTTP client.
		$http = new http_class();

		// Get the page from the wiki in JSON format via the wiki API.
		$error = $http->GetRequestArguments( $uri->toString(), $arguments );
		$error = $http->Open( $arguments );
		if ($error == '')
		{
			$error = $http->SendRequest( $arguments );
			for(;;)
			{
				$error = $http->ReadReplyBody( $body, 1000 );
				if ($error != '' || strlen( $body ) == 0) break;
				$data .= $body;
			}
			$http->Close();
		}

		// Return decoded JSON data as PHP object.
		return json_decode( $data );
	}

	/**
	 * Check if current page contains a REDIRECT.
	 * If a REDIRECT is present, returns the wiki page name to redirect to.
	 *
	 * @return	string	Page name to redirect to; false otherwise.
	 */
	public function isRedirect()
	{
		$pattern = '!<li>REDIRECT <a href="' . $this->_wiki_uri->getPath() . '/([^"]+)"!';
		if (preg_match( $pattern, $this->_page, $matches ))
		{
			return $matches[1];
		}

		return false;
	}

	/**
	 * Add page title.
	 *
	 * @param	string	Page title to add.
	 * @param	integer	HTML header level.
	 */
	public function addTitle( $title, $level = 2 )
	{
		if ((int) $level)
		{
			$this->_page = '<h' . $level . '>' . $title . '</h'. $level .'>' . "\n" . $this->_page;
		}
	}

	/**
	 * Remove links to pages that have not yet been written (replace with just the text instead).
	 */
	public function removeRedLinks()
	{
		// Remove red links.
		$redlink = '!<a href="' . $this->_wiki_uri->getPath() . '/index.php\?title=([^&]+)\&amp;';
		$redlink .= 'action=edit&amp;redlink=1" class="new" title="([^"]+) \(([^)]+)\)">([^<]+)</a>!';
		$this->_page = preg_replace( $redlink, '$4', $this->_page );
	}

	/**
	 * Amend or remove links from a wiki page.
	 */
	public function amendLinks()
	{
		$langcode = JRequest::getCmd('lang', 'en');

		// Remove links to wiki image information pages.
		$imglink = '!<a href="' . $this->_wiki_uri->getPath() . '/Image:([^>]+)>(.+)</a>!';
		$this->_page = preg_replace( $imglink, '$2', $this->_page );

		// Replace links to other wiki pages with links to the proxy.
		$uri = JFactory::getURI();
		$pattern = '<a href="' . $this->_wiki_uri->getPath() . '/';
		$replace = '<a href="';
		$replace .= $uri->toString( array( 'path' ) );

		if ($langcode == 'en' || $langcode == '')
		{
			$replace .= '?option=' . JRequest::getCmd( 'option' ) . '&view=help&keyref=';
			$this->_page = str_replace( $pattern, $replace, $this->_page );
			$this->_page = str_replace('Special:MyLanguage/', '', $this->_page);
		}
		else
		{
			$replace .= '?option=' . JRequest::getCmd( 'option' ) . '&view=help&lang=' . $langcode . '&keyref=';
			$this->_page = str_replace( $pattern, $replace, $this->_page );

			$pattern = '!Special:MyLanguage/(.+)"!U';
			$this->_page = preg_replace( $pattern, '$1/' . $langcode . '"', $this->_page );
		}

		// Replace relative links to images with absolute links to the wiki that bypass the proxy.
		$replace = 'src="' . $this->_wiki_uri->toString( array( 'scheme', 'host', 'path' ) ) . '/images/';
		$pattern = 'src="' . $this->_wiki_uri->getPath() . '/images/';
		$this->_page = str_replace( $pattern, $replace, $this->_page );

		// Remove [edit] links.
		$pattern = '!<span class="editsection">(.+)</span>!msU';
		$this->_page = preg_replace( $pattern, '', $this->_page );

		// Remove <translate> and </translate> tags.
		$this->_page = str_replace(array('&lt;translate&gt;', '&lt;/translate&gt;'), '', $this->_page);
	}

	/**
	 * Remove table of contents.
	 */
	public function removeToc()
	{
		// Remove table of contents.
		$toc = '!<table id="toc" class="toc">(.+)</table>!msU';
		$this->_page = preg_replace( $toc, '', $this->_page );

		// Remove navbox too.
		$toc = '!<table cellspacing="0" class="navbox"(.+)</table>!msU';
		$this->_page = preg_replace( $toc, '', $this->_page );
	}

	/**
	 * Return page data for rendering.
	 */
	public function getPage()
	{
		return $this->_page;
	}

	/**
	 * Return array of categories that the current page belongs to.
	 */
	public function getCategories()
	{
		return $this->_categories;
	}
}