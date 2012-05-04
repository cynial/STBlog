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
            <div class="column-16 suffix typecho-content-panel">
                <h4>
                <?php echo '<img src="'.Common::gravatar($this->user->mail,'X',40).'" width="40" height="40" />'; ?>
                <?php echo $this->user->name; ?><cite>(<?php echo $this->user->screenName; ?>)</cite>
                </h4>
                <p><?php printf('目前有 <em>%d</em> 篇 Blog,并有 <em>%d</em> 条关于你的评论在已设定的 <em>%d</em> 个分类中', $this->stats->count_posts('post', 'publish', $this->user->uid),$this->stats->count_cmts_by_owner('comment', 'approved', $this->user->uid), $this->stats->count_categories());?></p>
                    
                    <p>最后登录: 
					<?php 
						if($this->user->logged >0)
						{
							echo Common::dateWord($this->user->logged, now());
						}
					?></p>
                
                <h3 id="change-password">设置密码</h3>
                 <?php $this->load->view('admin/profile_password_form');?>
            </div>
            <div class="column-08 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                <?php $this->load->view('admin/profile_basic_form');?>
            </div>
        </div>
    </div>
</div>
<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
$this->load->view('admin/footer');
?>