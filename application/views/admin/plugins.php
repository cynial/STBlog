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
            <div class="column-24 typecho-list">
                <?php if ($activated_plugins): ?>
                <h6 class="typecho-list-table-title">激活的插件</h6>
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="10"/>
                        <col width="200"/>
                        <col width="360"/>
                        <col width="90"/>
                        <col width="105"/>
                        <col width="125"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th>名称</th>
                            <th>描述</th>
                            <th>版本</th>
                            <th>作者</th>
                            <th class="typecho-radius-topright">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($activated_plugins as $key => $plugin):?>
                        <tr<?php echo ($key % 2==0)?'':' class="even"'; ?> id="plugin-<?php echo $plugin['directory']; ?>">
                            <td></td>
                            <td><?php echo $plugin['name']; ?>
                            
                            </td>
                            <td><?php echo $plugin['description']; ?></td>
                            <td><?php echo $plugin['version']; ?></td>
                            <td><?php echo empty($plugin['plugin_uri']) ? $plugin['author'] : '<a href="' . $plugin['plugin_uri']
                            . '">' . $plugin['author'] . '</a>'; ?></td>
                            <td>
                                <?php if($plugin['configurable']):?>
                                	<?php echo anchor('admin/plugins/config/'.$plugin['directory'],'设置');?>
                                	|
                                <?php endif;?>
                                <?php echo anchor('admin/plugins/deactivate/'.$plugin['directory'],'禁用');?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
                
                <?php if ($deactivated_plugins): ?>
                <h6 class="typecho-list-table-title">禁用的插件</h6>
                <table class="typecho-list-table">
                    <colgroup>
                        <col width="10"/>
                        <col width="200"/>
                        <col width="360"/>
                        <col width="90"/>
                        <col width="105"/>
                        <col width="125"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="typecho-radius-topleft"> </th>
                            <th>名称</th>
                            <th>描述</th>
                            <th>版本</th>
                            <th>作者</th>
                            <th class="typecho-radius-topright">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($deactivated_plugins as $key => $plugin):?>
                        <tr<?php echo ($key % 2==0)?'':' class="even"'; ?> id="plugin-<?php echo $plugin['directory']; ?>">
                            <td></td>
                            <td><?php echo $plugin['name']; ?>
                            
                            </td>
                            <td><?php echo $plugin['description']; ?></td>
                            <td><?php echo $plugin['version']; ?></td>
                            <td><?php echo empty($plugin['plugin_uri']) ? $plugin['author'] : '<a href="' . $plugin['plugin_uri']
                            . '">' . $plugin['author'] . '</a>'; ?></td>
                            <td>
                                <?php echo anchor('admin/plugins/activate/'.$plugin['directory'],'激活');?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                
                
                
            </div>
        </div>
    </div>
</div>

<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
$this->load->view('admin/footer');
?>