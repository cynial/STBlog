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
                <div class="typecho-list-operate">
                <form method="get">

                    <p class="operate">
                    <?php echo anchor(site_url('admin/users/user'),'新增用户',array('class'=>'button'));?>
                    操作: 
                    <span class="operate-button typecho-table-select-all">全选</span>, 
                    <span class="operate-button typecho-table-select-none">不选</span>,&nbsp;&nbsp;&nbsp;
                    选中项: 
                    <span rel="delete" lang="你确认要删除这些用户吗?" class="operate-button operate-delete typecho-table-select-submit">删除</span>
                    </p>

             
                </form>
                </div>
            
                <form method="post" name="manage_users" class="operate-form" action="<?php echo site_url('admin/users/remove');?>">
                <table class="typecho-list-table">

                    <colgroup>
                        <col width="25"/>
                        <col width="150"/>
                        <col width="150"/>
                        <col width="30"/>
                        <col width="300"/>
                        <col width="165"/>
                        <col width="70"/>
                    </colgroup>

                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th>用户名</th>
                            <th>昵称</th>
                            <th> </th>
                            <th>电子邮件</th>

                            <th>用户组</th>
                            <th class="typecho-radius-topright">文章</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php foreach($users->result() as $user):?>
                    	<tr<?php echo ($user->uid % 2==0)?'':' class="even"'; ?> id="<?php echo 'user-'.$user->uid; ?>">
                            <td><input type="checkbox" value="<?php echo $user->uid;?>" name="uid[]"/></td>
                            <td>
                            	<?php echo anchor('admin/users/user/'.$user->uid,$user->name);?>
                            </td>

                            <td><?php echo $user->screenName;?></td>
                            <td>
                            <a class="right hidden-by-mouse" href="<?php echo site_url('author/'.$user->uid); ?>"><img src="<?php echo base_url();?>application/views/admin/images/view.gif" title="<?php echo '浏览 '. $user->screenName; ?>" width="16" height="16" alt="view" /></a>
                            </td>
                            <td><?php if($user->mail): ?><a href="mailto:<?php echo $user->mail;?>"><?php echo $user->mail;?></a><?php endif;?></td>
                            <td><?php switch ($user->group) {
                                case 'administrator':
                                    echo '管理员';
                                    break;
                                case 'editor':
                                    echo '编辑';
                                    break;
                                case 'contributor':
                                    echo '贡献者';
                                    break;
                                default:
                                    break;
                            } ?></td>
                            <td><a href="<?php echo site_url('admin/posts/manage?uid='.$user->uid); ?>" class="balloon-button left size-<?php echo Common::split_by_count($user->posts_num, 1, 10, 20, 50, 100); ?>"><?php echo $user->posts_num; ?></a></td>

                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <input type="hidden" name="do" value="delete" />
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