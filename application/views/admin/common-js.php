<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/mootools-1.2.2-core-yc.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/mootools-1.2.2.2-more.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>application/views/admin/javascript/typecho-ui.source.js"></script>
<script type="text/javascript">
    (function () {
        window.addEvent('domready', function() {
            var handle = new Typecho.guid('typecho:guid', {offset: 1, type: 'mouse'});
            
            //增加淡出效果
            Typecho.message('.popup');
            Typecho.scroll('.typecho-option .error', '.typecho-option');
            Typecho.autoDisableSubmit();
            Typecho.Table.init('.typecho-list-table');
            Typecho.Table.init('.typecho-list-notable');
        });
    })();
</script>