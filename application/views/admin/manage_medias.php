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
            <div class="column-24 start-01">
            
                <ul class="typecho-option-tabs">
                    <li<?php if(!$status || 'unattached' != $status): ?> class="current"<?php endif; ?>><?php echo anchor('admin/medias/manage','所有');?></li>
                    <li<?php if('unattached' == $status): ?> class="current"<?php endif; ?>><a href="<?php echo site_url('admin/medias/manage/unattached'); ?>">未归档
                    <?php if(!$this->auth->exceed('editor', TRUE) && ($my_unattached_attachment_num = $this->stats->count_posts('attachment', 'unattached', $this->user->uid)) > 0): ?> 
                        <span class="balloon"><?php echo $my_unattached_attachment_num; ?></span>
                    <?php elseif($this->auth->exceed('editor', TRUE) && ($unattached_attachment_num = $this->stats->count_posts('attachment', 'unattached', NULL)) > 0): ?>
                        <span class="balloon"><?php echo $unattached_attachment_num; ?></span>
                    <?php endif; ?>
                    </a></li>
                </ul>
                
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作: 
                        <span class="operate-button typecho-table-select-all">全选</span>, 
                        <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                        选中项: 
                        <span rel="delete" lang="你确认要删除这些文章吗" class="operate-button operate-delete typecho-table-select-submit">删除</span>
                    </p>
                    <p class="search">
                    <input type="text" value="请输入关键字" onclick="value='';name='keywords';" />
                    <button type="submit">筛选</button>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_medias" class="operate-form" action="<?php echo site_url('admin/medias/operate');?>">
                <table class="typecho-list-table draggable">
                    <colgroup>
                        <col width="25"/>
                        <col width="50"/>
                        <col width="20"/>
                        <col width="275"/>
                        <col width="30"/>
                        <col width="120"/>
                        <col width="220"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th> </th>
                            <th> </th>
                            <th>文件名</th>
                            <th> </th>
                            <th>上传者</th>
                            <th>所属文章</th>
                            <th class="typecho-radius-topright">发布日期</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php if($attachments->num_rows() > 0) : ?>
                        <?php foreach($attachments->result() as $attachment): ?>
                        <?php $info = unserialize($attachment->text);?>
                        <?php $mime = Common::mimeIconType($info['mime']); ?>
                        <tr<?php echo ($attachment->pid % 2==0)?'':' even'; ?> id="<?php echo 'attachment-'.$attachment->pid; ?>">
                            <td><input type="checkbox" value="<?php echo $attachment->pid; ?>" name="pid[]"/></td>
                            <td></td>
                            <td><span class="typecho-mime typecho-mime-<?php echo $mime; ?>"></span></td>
                            <td><?php echo anchor('admin/medias/detail/'.$attachment->pid, $attachment->title);?></td>
                            <td>
                            <?php if (!empty($attachment->order) && 'attached' == $attachment->status): ?>
                            <a class="right hidden-by-mouse" href="<?php echo site_url('posts/'.$attachment->parentPost->slug); ?>"><img src="<?php echo base_url();?>application/views/admin/images/view.gif" title="<?php echo '浏览 '. $attachment->parentPost->title; ?>" width="16" height="16" alt="view" /></a>
                            <?php endif; ?>                            
                            </td>
                            <td><?php echo $attachment->screenName; ?></td>
                            <td>
                            <?php if (!empty($attachment->order) && is_numeric($attachment->parentPost->pid)): ?>
                            <?php echo anchor('admin/'.$attachment->parentPost->type.'s/'.$attachment->parentPost->pid,$attachment->parentPost->title);?>
                            
                            <?php else: ?>
                            <span class="description">未归档</span>
                            <?php endif; ?>
                            </td>
                            <td><?php echo Common::dateWord($attachment->created,time());?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr class="even">
                        	<td colspan="8"><h6 class="typecho-list-table-title">没有任何附件</h6></td>
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