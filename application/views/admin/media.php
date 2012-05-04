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
                <div class="typecho-attachment-photo-box">
                	<?php $info = unserialize($attachment->text);?>
                    <?php if ($info['isImage']): ?>
                    <img src="<?php echo base_url().$info['path']; ?>" alt="<?php echo $info['name']; ?>" />
                    <?php endif; ?>
                    
                    <div class="description">
                        <ul>
                            <?php $mime = Common::mimeIconType($info['mime']); ?>
                            <li><span class="typecho-mime typecho-mime-<?php echo $mime; ?>"></span><strong><?php echo $info['name']; ?></strong> <small><?php echo ceil($info['size']); ?> Kb</small></li>
                            <li><input id="attachment-url" type="text" readonly class="text" value="<?php echo base_url().$info['path']; ?>" />
                            <?php if($this->auth->exceed('editor',TRUE) || $this->user->uid == $attachment->authorId):?>
                            <button id="exchange" disabled>替换</button>
                            <?php endif;?>
                            <span id="swfu"><span id="swfu-placeholder"></span></span></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
$this->load->view('admin/common-js');
$this->load->view('admin/copyright');
?>
<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/swfupload/swfupload.queue.js"></script>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            
            $(document).getElement('.typecho-attachment-photo-box .description input').addEvent('click', function () {
                this.select();
            });
            
            var swfuploadLoaded = function () {
                var btn = $(document)
                .getElement('.typecho-attachment-photo-box button#exchange');
                
                var obj = $(document)
                .getElement('.typecho-attachment-photo-box .description ul li #swfu');
                
                offset = obj.getCoordinates(btn);
                obj.setStyles({
                    'width': btn.getSize().x,
                    'height': btn.getSize().y,
                    'left': 0 - offset.left,
                    'top': 0 - offset.top
                });
                
                btn.removeAttribute('disabled');
            };
        
            var fileDialogComplete = function (numFilesSelected, numFilesQueued) {
                try {
                    this.startUpload();
                } catch (ex)  {
                    this.debug(ex);
                }
            };
        
            var uploadStart = function (file) {
                $(document)
                .getElement('.typecho-attachment-photo-box button#exchange')
                .set('html', '上传中')
                .setAttribute('disabled', '');
            };
            
            var uploadSuccess = function (file, serverData) {
                var _el = $(document).getElement('#attachment-url');
                var _result = JSON.decode(serverData);
                
                _el.set('tween', {duration: 1500});
                
                _el.setStyles({
                    'background-position' : '-1000px 0',
                    'background-color' : '#D3DBB3'
                });
                
                <?php if ($info['isImage']): ?>
                var _img = new Image(), _date = new Date();
                
                _img.src = _result.url + (_result.url.indexOf('?') > 0 ? '&' : '?') + '__rds=' + _date.toUTCString();
                _img.alt = _result.title;
                
                $(document).getElement('.typecho-attachment-photo-box img').destroy();
                $(_img).inject($(document).getElement('.typecho-attachment-photo-box'), 'top');
                <?php endif; ?>
                
                $(document).getElement('.typecho-attachment-photo-box .description small')
                .set('html', Math.ceil(_result.size) + ' Kb');
                
                _el.tween('background-color', '#D3DBB3', '#EEEEEE');
            };
            
            var uploadComplete = function (file) {
                $(document)
                .getElement('.typecho-attachment-photo-box button#exchange')
                .set('html', '替换')
                .removeAttribute('disabled');
            };
            
            var uploadError = function (file, errorCode, message) {
                var _el = $(document).getElement('#attachment-url');
                var _fx = new Fx.Tween(_el, {duration: 3000});
                
                _fx.start('background-color', '#CC0000', '#EEEEEE');
            };
            
            var uploadProgress = function (file, bytesLoaded, bytesTotal) {
                var _el = $(document).getElement('#attachment-url');
                var percent = Math.ceil((1 - (bytesLoaded / bytesTotal)) * _el.getSize().x);
                _el.setStyle('background-position', '-' + percent + 'px 0');
            };
            
            var swfu, _size = $(document).getElement('.typecho-attachment-photo-box button#exchange').getCoordinates(),
            settings = {
                flash_url : "<?php echo base_url();?>application/views/admin/javascript/swfupload/swfupload.swf",
                    upload_url: "<?php echo site_url('admin/upload/modify/'.$attachment->pid);?>",
                    post_params: {"__uid" : "<?php echo $this->user->uid;?>", 
                    "__token" : "<?php echo $this->user->token;?>"},
                    file_size_limit : "<?php $val = trim(ini_get('upload_max_filesize'));
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        echo $val;
                    ?> byte",
                file_types : "<?php echo setting_item('upload_exts');?>",
                file_types_description : "所有文件",
                file_upload_limit : 0,
                file_queue_limit : 1,
                debug: false,
                
                //Handle Settings
                file_dialog_complete_handler : fileDialogComplete,
                upload_start_handler : uploadStart,
                upload_progress_handler : uploadProgress,
                upload_success_handler : uploadSuccess,
                queue_complete_handler : uploadComplete,
                upload_error_handler : uploadError,
                swfupload_loaded_handler : swfuploadLoaded,
                
                // Button Settings
                button_placeholder_id : "swfu-placeholder",
                button_height: _size.height,
                button_text: '',
                button_text_style: '',
                button_text_left_padding: 14,
                button_text_top_padding: 0,
                button_width: _size.width,
                button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
                button_cursor: SWFUpload.CURSOR.HAND
            };

            swfu = new SWFUpload(settings);
        
        });
    })();
</script>
<?php
$this->load->view('admin/footer');
?>