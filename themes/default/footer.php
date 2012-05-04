<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
	<div id="footer"><!-- //footer-->
		﻿<hr />
		<p class="left">&copy;2007-2010 <?php echo anchor(site_url(), setting_item('blog_title'));?>, All Rights Reserved. </p>
		<p class="right">Powered by <?php echo anchor('http://code.google.com/p/stblog/', ST_NAME .' '. ST_VERSION, array('title' => '基于CodeIgniter构建'));?></p>
		<p>&nbsp;</p>          
	</div><!-- end footer -->
</div><!-- end container -->
<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shCore.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushPhp.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushXml.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushCSharp.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushCss.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushJava.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushJScript.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushSql.js"></script>
	<script type="text/javascript" src="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/shBrushBash.js"></script>
	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = '<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>
</body>
</html>