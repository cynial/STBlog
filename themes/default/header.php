<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo setting_item('blog_title');?> &raquo; <?php echo $page_title;?></title>
	<meta name="Keywords" content="<?php echo $page_keywords;;?>" />
	<meta name="Description" content="<?php echo $page_description;?>" />
	<meta name="generator" content="<?php echo ST_NAME.' '.ST_VERSION;?>" />
	<meta name="template" content="default" />
	<link href="<?php echo base_url(). ST_THEMES_DIR . '/' . setting_item('current_theme').'/css/style.css';?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo base_url(). ST_THEMES_DIR . '/' . setting_item('current_theme').'/js/common.js';?>"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/styles/shCore.css"/>
	<link type="text/css" rel="stylesheet" href="<?php echo base_url(). ST_PLUGINS_DIR;?>/editor/syntaxhighlighter/styles/shThemeDefault.css"/>
	
	<?php echo $parsed_feed;?>
	<?php echo isset($extra_header)?$extra_header:'';?>
</head>
<body onLoad="ReImgSize()">

<div id="container">
	<div id="header"><!-- header -->
		<form id="search" method="get" action="<?php echo site_url('search');?>">
			<div><input type="text" name="s" class="text" size="25" /> <input type="submit" class="submit" value="Search" /></div>
	    </form>
    	<h1><?php echo anchor(site_url(), setting_item('blog_title'));?></h1>
        <h2><?php echo setting_item('blog_slogan');?>
        	<?php $this->plugin->trigger('hook_test');?>
        </h2>
                
        <br />
        <div id="topbar">
        	<ul>
			    <li><?php echo anchor(site_url(), '首页');?></li>
			    <?php $this->plugin->trigger('Widget::Navigation', '<li><a href="{permalink}">{title}</a></li>');?>
			</ul>
		</div>

    </div><!-- //header -->

        
