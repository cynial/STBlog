<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php $this->load->view('admin/header');?>

<div class="body body-950">
    <div class="container">
    	<?php $this->load->view('admin/notify'); ?>
        <div class="column-07 start-09 typecho-login">
			<?php echo form_open('admin/login?ref='.urlencode($this->referrer), array('name'=>'login'));?>
                <fieldset>
                	<?php if(!empty($_POST)):?>
					<div class="message notice typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                    <ul>
                        <?php echo validation_errors(); ?>                    
                        <?php echo (empty($this->form_validation->error_string))?'':'<li>'.$this->form_validation->error_string.'</li>'; ?>
                    </ul>
					
                    </div>
					<?php endif;?>
                     <p><label for="name">用户名:</label> <input type="text" id="name" name="name" class="text" /></p>
					 <p><label for="password">密码:</label> <input type="password" id="password" name="password" class="text" /></p>
                     <p class="submit">
                     <!-- remember me will be released in next version -->
                     <!-- 
                     <label for="remember"><input type="checkbox" name="remember" class="checkbox" value="1" id="remember" /> 记住我</label>
                      -->
					<button type="submit">登录</button>
                    </p>
                </fieldset>
            <?php echo form_close();?>
            
            <div class="more-link">
                <p class="back-to-site">
                <a href="<?php echo site_url();?>" class="important">&laquo; 返回博客首页</a>
                </p>
                
				
				
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
(function () {
    var _form = document.login.name;
    _form.focus();
})();
</script>
<?php echo $this->load->view('admin/footer');?>