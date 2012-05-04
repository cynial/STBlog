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
                    <li class="current"><?php echo anchor('admin/themes/manage','可以使用的外观');?></li>
                    <li><?php echo anchor('admin/themes/editor','编辑当前外观');?></li>
                </ul>
                
                <table class="typecho-list-table typecho-theme-list" cellspacing="0" cellpadding="0">
                    <colgroup>
                        <col width="450"/>
                        <col width="450"/>
                    </colgroup>
                    
                    <?php 
                    $length = count($themes);
                    foreach($themes as $key => $theme): ?>
                    <?php echo ($key % 2==0)?'<tr>':''; ?>
                    <?php
                    $borderBottom = ($length - $key+1 >= ($length % 2 ? 1 : 2));
                    ?>
                    <td id="theme-<?php echo $theme['name']; ?>" class="<?php if(setting_item('current_theme') == $theme['directory']): ?>current <?php endif; echo ($key % 2==0)?'border-right':'';if ($borderBottom): echo ' border-bottom'; endif; ?>">
                        <div class="column-04">
                            <img src="<?php echo $theme['screen']; ?>" width="120" height="90" align="left" />
                        </div>
                        <div class="column-08">
                        <h4><?php echo $theme['name']; ?></h4>
                        <cite>作者: <?php echo empty($theme['uri']) ? $theme['author'] : '<a href="' . $theme['uri']
                            . '">' . $theme['author'] . '</a>'; ?>
                        &nbsp;&nbsp;&nbsp;版本: <?php echo $theme['version']; ?>
                        </cite>
                        <p><?php echo nl2br($theme['description']); ?></p>
                        </div>
                        <?php if(setting_item('current_theme') != $theme['directory']): ?>
                            <a class="edit" href="<?php echo site_url('admin/themes/editor/'. $theme['directory']); ?>">编辑</a>
                            <a class="activate" href="<?php echo site_url('admin/themes/activate/' . $theme['directory']); ?>">激活</a>
                        <?php endif; ?>
                    
                    
                    </td>
                    <?php $last = $key+1; ?>
                    <?php echo ($key % 2==0)?'':'</tr>'; ?>
                    <?php endforeach; ?>
                    <?php if($last % 2): ?>
                    <td>&nbsp;</td></tr>
                    <?php endif; ?>
                    
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
?>

<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            $(document).getElements('table.typecho-list-table tr td').each(function (item, index) {
                var _a = item.getElement('a.activate'),
                _e = item.getElement('a.edit');
                
                if (_a && _e) {
                    item.addEvents({
                    
                        'mouseover': function () {
                            this.addClass('hover');
                            
                            if (0 == index % 2) {
                                _a.setStyles({
                                
                                    'right': _a.getParent('td').getNext('td').getSize().x + 1,
                                    
                                    'top': _a.getParent('td').getPosition(_a.getParent('.column-24')).y
                                
                                });
                                
                                _a.addClass('typecho-radius-bottomleft');
                                
                                _e.setStyles({
                                
                                    'right': _e.getParent('td').getNext('td').getSize().x + 1,
                                    
                                    'top': _e.getParent('td').getPosition(_e.getParent('.column-24')).y + _e.getParent('td').getSize().y - _e.getSize().y - 1
                                
                                });
                                
                                _e.addClass('typecho-radius-topleft');
                            } else {
                                _a.setStyles({
                                
                                    'left': _a.getParent('td').getPosition(_a.getParent('.column-24')).x,
                                    
                                    'top': _a.getParent('td').getPosition(_a.getParent('.column-24')).y
                                
                                });
                                
                                _a.addClass('typecho-radius-bottomright');
                                
                                _e.setStyles({
                                
                                    'left': _e.getParent('td').getPosition(_e.getParent('.column-24')).x,
                                    
                                    'top': _e.getParent('td').getPosition(_e.getParent('.column-24')).y + _e.getParent('td').getSize().y - _e.getSize().y - 1
                                
                                });
                                
                                _e.addClass('typecho-radius-topright');
                            }
                        },
                        
                        'mouseleave': function () {
                            this.removeClass('hover');
                        }
                    
                    });
                }
            });
        });
    })();
</script>

<?php
$this->load->view('admin/footer');
?>
