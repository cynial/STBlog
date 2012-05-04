<form action="<?php echo site_url('admin/metas/manage/tag'.((isset($mid) && is_numeric($mid))?'/'.$mid:''));?>" method="post" enctype="application/x-www-form-urlencoded">
<ul class="typecho-option" id="typecho-option-item-name-0">
<li>
<label class="typecho-label" for="name-0-1">
标签名称*</label>
<?php echo form_error('name', '<p class="message error">', '</p>'); ?>
<input id="name-0-1" name="name" type="text" class="text"  value="<?php echo set_value('name',(isset($name))?htmlspecialchars_decode($name):''); ?>"/>
<p class="description">

这是标签在站点中显示的名称.可以使用中文,如"地球".</p>
</li>
</ul>
<ul class="typecho-option" id="typecho-option-item-slug-1">
<li>
<label class="typecho-label" for="slug-0-2">
标签缩略名</label>
<?php echo form_error('name', '<p class="message error">', '</p>'); ?>
<input id="slug-0-2" name="slug" type="text" class="text" value="<?php echo set_value('slug',(isset($slug))?htmlspecialchars_decode($slug):''); ?>"/>
<p class="description">
标签缩略名用于创建友好的链接形式,如果留空则默认使用标签名称.</p>
</li>
</ul>
<input name="do" type="hidden" value="<?php echo (isset($mid) && is_numeric($mid))?'update':'insert';?>" />
<ul class="typecho-option typecho-option-submit" id="typecho-option-item--4">
<li>
<button type="submit">
<?php echo (isset($mid) && is_numeric($mid))?'更新标签':'添加标签';?></button>
</li>
</ul>
</form>
