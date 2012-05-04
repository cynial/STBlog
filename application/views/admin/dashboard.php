<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('admin/header');
$this->load->view('admin/menu');
?>
<div class="main">
    <div class="body body-950">
        <div class="container typecho-page-title">
			<div class="column-24 start-01">
				<h2><?php echo $page_title;?></h2>
				<p><?php echo anchor(site_url(),'查看我的站点');?></p>
			</div>
		</div>
        <div class="container typecho-page-main">
            <div class="column-06 typecho-dashboard-nav suffix">
                <h3 class="intro">欢迎使用 STBlog,您可以使用下面的链接开始您的 Blog 之旅:</h3>
                <div class="intro-link">
                    <ul>
                        <li><?php echo anchor(site_url('admin/profile'),'更新我的资料');?></li>
                        <?php if($this->auth->exceed('contributor', TRUE)): ?>
							<li><?php echo anchor(site_url('admin/posts/write'),'撰写一篇新文章');?></li>
							<li><?php echo anchor(site_url('admin/pages/write'),'创建一个新页面');?></li>
							<li><?php echo anchor(site_url('admin/comments/manage'),'查看我的留言');?></li>
							<!--################more authtications#####################-->
							<?php if($this->auth->exceed('editor', TRUE)): ?>
								
									<?php if($this->auth->exceed('administrator', true)): ?>
										 <li><?php echo anchor(site_url('admin/themes/manage'),'更换我的主题');?></li>
										<li><?php echo anchor(site_url('admin/settings/general'),'修改系统设置');?></li>
									<?php endif; ?>
							<?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            
                <h3>统计信息</h3>
                <div class="status">
                    <p><?php printf('目前有 <em>%d</em> 篇 Blog,并有 <em>%d</em> 条关于你的评论在已设定的 <em>%d</em> 个分类中',$this->stats->count_posts('post', 'publish', $this->user->uid),$this->stats->count_cmts_by_owner('comment', 'approved', $this->user->uid),$this->stats->count_categories());?></p>
                    
                    <p>最后登录: 
					<?php 
						if($this->user->logged >0) echo Common::dateWord($this->user->logged, now());
					?></p>
                </div>
            </div>

            <div class="column-12 typecho-dashboard-main">
                <div class="section">
                    <h4>最近发表的文章</h4>
	                <?php if($my_recent_posts->num_rows() >0):?>
						<ul>
							<?php foreach($my_recent_posts->result() as $post):?>
								<li><?php echo anchor(site_url('posts').'/'.$post->slug, $post->title, array('class'=>'title'));?>  发布于
								<?php echo $post->categories;?>
								- <span class="date"><?php echo Common::dateWord($post->created, now());?></span>
								</li> 
							<?php endforeach;?>
						</ul>
					<?php else:?>
						<ul><li>你还没有发布任何文章...</li></ul>
					<?php endif;?>
                </div>
                
            	<div class="section">
                    <h4>最新得到的回复</h4>
                    <?php if($my_recent_cmts->num_rows() > 0):?>
                    <ul>
                    <?php foreach($my_recent_cmts->result() as $comment):?>
                    	<li><?php echo anchor((!empty($comment->url))?$comment->url:'', $comment->author, array('rel'=>'external nofollow'));?> 发布于 <?php echo anchor('posts/'. $comment->parent_post->slug . '#comment-'. $comment->cid, $comment->parent_post->title, array('class'=>'title'));?> - <span class="date"><?php echo Common::dateWord($comment->created, now());?></span></li>
                    <?php endforeach;?>
                    
                    <?php else:?>
                    	<ul><li>你还没有任何评论...</li></ul>
                    <?php endif;?>
                </div>
            </div>

            <div class="column-06 typecho-dashboard-nav prefix">
               
                <h3>官方消息</h3>
                <div id="typecho-message" class="intro-link">
                    <ul>
                        <li><?php echo anchor('http://code.google.com/p/stblog/wiki/install', '程序的安装与配置', array('target'=> '_blank'));?></li>
                        <li><?php echo anchor('http://code.google.com/p/stblog/wiki/remove_index_dot_php', '如何去除我博客URL中的index.php', array('target'=> '_blank'));?></li>
                        <li><?php echo anchor('http://code.google.com/p/stblog/wiki/ckeditor', '如何开启CKEditor可视化编辑器', array('target'=> '_blank'));?></li>
                        <li><?php echo anchor('http://code.google.com/p/stblog/wiki/antispam', '如何防治垃圾留言和引用', array('target'=> '_blank'));?></li>
                        <li><?php echo anchor('http://code.google.com/p/stblog/wiki/plugins', '如何编写并发布一个插件', array('target'=> '_blank'));?></li>
                        <li><?php echo anchor('http://code.google.com/p/stblog/wiki/themes', '如何编写并发布一个主题/皮肤', array('target'=> '_blank'));?></li>

                    </ul>
                </div>
            </div>
            
        </div>
    </div>
</div>
<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
$this->load->view('admin/footer');
?>