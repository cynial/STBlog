<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: ckeditor编辑器
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 一个优秀的所见即所得编辑器
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Editor 
{
	public function __construct(&$plugin)
	{
		//第三方可视化编辑器必须注册的钩子之一：实例化编辑器
		$plugin->register(ST_CORE_HOOK_EDITOR, $this, 'render');
		//第三方可视化编辑器必须注册的钩子之二：上传附件插入编辑器
		$plugin->register(ST_CORE_HOOK_EDITOR_INSERT_ATTACH, $this, 'insert_attachment');
		$plugin->register(ST_CORE_HOOK_EDITOR_INSERT_MORE, $this, 'insert_more');
	}
	
	public function render()
	{
		$url = base_url() . ST_PLUGINS_DIR . '/editor/';
		
		echo <<<EOT
<script language="javascript" type="text/javascript" src="{$url}ckeditor/ckeditor.js"></script>
<script type="text/javascript">
		CKEDITOR.replace('text');
</script>
EOT;

	}
	
	public function insert_attachment()
	{
		echo <<<EOT
var insertImageToEditor = function (title, url, link) {
if ( CKEDITOR.instances.text.mode == 'wysiwyg' ) {
CKEDITOR.instances.text.insertHtml('<img src=\"' + url + '\" alt=\"' + title + '\" />') ;
}
else
{
	alert('请先转换到所见即所得模式') ;
}
};

var insertLinkToEditor = function (title, url, link) {
if ( CKEDITOR.instances.text.mode == 'wysiwyg' ) {
CKEDITOR.instances.text.insertHtml('<a href=\"' + url + '\" title=\"' + title + '\">' + title + '</a>') ;
}
else
{
	alert('请先转换到所见即所得模式') ;
}
};
EOT;
		
	}
	
	public function insert_more()
	{
echo <<<EOT
<script type="text/javascript">
function InsertHTML(value)
{
	// Get the editor instance that we want to interact with.
	var oEditor = CKEDITOR.instances.text;
	// Check the active editing mode.
	if (oEditor.mode == 'wysiwyg' )
	{
		// Insert the desired HTML.
		oEditor.insertHtml( value ) ;
	}
	else
		alert( '请先转换到所见即所得模式' ) ;
}
</script>
<a href="javascript: InsertHTML('[--break--]');" title="用于割断长内容在列表页的显示和Feed摘要输出">插入摘要分割符</a>
EOT;
	}
}