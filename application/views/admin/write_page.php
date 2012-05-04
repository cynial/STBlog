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
        <div class="container typecho-page-main typecho-post-option typecho-post-area">

            <form action="" method="post" name="write_post">
                <div class="column-18 suffix" id="test">
                    <div class="column-18">
                        <label for="title" class="typecho-label">标题</label>
						<?php echo form_error('title', '<p class="message error">', '</p>'); ?>
                        <p class="title"><input type="text" id="title" name="title" value="<?php echo set_value('title',(isset($title))?htmlspecialchars_decode($title):''); ?>" class="text title" /></p>
                        <label for="text" class="typecho-label">内容
                        <!-- 使用wysiwyg编辑器时必需实现此方法 -->
                        <?php $this->plugin->trigger(ST_CORE_HOOK_EDITOR_INSERT_MORE);?>
                        </label>
						<?php echo form_error('text', '<p class="message error">', '</p>'); ?>
                        <p><textarea style="height: 350px"  id="text" name="text"><?php echo set_value('text',(isset($text))?$text:''); ?></textarea></p>
                                             
                        <p class="submit">
                            <span class="left">
                                <span class="advance close">展开高级选项</span>
                                <span class="attach">展开附件</span>
                            </span>
                            <span class="right">
                                <input type="hidden" name="draft" value="0" />
                                <button type="button" id="btn-save">保存并继续编辑</button>
                                <button type="submit" id="btn-submit">发布页面 &raquo;</button>
                            </span>
                        </p>
                    </div>
                    <ul id="advance-panel" class="typecho-post-option column-18">
						<li class="column-18">
                            <div class="column-12">
                                <label for="order" class="typecho-label">页面顺序</label>
                                <?php echo form_error('order', '<p class="message error">', '</p>'); ?>
                                <p><input type="text" id="order" name="order" value="<?php echo set_value('order',(isset($order))?$order:''); ?>" class="mini" /></p>

                                <p class="description">为你的自定义页面设定一个序列值以后, 能够使得它们按此值从小到大排列</p>
                            </div>
                            <div class="column-06">
                                <label class="typecho-label">权限控制</label>
                                <ul>
                                    <li><input id="allowComment" name="allowComment" type="checkbox" value="1" <?php echo set_checkbox('allowComment', '1', (1 == $allow_comment)?TRUE:FALSE); ?> />
                                    <label for="allowComment">允许评论</label></li>
                                    <li><input id="allowPing" name="allowPing" type="checkbox" value="1" <?php echo set_checkbox('allowPing', '1',(1 == $allow_ping)?TRUE:FALSE); ?> />
                                    <label for="allowPing">允许被引用</label></li>
                                    <li><input id="allowFeed" name="allowFeed" type="checkbox" value="1" <?php echo set_checkbox('allowFeed', '1',(1 == $allow_feed)?TRUE:FALSE); ?> />
                                    <label for="allowFeed">允许在聚合中出现</label></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                    <ul id="upload-panel" class="column-18">
                        <li class="column-18">
                            <?php $this->load->view('admin/file_upload'); ?>
                        </li>
                    </ul>
                </div>
                <div class="column-06">
                    <ul class="typecho-post-option">
                        <li>
                            <label for="date" class="typecho-label">日期</label>
                            <p>
                                <select name="month" id="month">
								    <option value="1" <?php if (1 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>一月</option>
                                    <option value="2" <?php if (2 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>二月</option>
                                    <option value="3" <?php if (3 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>三月</option>
                                    <option value="4" <?php if (4 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>四月</option>
                                    <option value="5" <?php if (5 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>五月</option>
                                    <option value="6" <?php if (6 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>六月</option>
                                    <option value="7" <?php if (7 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>七月</option>
                                    <option value="8" <?php if (8 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>八月</option>
                                    <option value="9" <?php if (9 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>九月</option>
                                    <option value="10" <?php if (10 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>十月</option>
                                    <option value="11" <?php if (11 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>十一月</option>
                                    <option value="12" <?php if (12 == $this->form_validation->month): ?>selected="true"<?php endif; ?>>十二月</option>
                                </select>
                                <input size="4" maxlength="4" type="text" name="day" id="day" value="<?php echo $this->form_validation->day;?>" />
                                ,
                                <input size="4" maxlength="4" type="text" name="year" id="year" value="<?php echo $this->form_validation->year;?>" />
                                @
                                <input size="2" maxlength="2" type="text" name="hour" id="hour" value="<?php echo $this->form_validation->hour;?>" />
                                :
                                <input size="2" maxlength="2" type="text" name="min" id="min" value="<?php echo $this->form_validation->minute;?>" />
                            </p>
                            <p class="description">请选择一个发布日期</p>
                        </li>
                        <li>
                            <label for="slug" class="typecho-label">缩略名</label>
							<?php echo form_error('slug', '<p class="message error">', '</p>'); ?>
                            <p><input type="text" id="slug" name="slug" value="<?php echo set_value('slug',(isset($slug))?htmlspecialchars_decode($slug):''); ?>" class="mini" /></p>
                            <p class="description">为这篇日志自定义链接地址, 有利于搜索引擎收录</p>
                        </li>
                      </ul>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
$this->load->view('admin/copyright');
$this->load->view('admin/common-js');
?>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
        
            /** 绑定按钮 */
            $(document).getElement('span.advance').addEvent('click', function () {
                Typecho.toggle('#advance-panel', this,
                '收起高级选项', '展开高级选项');
            });
            
            $(document).getElement('span.attach').addEvent('click', function () {
                Typecho.toggle('#upload-panel', this,
                '收起附件', '展开附件');
            });
            
            $('btn-save').removeProperty('disabled');
            $('btn-submit').removeProperty('disabled');
            
            $('btn-save').addEvent('click', function (e) {
                this.getParent('span').addClass('loading');
                this.setProperty('disabled', true);
                $(document).getElement('input[name=draft]').set('value', 1);
                $(document).getElement('form[name=write_post]').submit();
            });
            
            $('btn-submit').addEvent('click', function (e) {
                this.getParent('span').addClass('loading');
                this.setProperty('disabled', true);
                $(document).getElement('input[name=draft]').set('value', 0);
            });
        });
    })();
</script>
<?php
/** 挂载一个编辑器插件 */
$this->plugin->trigger(ST_CORE_HOOK_EDITOR);

$this->load->view('admin/file_upload_js');
$this->load->view('admin/footer');
?>