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
            <div class="column-24">
                <ul class="typecho-option-tabs">
                    <li><?php echo anchor('admin/themes/manage','可以使用的外观');?></li>
                    <li class="current"><a href="<?php echo site_url('admin/themes/editor'); ?>">
                    <?php if (setting_item('current_theme') == $theme): ?>
                    编辑当前外观
                    <?php else: ?>
                    <?php printf('编辑%s外观', ' <cite>' . $theme . '</cite> '); ?>
                    <?php endif; ?>
                    </a></li>
                </ul>
                
                <div class="typecho-edit-theme">
                    <div>
                        <ul>
                            <?php foreach($files as $file): ?>
                            <li<?php if($file == $current_file): ?> class="current"<?php endif; ?>>
                            <?php echo anchor('admin/themes/editor/'.$theme.'?file='.$file,$file);?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="content">
                        <form method="post" name="theme" id="theme" action="<?php echo site_url('admin/themes/edit'); ?>">
                            <textarea name="content" id="content" <?php if(!$content_is_writeable): ?>readonly<?php endif; ?>><?php echo $current_content; ?></textarea>
                            <div class="submit">
                                <?php if($content_is_writeable): ?>
                                <input type="hidden" name="theme" value="<?php echo $theme; ?>" />
                                <input type="hidden" name="file" value="<?php echo $current_file; ?>" />
                                <button type="submit">保存文件</button>
                                <?php else: ?>
                                    <h6 class="typecho-list-table-title">此文件无法写入</h6>
                                <?php endif; ?>
                            </div>
                        </form>
                        
                        </div>
                    </div>
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
