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
        <div class="container typecho-page-main manage-metas">
                <div class="column-16 suffix">
                    <ul class="typecho-option-tabs">
                        
                        <li<?php if('category' == $type): ?> class="current"<?php endif; ?>><?php echo anchor(site_url('admin/metas/manage'),'分类');?></li>

                        <li<?php if('tag' == $type): ?> class="current"<?php endif; ?>><?php echo anchor(site_url('admin/metas/manage/tag'),'标签');?></li>
                    </ul>
                    
                   <?php if('category' == $type): ?> 
                   <form method="post" name="manage_categories" class="operate-form" action="<?php echo site_url('admin/metas/operate/'.$type);?>">
                    <div class="typecho-list-operate">
                        <p class="operate">操作: 
                            <span class="operate-button typecho-table-select-all">全选</span>, 
                            <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                            选中项: 
                            <span rel="delete" lang="此分类下的所有内容将被删除, 你确认要删除这些分类吗?" class="operate-button operate-delete typecho-table-select-submit">删除</span>, 
                            <span rel="refresh" lang="刷新分类可能需要等待较长时间, 你确认要刷新这些分类吗?" class="operate-button typecho-table-select-submit">刷新</span>, 
                            <span rel="merge" class="operate-button typecho-table-select-submit">合并到</span>

                            <select name="merge">
                                <?php foreach($category->result() as $cate):?>
                                	<option value="<?php echo $cate->mid;?>"><?php echo $cate->name;?></optin>
                                <?php endforeach;?>
                            </select>
                        </p>
                    </div>
                    
                    <table class="typecho-list-table draggable">
                        <colgroup>
                            <col width="25"/>

                            <col width="230"/>
                            <col width="30"/>
                            <col width="170"/>
                            <col width="50"/>
                            <col width="65"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="typecho-radius-topleft"> </th>

                                <th>名称</th>
                                <th> </th>
                                <th>缩略名</th>
                                <th> </th>
                                <th class="typecho-radius-topright">文章数</th>
                            </tr>

                        </thead>
                        <tbody>
                           <?php if($category->num_rows() > 0):?>
                           	<?php foreach($category->result() as $cate):?>
                           <tr<?php echo ($cate->mid % 2==0)?'':' class="even"'; ?> id="<?php echo 'category-'.$cate->mid; ?>">
                                <td><input type="checkbox" value="<?php echo $cate->mid; ?>" name="mid[]"/></td>
                                <td>
                                	<?php echo anchor('admin/metas/manage/category/'.$cate->mid,$cate->name);?>
                                </td>
                                <td>
                                	<a class="right hidden-by-mouse" href="<?php echo site_url('category/'.$cate->slug); ?>"><img src="<?php echo base_url();?>application/views/admin/images/view.gif" title="<?php echo '浏览 '. $cate->name; ?>" width="16" height="16" alt="view" /></a>
                                </td>

                                <td><?php echo $cate->slug;?></td>
                                <td>
                                                                
                                                                </td>
                                <td><a href="<?php echo site_url('admin/posts/manage?category='.$cate->mid);?>" class="balloon-button right size-<?php echo Common::split_by_count($cate->count, 1, 10, 20, 50, 100); ?>"><?php echo $cate->count;?></a></td>
                            </tr>
                            <?php endforeach;?>
                           <?php else:?>
                           	<tr class="even">
                                <td colspan="6"><h6 class="typecho-list-table-title">没有任何分类</h6></td>
                            </tr> 
                           <?php endif;?>
                        </tbody>

                    </table>
                    <input type="hidden" name="do" value="delete" />
                    </form>
                    <?php else:?>
                    <form method="post" name="manage_tags" class="operate-form" action="<?php echo site_url('admin/metas/operate/'.$type);?>">
                    <div class="typecho-list-operate">
                        <p class="operate">操作: 
                            <span class="operate-button typecho-table-select-all">全选</span>, 
                            <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                            选中项: 
                            <span rel="delete" lang="此标签下的所有内容将被删除, 你确认要删除这些标签吗?" class="operate-button operate-delete typecho-table-select-submit">删除</span>, 
                            <span rel="refresh" lang="刷新标签可能需要等待较长时间, 你确认要刷新这些分类吗?" class="operate-button typecho-table-select-submit">刷新</span>, 
                            <span rel="merge" class="operate-button typecho-table-select-submit">合并到</span> 
                            <input type="text" name="merge" />

                        </p>
                    </div>
                    
                    <ul class="typecho-list-notable tag-list clearfix typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                        <?php if($tag->num_rows()>0):?>
                        	<?php foreach($tag->result() as $t):?>
                        		<li class="size-<?php echo Common::split($t->count, 5, 10, 20, 30); ?>" id="tag-<?php echo $t->mid;?>">
                        			<input type="checkbox" value="<?php echo $t->mid;?>" name="mid[]"/>
                        			<span rel="<?php echo site_url('admin/metas/manage/tag/'.$t->mid);?>"><?php echo $t->name?></span>
                        		</li>
                        	<?php endforeach;?>
                        <?php else:?>
                        	<h6 class="typecho-list-table-title">没有任何标签</h6>
                        <?php endif;?>
                    </ul>

                    <input type="hidden" name="do" value="delete" />
                    </form>

                    <?php endif;?>              
                </div>
                <div class="column-08 typecho-mini-panel typecho-radius-topleft typecho-radius-topright typecho-radius-bottomleft typecho-radius-bottomright">
                <?php if('category' == $type):?>
                	<?php $this->load->view('admin/metas_cate_form');?>
                <?php endif;?>
                
                <?php if('tag' == $type):?>
                	<?php $this->load->view('admin/metas_tag_form');?>
                <?php endif;?>
                </div>
        </div>
    </div>
</div>


<?php
include 'copyright.php';
include 'common-js.php';
?>

<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var _selection;
            
            <?php if (isset($mid)): ?>
            var _hl = $(document).getElement('.typecho-mini-panel');
            if (_hl) {
                _hl.set('tween', {duration: 1500});
    
                var _bg = _hl.getStyle('background-color');
                if (!_bg || 'transparent' == _bg) {
                    _bg = '#F7FBE9';
                }

                _hl.tween('background-color', '#AACB36', _bg);
            }
            <?php endif; ?>
            <?php if ('tag' == $type): ?>
            Typecho.Table.checked = function (input, item) {
                if (!_selection) {
                    _selection = document.createElement('div');
                    $(_selection).addClass('tag-selection');
                    $(_selection).addClass('clearfix');
                    $(document).getElement('.typecho-mini-panel form')
                    .insertBefore(_selection, $(document).getElement('.typecho-mini-panel form #typecho-option-item-name-0'));
                }
                
                var _href = item.getElement('span').getProperty('rel');
                var _text = item.getElement('span').get('text');
                var _a = document.createElement('a');
                $(_a).addClass('button');
                $(_a).setProperty('href', _href);
                $(_a).set('text', _text);
                _selection.appendChild(_a);
                item.checkedElement = _a;
            };
            
            Typecho.Table.unchecked = function (input, item) {
                if (item.checkedElement) {
                    $(item.checkedElement).destroy();
                }
                
                if (!$(_selection).getElement('a')) {
                    _selection.destroy();
                    _selection = null;
                }
            };
            <?php endif;?>
        });
    })();
</script>
<?php $this->load->view('admin/footer'); ?>