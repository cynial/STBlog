<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="typecho-head-guid body-950">
	<dl id="typecho:guid">
		<dt<?php echo ($parentPage=='dashboard')?' class="focus"':'';?>><?php echo anchor(site_url('admin/dashboard/'),'控制台',array('title'=>'控制台'));?></dt>
		<dd>
			<ul>
			<li<?php echo ($currentPage=='dashboard')?' class="focus"':'';?>><?php echo anchor(site_url('admin/dashboard/'),'概要',array('title'=>'概要'));?></li>
			<li<?php echo ($currentPage=='profile')?' class="focus"':'';?>><?php echo anchor(site_url('admin/profile/'),'个人设置',array('title'=>'个人设置'));?></li>
			<li<?php echo ($currentPage=='plugins')?' class="focus"':'';?>><?php echo anchor(site_url('admin/plugins/'),'插件',array('title'=>'插件'));?></li>
			<li<?php echo ($currentPage=='themes')?' class="focus"':'';?>><?php echo anchor(site_url('admin/themes/'),'外观',array('title'=>'外观'));?></li>
			</ul>
		</dd>

		<dt<?php echo ($parentPage=='post')?' class="focus"':'';?>><?php echo anchor(site_url('admin/posts/write/'),'创建',array('title'=>'创建'));?></dt>
		<dd>
			<ul>
			<li<?php echo ($currentPage=='post')?' class="focus"':'';?>><?php echo anchor(site_url('admin/posts/write/'),'撰写文章',array('title'=>'撰写文章'));?></li>
			<li<?php echo ($currentPage=='page')?' class="focus"':'';?>><?php echo anchor(site_url('admin/pages/write/'),'创建页面',array('title'=>'创建页面'));?></li>
			</ul>
		</dd>

		<dt<?php echo ($parentPage=='manage-posts')?' class="focus"':'';?>><?php echo anchor(site_url('admin/posts/manage/'),'管理',array('title'=>'管理'));?></dt>
		<dd>
		<ul>
			<li<?php echo ($currentPage=='manage-posts')?' class="focus"':'';?>><?php echo anchor(site_url('admin/posts/manage/'),'文章',array('title'=>'文章'));?></li>
			<li<?php echo ($currentPage=='manage-pages')?' class="focus"':'';?>><?php echo anchor(site_url('admin/pages/manage/'),'独立页面',array('title'=>'独立页面'));?></li>
			<li<?php echo ($currentPage=='manage-comments')?' class="focus"':'';?>><?php echo anchor(site_url('admin/comments/manage/'),'评论和引用',array('title'=>'评论和引用'));?></li>
			<li<?php echo ($currentPage=='manage-metas')?' class="focus"':'';?>><?php echo anchor(site_url('admin/metas/manage/'),'标签和分类',array('title'=>'标签和分类'));?></li>
			<li<?php echo ($currentPage=='manage-medias')?' class="focus"':'';?>><?php echo anchor(site_url('admin/medias/manage/'),'附件',array('title'=>'附件'));?></li>
			<li<?php echo ($currentPage=='manage-users')?' class="focus"':'';?>><?php echo anchor(site_url('admin/users/manage/'),'用户',array('title'=>'用户'));?></li>
		</ul>
		</dd>
		<dt<?php echo ($parentPage=='manage-settings')?' class="focus"':'';?>><?php echo anchor(site_url('admin/settings/general/'),'设置',array('title'=>'设置'));?></dt>
		<dd>
			<ul>
				<li<?php echo ($currentPage=='settings-general')?' class="focus"':'';?>><?php echo anchor(site_url('admin/settings/general/'),'基本',array('title'=>'基本'));?></li>
				<li<?php echo ($currentPage=='settings-discussion')?' class="focus"':'';?>><?php echo anchor(site_url('admin/settings/discussion/'),'评论',array('title'=>'评论'));?></li>
				<li<?php echo ($currentPage=='settings-reading')?' class="focus"':'';?>><?php echo anchor(site_url('admin/settings/reading/'),'文章',array('title'=>'文章'));?></li>
				<li<?php echo ($currentPage=='settings-cache')?' class="focus"':'';?>><?php echo anchor(site_url('admin/settings/cache/'),'静态缓存',array('title'=>'静态缓存'));?></li>
			</ul>
		</dd>
	</dl>
	<p class="operate">欢迎, <?php echo anchor(site_url('admin/profile/'),$this->user->name,array('class'=>'author important'));?><?php echo anchor(site_url('admin/login/logout'),'登出',array('class'=>'exit','title'=>'安全登出后台'));?></p>
</div>

