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
                <form action="<?php echo site_url('admin/settings/discussion');?>" method="post" enctype="application/x-www-form-urlencoded">
<ul class="typecho-option">
<li>

<label class="typecho-label" for="comments_date_format">
评论日期格式</label>
<input id="comments_date_format" name="comments_date_format" type="text" class="text" value="<?php echo set_value('comments_date_format',$comments_date_format); ?>" />
<p class="description">
这是一个默认的格式,当你在模板中调用显示评论日期方法时, 如果没有指定日期格式, 将按照此格式输出.<br />
        具体写法请参考<a href="http://cn.php.net/manual/zh/function.date.php">PHP日期格式写法</a>.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="comments_list_size">

评论列表数目</label>
<input id="comments_list_size" name="comments_list_size" type="text" class="mini" value="<?php echo set_value('comments_list_size',$comments_list_size); ?>" />
<p class="description">
此数目用于指定显示在侧边拦中的评论列表数目.</p>
</li>
</ul>
<ul class="typecho-option">
<li>

<label class="typecho-label">
是否对评论者个人主页链接使用nofollow属性</label>
<span>
<input name="comments_url_no_follow" type="radio" value="0" id="comments_url_no_follow" <?php echo set_radio('comments_url_no_follow','0',(0 == intval($comments_url_no_follow))?TRUE:FALSE); ?>/>
<label for="comments_url_no_follow">
不启用</label>
</span>
<span>
<input name="comments_url_no_follow" type="radio" value="1" id="comments_url_no_follow_1" <?php echo set_radio('comments_url_no_follow','1', (1 == intval($comments_url_no_follow))?TRUE:FALSE); ?> />
<label for="comments_url_no_follow_1">
启用</label>
</span>
<p class="description">
当评论作者的个人主页地址在你的网站上呈现时, 其在搜索引擎中可能被识别为外链地址.<br />

        过多的外链地址将导致你的网站在搜索引擎中被降权, 打开此选项能帮助你解决此问题.<br />
        更多关于nofollow的信息请参考<a href="http://en.wikipedia.org/wiki/Nofollow">wikipedia上的解释</a>.</p>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label">
评论审核</label>
<span>
<input name="comments_require_moderation" type="radio" value="0" id="comments_require_moderation" <?php echo set_radio('comments_require_moderation', '0', (0 == intval($comments_require_moderation))?TRUE:FALSE); ?> />
<label for="comments_require_moderation">
不启用</label>

</span>
<span>
<input name="comments_require_moderation" type="radio" value="1" id="comments_require_moderation_1" <?php echo set_radio('comments_require_moderation', '1', (1 == intval($comments_require_moderation))?TRUE:FALSE); ?> />
<label for="comments_require_moderation_1">
启用</label>
</span>
<p class="description">
打开此选项后,所有提交的评论,引用通告和广播将不会立即呈现, 而是被标记为待审核, 你可以在后台标记它们是否呈现.</p>
</li>
</ul>
<ul class="typecho-option">
<li>

<label class="typecho-label" for="comments_auto_close">
在文章发布一段时间后自动关闭反馈功能</label>
<select name="comments_auto_close" id="comments_auto_close">
<option value="0" <?php echo set_select('comments_auto_close', '0', (0 == intval($comments_auto_close))?TRUE:FALSE); ?>>
永不关闭</option>
<option value="86400" <?php echo set_select('comments_auto_close', '86400', (86400 == intval($comments_auto_close))?TRUE:FALSE); ?>>
一天后关闭</option>
<option value="259200" <?php echo set_select('comments_auto_close', '259200', (259200 == intval($comments_auto_close))?TRUE:FALSE); ?>>
三天后关闭</option>
<option value="1296000" <?php echo set_select('comments_auto_close', '1296000', (1296000 == intval($comments_auto_close))?TRUE:FALSE); ?>>
半个月后关闭</option>
<option value="2592000" <?php echo set_select('comments_auto_close', '2592000', (2592000 == intval($comments_auto_close))?TRUE:FALSE); ?>>

一个月后关闭</option>
<option value="7776000" <?php echo set_select('comments_auto_close', '7776000', (7776000 == intval($comments_auto_close))?TRUE:FALSE); ?>>
三个月后关闭</option>
<option value="15552000" <?php echo set_select('comments_auto_close', '15552000', (15552000 == intval($comments_auto_close))?TRUE:FALSE); ?>>
半年后关闭</option>
<option value="31536000" <?php echo set_select('comments_auto_close', '31536000', (31536000 == intval($comments_auto_close))?TRUE:FALSE); ?>>
一年后关闭</option>
</select>
<p class="description">
打开此选项后, 发布时间超过此设置文章的反馈功能将被关闭.<br />
        此选项可以帮助你抵御一部分垃圾评论, 但也有可能会让你失去一部分优秀的评论.</p>

</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label">
必须填写邮箱</label>
<span>
<input name="comments_require_mail" type="radio" value="0" id="comments_require_mail" <?php echo set_radio('comments_require_mail', '0', (0 == intval($comments_require_mail))?TRUE:FALSE); ?> />
<label for="comments_require_mail">
不需要</label>
</span>
<span>
<input name="comments_require_mail" type="radio" value="1" id="comments_require_mail_1" <?php echo set_radio('comments_require_mail', '1', (1 == intval($comments_require_mail))?TRUE:FALSE); ?> />
<label for="comments_require_mail_1">
需要</label>

</span>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label">
必须填写网址</label>
<span>
<input name="comments_require_url" type="radio" value="0" id="comments_require_url" <?php echo set_radio('comments_require_url', '0', (0 == intval($comments_require_url))?TRUE:FALSE); ?> />
<label for="comments_require_url">
不需要</label>
</span>
<span>
<input name="comments_require_url" type="radio" value="1" id="comments_require_url_1" <?php echo set_radio('comments_require_url', '1', (1 == intval($comments_require_url))?TRUE:FALSE); ?> />
<label for="comments_require_url_1">

需要</label>
</span>
</li>
</ul>
<ul class="typecho-option">
<li>
<label class="typecho-label" for="comments_allowed_html">
允许使用的HTML标签和属性</label>
<textarea id="comments_allowed_html" name="comments_allowed_html"><?php echo set_value('comments_allowed_html',(!empty($comments_allowed_html)?$comments_allowed_html:'')); ?></textarea>
<p class="description">
默认的用户评论不允许填写任何的HTML标签, 你可以在这里填写允许使用的HTML标签.<br />
        比如: &lt;a href=&quot;&quot;&gt; &lt;img src=&quot;&quot;&gt; &lt;blockquote&gt;</p>

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