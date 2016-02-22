<?php
/**
 * Joomla! Help Screen Proxy
 *
 * @copyright  Copyright (C) 2009 - 2014 Chris Davenport, (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/** @var HelpViewHelp $this */

$source_doc = $this->params->get('wiki_url', 'https://docs.joomla.org') . '/' . $this->pageName;

echo $this->data;

?>
<div id="footer-wrapper">
<div id="license">License: <a href="https://docs.joomla.org/JEDL">Joomla! Electronic Documentation License</a></div>
<div id="source-page">Source page: <a href="<?php echo $source_doc; ?>"><?php echo $source_doc; ?></a></div>
<div id="copyright">Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.opensourcematters.org">Open Source Matters, Inc.</a> All rights reserved.
</div>
</div>
