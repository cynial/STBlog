<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<form action="<?php echo site_url('admin/profile/updateProfile');?>" method="post" enctype="application/x-www-form-urlencoded">
	<ul class="typecho-option">
		<li>
			<label class="typecho-label" for="screenName">昵称</label>
			<input id="screenName" name="screenName" type="text" class="text" value="<?php echo $this->user->screenName;?>" />
			<p class="description">用户昵称可以与用户名不同, 用于前台显示.<br />如果你将此项留空,将默认使用用户名.</p>
			<?php echo form_error('screenName', '<p class="message error">', '</p>'); ?>
		</li>
	</ul>
	<ul class="typecho-option">
		<li>
			<label class="typecho-label" for="url">个人主页地址</label>
			<input id="url" name="url" type="text" class="text" value="<?php echo $this->user->url;?>" />
			<?php echo form_error('url', '<p class="message error">', '</p>'); ?>
		</li>
	</ul>
	<ul class="typecho-option">
		<li>
			<label class="typecho-label" for="mail">电子邮箱地址*</label>
			<input id="mail" name="mail" type="text" class="text" value="<?php echo $this->user->mail;?>" />
			<p class="description">电子邮箱地址将作为此用户的主要联系方式.<br />请不要与系统中现有的电子邮箱地址重复.</p>
			<?php echo form_error('mail', '<p class="message error">', '</p>'); ?>
		</li>
	</ul>
	<ul class="typecho-option typecho-option-submit">
		<li>
			<button type="submit">更新我的档案</button>
		</li>
	</ul>
</form>