<form action="<?php echo site_url('admin/metas/manage/category'.((isset($mid) && is_numeric($mid))?'/'.$mid:''));?>" method="post" enctype="application/x-www-form-urlencoded">
<ul class="typecho-option" id="typecho-option-item-name-0">
<li>
<label class="typecho-label" for="name-0-1">
分类名称*</label>
<?php echo form_error('name', '<p class="message error">', '</p>'); ?>
<input id="name-0-1" name="name" type="text" class="text" value="<?php echo set_value('name',(isset($name))?htmlspecialchars_decode($name):''); ?>"/>
</li>
</ul>
<ul class="typecho-option" id="typecho-option-item-slug-1">
<li>
<label class="typecho-label" for="slug-0-2">
分类缩略名</label>
<?php echo form_error('slug', '<p class="message error">', '</p>'); ?>
<input id="slug-0-2" name="slug" type="text" class="text" value="<?php echo set_value('slug',(isset($slug))?htmlspecialchars_decode($slug):''); ?>"/>
<p class="description">
分类缩略名用于创建友好的链接形式,建议使用字母,数字,下划线和横杠.</p>
</li>
</ul>
<ul class="typecho-option" id="typecho-option-item-description-2">
<li>
<label class="typecho-label" for="description-0-3">

分类描述</label>
<?php echo form_error('description', '<p class="message error">', '</p>'); ?>
<textarea id="description-0-3" name="description"><?php echo set_value('description',(isset($description))?htmlspecialchars_decode($description):''); ?>
</textarea>
<p class="description">
此文字用于描述分类,在有的主题中它会被显示.</p>
</li>
</ul>
<input name="do" type="hidden" value="<?php echo (isset($mid) && is_numeric($mid))?'update':'insert';?>" />
<ul class="typecho-option typecho-option-submit" id="typecho-option-item--5">
<li>
<button type="submit">
<?php echo (isset($mid) && is_numeric($mid))?'更新分类':'添加分类';?>
</button>
</li>
</ul>
</form>
