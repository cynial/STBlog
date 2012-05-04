<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="sidebar"><!-- sidebar -->     
    <!-- about -->
    <h5>关于博客</h5>
 <p>本博客为Saturn的个人技术博客，主要关注IT和Web技术，这里也是Stblog的官方博客。欢迎大家和我深入交流。<br /></p>
    
    <!-- categories -->
    <h5>日志分类</h5>
 <ul>
     <?php $this->plugin->trigger('Widget::Categories', '<li><a href="{permalink}" title="{description}">{title} [{count}]</a></li>');?>
 </ul>
    
    <h5>最新日志</h5>
 <ul class="post_list">
        <?php $this->plugin->trigger('Widget::Posts::Recent', '<li><a href="{permalink}" title="{title}">{title}</a></li>');?>
 </ul>
    
    <h5>最新评论</h5>
 <ul class="recent_comments">
     <?php $this->plugin->trigger('Widget::Comments::Recent', '<li><a href="{permalink}" title="{parent_post_desc}">{title}: </a><p>{content}</p></li>', 50, '...');?>
 </ul>
 
 <h5>日志归档</h5>
 <ul>
     <?php $this->plugin->trigger('Widget::Posts::Archive', '<li><a href="{permalink}">{title} [{count}]</a></li>', 'month', 'Y年m月');?>
 </ul>

    <h5>友情链接</h5>
 <ul>
  <li><a href="http://www.cnsaturn.com/" target="_blank">Saturn's Weblog</a></li>
 </ul>           
 
 <h5>订阅本站</h5>
 <p><?php echo anchor('feed', '日志RSS');?><br /><?php echo anchor('feed/comments', '评论RSS');?></p>
 
 <h5>其他</h5>
 <p><?php echo anchor('admin', '登陆');?>
 <br /><?php echo anchor('http://code.google.com/p/stblog/', '下载Stblog');?>
 <br /><?php echo anchor('http://www.codeigniter.org.cn/', 'CI中国社区');?>
 </p>

</div><!-- //sidebar -->