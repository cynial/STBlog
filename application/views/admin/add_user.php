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
                <form action="" method="post" enctype="application/x-www-form-urlencoded">

				<ul class="typecho-option" id="typecho-option-item-name-0">
				<li>
				<label class="typecho-label" for="name-0-1">
				用户名*</label>
				<input id="name-0-1" name="uname" type="text" class="text" <?php if(isset($uid) && is_numeric($uid)){echo 'readonly';}?> value="<?php echo set_value('uname',(isset($uname))?$uname:''); ?>"/>
				<p class="description">
				此用户名将作为用户登录时所用的名称.<br />
				        请不要与系统中现有的用户名重复.</p>
				<?php echo form_error('name', '<p class="message error">', '</p>'); ?>
				</li>
				</ul>
				<ul class="typecho-option" id="typecho-option-item-mail-1">
				<li>
				<label class="typecho-label" for="mail-0-2">
				
				电子邮箱地址*</label>
				<input id="mail-0-2" name="mail" type="text" class="text" value="<?php echo set_value('mail',(isset($mail))?$mail:''); ?>"/>
				<p class="description">
				电子邮箱地址将作为此用户的主要联系方式.<br />
				        请不要与系统中现有的电子邮箱地址重复.</p>
				<?php echo form_error('mail', '<p class="message error">', '</p>'); ?>
				</li>
				</ul>
				<ul class="typecho-option" id="typecho-option-item-screenName-2">
				<li>
				<label class="typecho-label" for="screenName-0-3">
				用户昵称</label>
				<input id="screenName-0-3" name="screenName" type="text" class="text" value="<?php echo set_value('screenName',(isset($screenName))?$screenName:''); ?>"/>
				
				<p class="description">
				用户昵称可以与用户名不同, 用于前台显示.<br />
				        如果你将此项留空,将默认使用用户名.</p>
				<?php echo form_error('screenName', '<p class="message error">', '</p>'); ?>
				</li>
				</ul>
				<ul class="typecho-option" id="typecho-option-item-password-3">
				<li>
				<label class="typecho-label" for="password-0-4">
				用户密码*</label>
				<input id="password-0-4" name="password" type="password" class="password" value="<?php echo set_value('password',(isset($password))?$password:''); ?>"/>
				<p class="description">
				为此用户分配一个密码.<br />
				
				        建议使用特殊字符与字母的混编样式,以增加系统安全性.</p>
				<?php echo form_error('password', '<p class="message error">', '</p>'); ?>
				</li>
				</ul>
				<ul class="typecho-option" id="typecho-option-item-confirm-4">
				<li>
				<label class="typecho-label" for="confirm-0-5">
				用户密码确认*</label>
				<input id="confirm-0-5" name="confirm" type="password" class="password" />
				<p class="description">
				请确认你的密码, 与上面输入的密码保持一致.</p>
				<?php echo form_error('confirm', '<p class="message error">', '</p>'); ?>
				</li>
				</ul>
				<ul class="typecho-option" id="typecho-option-item-url-5">
				
				<li>
				<label class="typecho-label" for="url-0-6">
				个人主页地址</label>
				<input id="url-0-6" name="url" type="text" class="text" value="<?php echo set_value('url',(isset($url))?$url:''); ?>"/>
				</li>
				</ul>
				<ul class="typecho-option">
				<li>
				<label class="typecho-label" for="group">
				用户组</label>
				
				<select name="group" id="group">
				<option value="contributor"<?php echo set_select('group', 'contributor', ('contributor' != $group)?FALSE:TRUE); ?>>
				贡献者</option>
				<option value="editor"<?php echo set_select('group', 'editor', ('editor' != $group)?FALSE:TRUE); ?>>
				编辑</option>
				<option value="administrator"<?php echo set_select('group', 'administrator', ('administrator' != $group)?FALSE:TRUE); ?>>
				管理员</option>
				</select>
				
				<p class="description">
				不同的用户组拥有不同的权限.<br />
				        具体的权限分配表请<a href="#">参考这里</a>.</p>
				</li>
				</ul>
				<ul class="typecho-option typecho-option-submit">
				<li>
				<button type="submit">
				<?php echo (isset($uid) && is_numeric($uid))?'编辑用户':'添加用户';?>
				</button>
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