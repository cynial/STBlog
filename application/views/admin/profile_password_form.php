<form action="<?php echo site_url('admin/profile/updatePassword');?>" method="post" enctype="application/x-www-form-urlencoded">
	<ul class="typecho-option">
		<li>
			<label class="typecho-label" for="password">用户密码</label>
			<input id="password" name="password" type="password" class="password" />
			<p class="description">为此用户分配一个密码.<br />建议使用特殊字符与字母的混编样式,以增加系统安全性.</p>
			<?php echo form_error('password', '<p class="message error">', '</p>'); ?>
		</li>
	</ul>
	<ul class="typecho-option">
		<li>
			<label class="typecho-label" for="confirm">用户密码确认</label>
			<input id="confirm" name="confirm" type="password" class="password" />
			<p class="description">请确认你的密码, 与上面输入的密码保持一致.</p>
			<?php echo form_error('confirm', '<p class="message error">', '</p>'); ?>
		</li>
	</ul>
	<ul class="typecho-option typecho-option-submit">
		<li>
			<button type="submit">更新密码</button>
		</li>
	</ul>
</form>