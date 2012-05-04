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
            <div class="column-24 start-01 typecho-list">
                <ul class="typecho-option-tabs">
                    <li<?php if('publish' == $status): ?> class="current"<?php endif; ?>><?php echo anchor('admin/posts/manage'.(isset($author_id) ? '?author='.$this->input->get('author',TRUE) : ''),'已发布');?></li>
                    <li<?php if('draft' == $status): ?> class="current"<?php endif; ?>><a href="<?php echo site_url('admin/posts/manage/draft'. (isset($author_id) ? '?author='.$this->input->get('author',TRUE):'')); ?>">草稿
					<?php if('on' !== $this->session->userdata('__all_posts') && !isset($author_id) && ($my_draft_num = $this->stats->count_posts('post', 'draft', $this->user->uid)) > 0): ?> 
                        <span class="balloon"><?php echo $my_draft_num; ?></span>
                    <?php elseif('on' == $this->session->userdata('__all_posts')  && !isset($author_id) && ($all_draft_num = $this->stats->count_posts('post', 'draft', NULL)) > 0): ?>
                        <span class="balloon"><?php echo $all_draft_num; ?></span>
                    <?php elseif('on' !== $this->session->userdata('__all_posts') && isset($author_id) && ($author_draft_num = $this->stats->count_posts('post', 'draft', $author_id)) > 0): ?>
                        <span class="balloon"><?php echo $author_draft_num; ?></span>
                    <?php endif; ?>
					</a>
                    </li>
                    <li<?php if('waiting' == $status): ?> class="current"<?php endif; ?>><a href="<?php echo site_url('admin/posts/manage/waiting'. (isset($author_id) ? '?author='.$this->input->get('author',TRUE):'')); ?>">待审核
                    <?php if('on' !== $this->session->userdata('__all_posts') && !isset($author_id) && ($my_waiting_num = $this->stats->count_posts('post', 'waiting', $this->user->uid)) > 0): ?> 
                        <span class="balloon"><?php echo $my_waiting_num; ?></span>
                    <?php elseif('on' == $this->session->userdata('__all_posts') && !isset($author_id) && ($all_waiting_num = $this->stats->count_posts('post', 'waiting', NULL)) > 0): ?>
                        <span class="balloon"><?php echo $all_waiting_num; ?></span>
                    <?php elseif('on' !== $this->session->userdata('__all_posts') && isset($author_id) && ($author_waiting_num = $this->stats->count_posts('post', 'waiting', $author_id)) > 0): ?>
                        <span class="balloon"><?php echo $author_waiting_num; ?></span>
                    <?php endif; ?>
                    </a></li>
                    <?php if($this->auth->exceed('editor', true) && !isset($author_id)): ?>
                        <li class="right<?php if('on' == $this->session->userdata('__all_posts')): ?> current<?php endif; ?>"><?php echo anchor("admin/posts/manage/$status?__all_posts=on",'所有');?></li>
                        <li class="right<?php if('on' !== $this->session->userdata('__all_posts')): ?> current<?php endif; ?>"><?php echo anchor("admin/posts/manage/$status?__all_posts=off",'我的');?></li>
                    <?php endif; ?>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作: 
                        <span class="operate-button typecho-table-select-all">全选</span>, 
                        <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                        选中项: 
                        <span rel="delete" lang="你确认要删除这些文章吗" class="operate-button operate-delete typecho-table-select-submit">删除</span>
                        <?php if($this->auth->exceed('editor', true) && 'waiting' == $status): ?>
                        <span rel="approved" class="operate-button typecho-table-select-submit">通过审核</span>
                        <?php endif;?>
                    </p>
                    <p class="search">
                    <input type="text" value="请输入关键字" onclick="value='';name='keywords';" />
                    <select name="category">
                    	<option value="0">所有分类</option>
                    	<?php if($categories):?>
							<?php foreach($categories->result() as $category):?>
								<option value="<?php echo $category->mid;?>"><?php echo $category->name;?></option>
							<?php endforeach;?>
						<?php endif;?>
                    </select>
                    <button type="submit">筛选</button>
                    <?php if(isset($author_id)): ?>
                        <input type="hidden" value="<?php echo $this->input->get('author',TRUE); ?>" name="author" />
                    <?php endif; ?>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_posts" class="operate-form" action="<?php echo site_url('admin/posts/operate')?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="320"/>
                        <col width="30"/>
                        <col width="110"/>
                        <col width="205"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th>标题</th>
                            <th> </th>
                            <th>作者</th>
                            <th>分类</th>
                            <th class="typecho-radius-topright">发布日期</th>
                        </tr>
                    </thead>
                    <tbody>

						<?php if($posts->num_rows() > 0):?>
						<?php foreach($posts->result() as $post):?>
                        <tr<?php echo ($post->pid % 2==0)?'':' class="even"'; ?> id="<?php echo 'post-'.$post->pid; ?>">
                            <td><input type="checkbox" value="<?php echo $post->pid; ?>" name="pid[]"/></td>
                            <td><a href="<?php echo site_url('admin/comments/manage?pid='.$post->pid);?>" class="balloon-button right size-<?php echo Common::split_by_count($post->commentsNum, 1, 10, 20, 50, 100); ?>"><?php echo $post->commentsNum;?></a></td>
                            <td><?php echo anchor(site_url('admin/posts/write/'.$post->pid),$post->title);?></td>
                            <td>
                            <?php if ('publish' == $post->status): ?>
                            <a class="right hidden-by-mouse" href="<?php echo site_url('posts/'.$post->slug); ?>"><img src="<?php echo base_url();?>application/views/admin/images/view.gif" title="<?php echo '浏览 '. $post->title; ?>" width="16" height="16" alt="view" /></a>
                            <?php endif; ?>
                            </td>
                            <td><?php echo anchor("admin/posts/manage/$status?author=".$post->authorId,$post->screenName); ?></td>
                            <td>
                            	<?php 
                            	$length = count($post->categories);
                            	foreach($post->categories as $key => $val):?>
								<?php
									echo '<a href="'; 
									echo site_url("admin/posts/manage/$status?category=".$val['mid']
												.(isset($author) ? '&author='.$this->input->get('author',TRUE):''));
									echo '">' . $val['name'] . '</a>' . ($key < $length - 1 ? ', ' : ''); ?>
								<?php endforeach;?>
							</td>
                            <td><?php echo Common::dateWord($post->created, now());?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><h6 class="typecho-list-table-title">没有任何文章</h6></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
                </form>
            
				<?php echo isset($pagination)?$pagination:''; ?>
                   
                
            

            
            </div>
        </div>
    </div>
</div>
<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
$this->load->view('admin/footer');
?>