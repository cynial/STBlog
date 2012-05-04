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
 <form action="<?php echo site_url('admin/settings/reading');?>" method="post" enctype="application/x-www-form-urlencoded">
<ul class="typecho-option">
<li>

<label class="typecho-label" for="post_date_format">
文章日期格式</label>
<input id="post_date_format" name="post_date_format" type="text" class="text" value="<?php echo set_value('post_date_format',$post_date_format); ?>" />
<p class="description">
此格式用于指定显示在文章归档中的日期默认显示格式.<br />
        在某些主题中这个格式可能不会生效, 因为主题作者可以自定义日期格式.<br />
        请参考<a href="http://cn.php.net/manual/zh/function.date.php">PHP日期格式写法</a>.</p>
</li>
</ul>

<ul class="typecho-option">
<li>
<label class="typecho-label" for="posts_page_size">
每页文章数目</label>
<input id="posts_page_size" name="posts_page_size" type="text" class="mini" value="<?php echo set_value('posts_page_size',$posts_page_size); ?>" />
<p class="description">
此数目用于指定文章归档输出时每页显示的文章数目.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="posts_list_size">
文章列表数目</label>
<input id="posts_list_size" name="posts_list_size" type="text" class="mini" value="<?php echo set_value('posts_list_size',$posts_list_size); ?>" />


<p class="description">
此数目用于指定显示在侧边拦中的文章列表数目.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="feed_full_text">
聚合全文输出</label>
<span>
<input name="feed_full_text" type="radio" value="0" id="feed_full_text" <?php echo set_radio('blog_status','0',(0 == intval($feed_full_text))?TRUE:FALSE); ?> />
<label for="feedFullText-0">
仅输出摘要</label>
</span>
<span>

<input name="feed_full_text" type="radio" value="1" id="feed_full_text" <?php echo set_radio('blog_status','1',(1 == intval($feed_full_text))?TRUE:FALSE); ?>/>
<label for="feedFullText-1">
全文输出</label>
</span>
<p class="description">
如果你不希望在聚合中输出文章全文,请使用仅输出摘要选项.<br />
        摘要的文字取决于你在文章中使用分隔符的位置.</p>
</li>
</ul>
<ul class="typecho-option typecho-option-submit" id="typecho-option-item-submit-4">
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