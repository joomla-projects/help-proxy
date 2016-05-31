<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/** @var JDocumentHtml $this */

$this->addStyleSheet('templates/' . $this->template . '/css/reset.css');
$this->addStyleSheet('templates/' . $this->template . '/css/help.css');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head"/>
	</head>
	<body>
		<!-- Google Tag Manager -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T742X9" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-T742X9');</script>
		<!-- End Google Tag Manager -->
		<a name="Top" id="Top"></a>
		<jdoc:include type="component"/>
		<hr/>
		<a href="<?php echo JUri::getInstance(); ?>#Top">Top</a>
	</body>
</html>
