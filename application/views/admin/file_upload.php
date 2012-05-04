<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
.upload-progress {
    font-size: 12px;
}

#upload-panel ul li.upload-progress-item {
	background-image: url(<?php echo base_url();?>application/views/admin/images/progress.gif);
	background-repeat: repeat-y;
	background-position: -1000px 0;
    background-color: #fff;
    padding: 5px;
    margin-bottom: 5px;
    border: 1px solid #C1CD94;
    
	-moz-border-radius-topleft: 2px;
	-moz-border-radius-topright: 2px;
	-moz-border-radius-bottomleft: 2px;
	-moz-border-radius-bottomright: 2px;
	-webkit-border-top-left-radius: 2px;
	-webkit-border-top-right-radius: 2px;
	-webkit-border-bottom-left-radius: 2px;
	-webkit-border-bottom-right-radius: 2px;
	
	/* hope IE support border radius, God save me! */
	border-top-left-radius: 2px;
	border-top-right-radius: 2px;
	border-bottom-left-radius: 2px;
	border-bottom-right-radius: 2px;
}

.upload-progress-item strong {
    float: left;
}

.upload-progress-item strong.delete {
    text-decoration: line-through;
}

.upload-progress-item small {
    float: right;
    font-size: 8pt;
}

.upload-progress-item small .insert, .upload-progress-item small .delete {
    cursor: pointer;
    text-decoration: underline;
}

.upload-progress-item small .insert {
    color: #00AA00;
}

.upload-progress-item small .delete {
    color: #CC0000;
}
</style>

<div class="typecho-list-operate">
<p class="operate">
    <a class="button left">上传文件 <small style="font-weight:normal">(<?php echo ini_get('upload_max_filesize'); ?>)</small></a>
    <span id="swfu"><span id="swfu-placeholder"></span></span>
</p>
</div>

<ul class="upload-progress">
<?php if($attachments->num_rows() >0): ?>
	<?php foreach($attachments -> result() as $row): ?>
		<?php $attachment = unserialize($row->text);?>
    <li class="upload-progress-item clearfix">
        <strong>
            <?php echo $row->title; ?>
            <input type="hidden" name="attachment[]" value="<?php echo $row->pid; ?>" />
        </strong>
        <small>
            <span class="insert" onclick="<?php if ($attachment['isImage']){
                        printf("insertImageToEditor('%s', '%s', '%s');",$row->title,base_url().$attachment['path'],base_url().$attachment['path']);
                    } else {
                        printf("insertLinkToEditor('%s', '%s', '%s');",$row->title,base_url().$attachment['path'],base_url().$attachment['path']);
                    } ?>">插入</span>
            ,
            <span class="delete" onclick="deleteAttachment(<?php echo $row->pid; ?>, this);">删除</span>
        </small>
    </li>
    <?php endforeach;?>
<?php endif; ?>
</ul>
