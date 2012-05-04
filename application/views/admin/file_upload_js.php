<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/swfupload/swfupload.queue.js"></script>
<script type="text/javascript">
    var deleteAttachment = function (pid, el) {
    
        var _title = $(el).getParent('li').getElement('strong');
        
        if (!confirm("你确认删除附件 %s 吗".replace("%s", _title.get('text').trim()))) {
            return;
        }

        _title.addClass('delete');
        
        new Request.JSON({
            method : 'post',
            url : '<?php echo site_url('admin/medias/operate');?>',//remove attachment
            onComplete : function (result) {
                if (200 == result.code) {
                    $(el).getParent('li').destroy();
                } else {
                    _title.removeClass('delete');
                    alert('删除失败');
                }
            }
        }).send('do=delete&from=ajax&pid=' + pid);
    };

    (function () {

        window.addEvent('domready', function() {
            var _inited = false;
            
            //加强未加载暗示
            var uploadButton = $(document).getElement('#upload-panel .button')
            .setStyle('cursor', 'pointer')
            .addEvent('click', function () {
                alert('正在加载上传组件, 请稍候再试');
            });
            
            //begin parent tabshow
            $(document).getElement('#upload-panel').addEvent('tabShow', function () {
            
                if (_inited) {
                    return;
                }
                _inited = true;
                
                var swfuploadLoaded = function () {
                    uploadButton.removeEvent('click');
                };
            
                var fileDialogComplete = function (numFilesSelected, numFilesQueued) {
                    try {
                        this.startUpload();
                    } catch (ex)  {
                        this.debug(ex);
                    }
                };
            
                var uploadStart = function (file) {
                    var _el = new Element('li', {
                        'class' : 'upload-progress-item clearfix',
                        'id'    : file.id,
                        'text'  : file.name
                    });
                    
                    _el.inject($(document).getElement('ul.upload-progress'), 'top');
                };
                
                var uploadSuccess = function (file, serverData) {
                    var _el = $(document).getElement('#' + file.id);
                    var _result = JSON.decode(serverData);
                    
                    _el.set('html', '<strong>' + file.name + 
                    '<input type="hidden" name="attachment[]" value="' + _result.pid + '" /></strong>' + 
                    '<small><span class="insert">插入</span>' +
                    ' , <span class="delete">删除</span></small>');
                    _el.set('tween', {duration: 1500});
                    
                    _el.setStyles({
                        'background-image' : 'none',
                        'background-color' : '#D3DBB3'
                    });
                    
                    _el.tween('background-color', '#D3DBB3', '#FFFFFF');
                    
                    var _insertBtn = _el.getElement('.insert');
                    if (_result.isImage) {
                        _insertBtn.addEvent('click', function () {
                            insertImageToEditor(_result.title, _result.url, _result.permalink);
                        });
                    } else {
                        _insertBtn.addEvent('click', function () {
                            insertLinkToEditor(_result.title, _result.url, _result.permalink);
                        });
                    }
                    
                    var _deleteBtn = _el.getElement('.delete');
                    _deleteBtn.addEvent('click', function () {
                        deleteAttachment(_result.pid, this);
                    });
                };
                
                var uploadComplete = function (file) {
                    //console.dir(file);
                };
                
                var uploadError = function (file, errorCode, message) {
                    var _el = $(document).getElement('#' + file.id);
                    var _fx = new Fx.Tween(_el, {duration: 3000});
                    
                    _el.set('html', '<strong>' + file.name + ' 上传失败</strong>');
                    _el.setStyles({
                        'background-image' : 'none',
                        'color'            : '#FFFFFF',
                        'background-color' : '#CC0000'
                    });
                    
                    _fx.addEvent('complete', function () {
                        _el.destroy();
                    });
                    
                    _fx.start('background-color', '#CC0000', '#F7FBE9');
                };
                
                var uploadProgress = function (file, bytesLoaded, bytesTotal) {
                    var _el = $(document).getElement('#' + file.id);
                    var percent = Math.ceil((1 - (bytesLoaded / bytesTotal)) * _el.getSize().x);
                    _el.setStyle('background-position', '-' + percent + 'px 0');
                };
            
                var swfu, _size = $(document).getElement('.typecho-list-operate a.button').getCoordinates(),
                settings = {
                    flash_url : "<?php echo base_url();?>application/views/admin/javascript/swfupload/swfupload.swf",
                    upload_url: "<?php echo site_url('admin/upload');?>",
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
                    file_queue_limit : 0,
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
                    button_height: 25,
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
            //end parent tabshow
        });
    })();
</script>
<script type="text/javascript">    
    /** 这两个函数在插件中必须实现 */
    var insertImageToEditor = function (title, url, link) {
        Typecho.textareaAdd('#text', '<img src="' + url + '" alt="' + title + '" />', '');
        new Fx.Scroll(window).toElement($(document).getElement('textarea#text'));
    };
    
    var insertLinkToEditor = function (title, url, link) {
        Typecho.textareaAdd('#text', '<a href="' + url + '" title="' + title + '">' + title + '</a>', '');
        new Fx.Scroll(window).toElement($(document).getElement('textarea#text'));
    };
    
    /** 挂载以上两个函数在编辑器插件中的实现 */
    <?php $this->plugin->trigger(ST_CORE_HOOK_EDITOR_INSERT_ATTACH);?>
    
</script>
