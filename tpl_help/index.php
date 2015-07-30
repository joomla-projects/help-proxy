<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/** @var JDocumentHtml $this */

$this->addStylesheet('templates/' . $this->template . '/css/reset.css');
$this->addStylesheet('templates/' . $this->template . '/css/help.css');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head"/>
</head>
<body><a name="Top" id="Top"></a>
<jdoc:include type="component"/>
<hr/>
<a href="<?php echo JUri::getInstance(); ?>#Top">Top</a>
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	var pageTracker = _gat._getTracker("UA-544070-3");
	pageTracker._initData();
	pageTracker._setDomainName("joomla.org");
	pageTracker._trackPageview();
</script>
</body>
</html>
