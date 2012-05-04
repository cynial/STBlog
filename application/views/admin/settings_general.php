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
                <form action="<?php echo site_url('admin/settings/general');?>" method="post" enctype="application/x-www-form-urlencoded">
<ul class="typecho-option">
<li>

<label class="typecho-label" for="blog_title">
站点名称</label>
<input id="blog_title" name="blog_title" type="text" class="text" value="<?php echo set_value('blog_title',$blog_title); ?>"/>
<p class="description">
站点的名称将显示在网页的标题处.</p>
</li>
</ul>
<ul class="typecho-option">
<li>

<label class="typecho-label" for="blog_slogan">
站点口号</label>
<input id="blog_slogan" name="blog_slogan" type="text" class="text" value="<?php echo set_value('blog_slogan',$blog_slogan); ?>"/>
<p class="description">
用一句话描述你的站点，它将显示在网页的头部.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="blog_description">
站点描述</label>
<textarea id="blog_description" name="blog_description">
<?php echo set_value('blog_description',$blog_description); ?>
</textarea>
<p class="description">
站点描述将显示在首页网页代码的Meta标签中(主要用于SEO).</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="blog_keywords">
关键词</label>
<input id="blog_keywords" name="blog_keywords" type="text" class="text" value="<?php echo set_value('blog_keywords',$blog_keywords); ?>" />
<p class="description">
请以半角逗号","分割多个关键字.</p>
</li>
</ul>
<ul class="typecho-option">
<li>

<label class="typecho-label">
是否关闭站点</label>
<span>
<input name="blog_status" id="blog_status_0" type="radio" value="on" <?php echo set_radio('blog_status','on',('on' == $blog_status)?TRUE:FALSE); ?> />
<label for="blog_status_0">
不关闭</label>
</span>
<span>
<input name="blog_status" type="radio" value="off" id="blog_status_1" <?php echo set_radio('blog_status','off',('off' == $blog_status)?TRUE:FALSE ); ?> />
<label for="blog_status_1">
关闭</label>
</span>
<p class="description">
是否暂时关闭你的站点，请仅在河蟹时期使用此选项.</p>

</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="offline_reason">
站点关闭原因</label>
<textarea id="offline_reason" name="offline_reason">
<?php echo set_value('offline_reason',$offline_reason); ?>
</textarea>
<p class="description">
告诉读者为何关闭你的站点，仅在站点关闭时有效.</p>
</li>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="upload_dir">
上传文件存放路径</label>
<input id="upload_dir" name="upload_dir" type="text" class="text" value="<?php echo set_value('upload_dir',$upload_dir); ?>" />
<p class="description">
相对于程序根目录的相对路径，以 / 结尾.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="upload_exts">
允许上传的文件类型</label>
<input id="upload_exts" name="upload_exts" type="text" class="text" value="<?php echo set_value('upload_exts',$upload_exts); ?>" />
<p class="description">
用分号 ; 隔开, 例如: *.zip;*.jpg</p>
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