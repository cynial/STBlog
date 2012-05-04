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
                    <li<?php if('publish' == $status): ?> class="current"<?php endif; ?>><?php echo anchor('admin/pages/manage','已发布');?></li>
                    <li<?php if('draft' == $status): ?> class="current"<?php endif; ?>><a href="<?php echo site_url("admin/pages/manage/draft"); ?>">草稿
                    <?php if(($pages_draft_num = $this->stats->count_posts('page', 'draft', NULL)) > 0): ?> 
                        <span class="balloon"><?php echo $pages_draft_num; ?></span>
                    <?php endif; ?>
					</a>
                    </li>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作: 
                        <span class="operate-button typecho-table-select-all">全选</span>, 
                        <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                        选中项: 
                        <span rel="delete" lang="你确认要删除这些页面吗" class="operate-button operate-delete typecho-table-select-submit">删除</span>
                    </p>
                    <p class="search">
                    <input type="text" value="请输入关键字" onclick="value='';name='keywords';" />
                    <button type="submit">筛选</button>
					<?php if(isset($status)): ?>
                    	<input type="hidden" value="<?php echo $status; ?>" name="status" />
                    <?php endif; ?>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_pages" class="operate-form" action="<?php echo site_url('admin/pages/remove')?>">
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="355"/>
                        <col width="30"/>
                        <col width="180"/>
                        <col width="120"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                         <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th>标题</th>

                            <th> </th>
                            <th>缩略名</th>
                            <th>作者</th>
                            <th class="typecho-radius-topright">发布日期</th>
                        </tr>
                    </thead>
                    <tbody>

						<?php if($posts->num_rows() > 0):?>
						<?php foreach($posts->result() as $post):?>
                        <tr<?php echo ($post->pid % 2==0)?'':' class="even"'; ?> id="<?php echo 'page-'.$post->pid; ?>">
                            <td><input type="checkbox" value="<?php echo $post->pid; ?>" name="pid[]"/></td>
                            <td><a href="<?php echo site_url('admin/comments/manage/'.$post->pid);?>" class="balloon-button right size-<?php echo Common::split_by_count($post->commentsNum, 1, 10, 20, 50, 100); ?>"><?php echo $post->commentsNum;?></a></td>
                            <td><?php echo anchor(site_url('admin/pages/write/'.$post->pid),$post->title);?></td>
                            <td>
                            <?php if ('publish' == $post->status): ?>
                            <a class="right hidden-by-mouse" href="<?php echo site_url('pages/'.$post->slug); ?>"><img src="<?php echo base_url();?>application/views/admin/images/view.gif" title="<?php echo '浏览 '. $post->title; ?>" width="16" height="16" alt="view" /></a>
                            <?php endif; ?>
                            </td>
                            <td><?php echo $post->slug;?></td>
                            <td><?php echo $post->screenName; ?></td>
                            <td><?php echo Common::dateWord($post->created, now());?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="7"><h6 class="typecho-list-table-title">没有任何页面</h6></td>
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