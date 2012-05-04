<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$this->load->view('admin/header');
$this->load->view('admin/menu');
?>
<div class="main">
    <div class="body body-950">
	<?php $this->load->view('admin/notify'); ?>
        <div class="container typecho-page-title">
			<div class="column-24 start-01">
				<h2><?php echo $page_title;?></h2>
				<p><?php echo anchor(site_url(),'查看我的站点');?></p>
			</div>
		</div>
		<div class="container typecho-page-main">
            <div class="column-22 start-02">
                <form action="<?php echo site_url('admin/settings/cache');?>" method="post" enctype="application/x-www-form-urlencoded">
<ul class="typecho-option">
<li>

<label class="typecho-label">
是否开启静态缓存</label>
<span>
<input name="cache_enabled" type="radio" value="0" id="cache_enabled" <?php echo set_radio('cache_enabled','0',(0 == intval($cache_enabled))?TRUE:FALSE); ?> />
<label for="cache_enabled">
不启用</label>
</span>
<span>
<input name="cache_enabled" type="radio" value="1" id="cache_enabled_1" <?php echo set_radio('cache_enabled','1',(1 == intval($cache_enabled))?TRUE:FALSE); ?>/>
<label for="cache_enabled_1">
启用</label>
</span>
<p class="description">
启用静态文件缓存可以加快页面显示，但频繁更新的数据可能会存在时延（不包括后台管理页面). 你可以<?php echo anchor(site_url('admin/settings/cacheClear'),'点击此处');?>手动清除缓存数据. 
推荐访问量大的网站开启此设置.
</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="cache_expire_time">
缓存自动刷新时间</label>
<?php echo form_error('cache_expire_time', '<p class="message error">', '</p>'); ?>
<input id="cache_expire_time" name="cache_expire_time" type="text" class="mini" value="<?php echo set_value('cache_expire_time',$cache_expire_time); ?>" /> 分钟
<p class="description">
缓存过期的时间间隔, 单位为<strong>分钟</strong>, 仅在开启缓存功能时有效.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="cache_file_limit">
页面缓存文件的最大个数</label>
<?php echo form_error('cache_file_limit', '<p class="message error">', '</p>'); ?>
<input id="cache_file_limit" name="cache_file_limit" type="text" class="mini" value="<?php echo set_value('cache_file_limit',$cache_file_limit); ?>" />
<p class="description">
为了防止静态文件过多导致服务器磁盘空间不够, 可以设置此值预防. 当缓存文件个数超过此值时, 所有缓存自动被删除。仅在开启缓存功能时有效.</p>
</li>
</ul>
<ul class="typecho-option typecho-option-submit">
<li>
<button type="submit">
保存设置</button>
</li>
</ul>
</form>
            </div>
        </div>
    </div>
</div>
<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
$this->load->view('admin/footer');
?>