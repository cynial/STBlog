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
                <ul class="typecho-option-tabs">
                    <li<?php if('approved' == $status): ?> class="current"<?php endif; ?>>
                    <?php echo anchor('admin/comments/manage/approved'.($this->input->get('pid')? '?pid=' . $this->input->get('pid',TRUE) : ''), '已通过');?>
                   	</li>
                    <li<?php if('waiting' == $status): ?> class="current"<?php endif; ?>>
                    <a href="<?php echo site_url('admin/comments/manage/waiting'.($this->input->get('pid')? '?pid=' . $this->input->get('pid',TRUE) : ''));?>">待审核
                    <?php if('on' != $this->session->userdata('__all_comments') && !$this->input->get('pid') && ($my_waiting_cmts_num = $this->stats->count_cmts_by_owner('comment', 'waiting', $this->user->uid)) > 0): ?> 
                        <span class="balloon"><?php echo $my_waiting_cmts_num; ?></span>
                    <?php elseif('on' == $this->session->userdata('__all_comments') && !$this->input->get('pid') && ($waiting_cmts_num = $this->stats->count_cmts_by_owner('comment', 'waiting', NULL)) > 0): ?>
                        <span class="balloon"><?php echo $waiting_cmts_num; ?></span>
                    <?php elseif($this->input->get('pid') && ($current_waiting_cmts_num = $this->stats->count_cmts($this->input->get('pid',TRUE), 'comment', 'waiting')) > 0): ?>
                        <span class="balloon"><?php echo $current_waiting_cmts_num; ?></span>
                    <?php endif; ?>
                    </a></li>
                    <li<?php if('spam' == $status): ?> class="current"<?php endif; ?>><a href="<?php echo site_url('admin/comments/manage/spam'.($this->input->get('pid')? '?pid=' . $this->input->get('pid',TRUE) : ''));?>">垃圾
                    <?php if('on' != $this->session->userdata('__all_comments') && !$this->input->get('pid') && ($my_spam_cmts_num = $this->stats->count_cmts_by_owner('comment', 'spam', $this->user->uid)) > 0): ?> 
                        <span class="balloon"><?php echo $my_spam_cmts_num; ?></span>
                    <?php elseif('on' == $this->session->userdata('__all_comments') && !$this->input->get('pid') && ($spam_cmts_num = $this->stats->count_cmts_by_owner('comment', 'spam', NULL)) > 0): ?>
                        <span class="balloon"><?php echo $spam_cmts_num; ?></span>
                    <?php elseif($this->input->get('pid') && ($current_spam_cmts_num = $this->stats->count_cmts($this->input->get('pid',TRUE), 'comment', 'spam')) > 0): ?>
                        <span class="balloon"><?php echo $current_spam_cmts_num; ?></span>
                    <?php endif; ?>
                    </a></li>
                    <?php if($this->auth->exceed('editor', true) && !$this->input->get('pid',TRUE)): ?>
                        <li class="right<?php if('on' == $this->session->userdata('__all_comments')): ?> current<?php endif; ?>"><?php echo anchor('admin/comments/manage/'. $status .'?__all_comments=on','所有');?></li>
                        <li class="right<?php if('on' != $this->session->userdata('__all_comments')): ?> current<?php endif; ?>"><?php echo anchor('admin/comments/manage/'. $status .'?__all_comments=off','我的');?></li>
                    <?php endif; ?>
                </ul>
                <div class="typecho-list-operate">
                <form method="get">
                    <p class="operate">操作: 
                    <span class="operate-button typecho-table-select-all">全选</span>, 
                    <span class="operate-button typecho-table-select-none">不选</span>&nbsp;&nbsp;&nbsp;
                    选中项:
                    <span rel="approved" class="operate-button typecho-table-select-submit">通过</span>, 
                    <span rel="waiting" class="operate-button typecho-table-select-submit">待审核</span>, 
                    <span rel="spam" class="operate-button typecho-table-select-submit">标记垃圾</span>, 
                    <span rel="delete" lang="你确认要删除这些评论吗?" class="operate-button operate-delete typecho-table-select-submit">删除</span><?php if('spam' == $status): ?>, 
                    <span rel="delete-spam" lang="你确认要删除所有垃圾评论吗?" class="operate-button operate-delete typecho-table-select-submit">删除所有垃圾评论</span>
                    <?php endif; ?>
                    </p>
                    <p class="search">
                    <input type="text" value="请输入关键字" onclick="value='';name='keywords';" />
                    <?php if(isset($status)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($status); ?>" name="status" />
                    <?php endif; ?>
                    <?php if(isset($request->cid)): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($this->input->get('pid')); ?>" name="pid" />
                    <?php endif; ?>
                    <button type="submit">筛选</button>
                    </p>
                </form>
                </div>
            
                <form method="post" name="manage_comments" class="operate-form" action="<?php echo site_url('admin/comments/operate'); ?>">
                    <ul class="typecho-list-notable clearfix">
                    <?php if($comments->num_rows > 0): ?>
                    <?php foreach($comments->result() as $comment): ?>
                    <li class="column-24<?php echo ($comment->cid % 2==0)?'':' even'; ?>" id="<?php echo 'comment-'.$comment->cid; ?>">
                        <div class="column-01 center">
                            <input type="checkbox" value="<?php echo $comment->cid; ?>" name="cid[]"/>
                        </div>
                        <div class="column-02 center avatar">
                            <img src="<?php echo Common::gravatar($comment->mail,'X','40'); ?>" height="40" width="40" alt="<?php echo $comment->author;?>" />
                        </div>
                        <div class="column-21">
                            <div class="content">
                                <div class="comment-meta">
                                    <span class="<?php echo $comment->type; ?>"></span>
                                    <?php 
                                    echo (!empty($comment->url))?anchor($comment->url,$comment->author):$comment->author;
                 					?>
                                    <?php if($comment->mail): ?>
                                     | 
                                    <a href="mailto:<?php echo $comment->mail; ?>"><?php echo $comment->mail; ?></a>
                                    <?php endif; ?>
                                    <?php if($comment->ip): ?>
                                     | 
                                    <?php echo $comment->ip; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="comment-content">
                                    <?php echo Common::cut_paragraph($comment->text); ?>
                                </div>
                            </div>
                            
                            <div class="line">
                                <div class="left hidden-by-mouse">
                                    <?php if('approved' == $comment->status): ?>
                                    <span class="weak">通过</span>
                                    <?php else: ?>
                                    <a href="<?php echo site_url('admin/comments/operate?do=approved&cid='.$comment->cid); ?>" class="ajax">通过</a>
                                    <?php endif; ?>
                                     | 
                                    <?php if('waiting' == $comment->status): ?>
                                    <span class="weak">待审核</span>
                                    <?php else: ?>
                                    <a href="<?php echo site_url('admin/comments/operate?do=waiting&cid='.$comment->cid); ?>" class="ajax">待审核</a>
                                    <?php endif; ?>
                                     | 
                                    <?php if('spam' == $comment->status): ?>
                                    <span class="weak">垃圾</span>
                                    <?php else: ?>
                                    <a href="<?php echo site_url('admin/comments/operate?do=spam&cid='.$comment->cid); ?>" class="ajax">垃圾</a>
                                    <?php endif; ?>
                                     | 
                                    <a href="#<?php echo 'comment-'.$comment->cid; ?>" rel="<?php echo site_url('admin/comments/operate?do=get&cid='.$comment->cid); ?>" class="ajax operate-edit">编辑</a>
                                     | 
                                    <a lang="<?php printf('你确认要删除%s的评论吗?', htmlspecialchars($comment->author)); ?>" href="<?php echo site_url('admin/comments/operate?do=delete&cid='.$comment->cid); ?>" class="ajax operate-delete">删除</a>
                                </div>
                                <div class="right">
                                    <?php echo Common::dateWord($comment->created, time()); ?>
                                    &nbsp;&nbsp;
                                    <?php echo anchor('posts/'.$comment->post->slug.'#comment-'.$comment->cid,$comment->post->title);?>
                              
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <li class="even">
                        <h6 class="typecho-list-table-title">没有评论</h6>
                    </li>
                    <?php endif; ?>
                    </ul>
                    <input type="hidden" name="do" value="delete" />
                    <?php if($this->input->get('pid')): ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($this->input->get('pid',TRUE)); ?>" name="pid" />
                    <?php endif; ?>
                </form>
                             
                    <?php echo isset($pagination)?$pagination:''; ?>

            
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
        
            $(document).getElements('.typecho-list-notable li .operate-edit').addEvent('click', function () {
                
                var form = this.getParent('li').getElement('.comment-form');
                var request;
                
                if (form) {
                    
                    if (request) {
                        request.cancle();
                    }
                    
                    form.destroy();
                    this.getParent('li').getElement('.content').setStyle('display', 'inline');
                    this.clicked = false;
                    
                } else {
                    if ('undefined' == typeof(this.clicked) || !this.clicked) {
                        this.clicked = true;
                        this.getParent('.line').addClass('loading');
                        
                        request = new Request.JSON({
                            url: this.getProperty('rel'),
                            
                            onComplete: (function () {
                                this.clicked = false;
                            }).bind(this),
                            
                            onSuccess: (function (json) {
                            
                                if (json.success) {
                                    var cid = this.getParent('li').getElement('input[type=checkbox]').get('value');
                                    
                                    var form = new Element('div', {
                                        'class': 'comment-form',
                                    
                                        'html': '<label for="author-' + cid + '">名称</label>' +
                                        '<input type="text" class="text" name="author" id="author-' + cid + '" />' +
                                        '<label for="mail">电子邮件</label>' +
                                        '<input type="text" class="text" name="mail" id="mail-' + cid + '" />' +
                                        '<label for="url">个人主页</label>' +
                                        '<input type="text" class="text" name="url" id="url-' + cid + '" />' +
                                        '<textarea name="text" id="text-' + cid + '"></textarea>' +
                                        '<p><button id="submit-' + cid + '">保存评论</button>' +
                                        '<input type="hidden" name="cid" id="cid-' + cid + '" /></p>'
                                    
                                    });
                                    
                                    form.getElement('input[name=author]').set('value', json.comment.author);
                                    form.getElement('input[name=mail]').set('value', json.comment.mail);
                                    form.getElement('input[name=url]').set('value', json.comment.url);
                                    form.getElement('input[name=cid]').set('value', cid);
                                    form.getElement('textarea[name=text]').set('value', json.comment.text);
                                    
                                    var commentHeight = this.getParent('li').getElement('.comment-content').getSize().y;
                                    
                                    form.getElement('textarea[name=text]').setStyle('height', commentHeight > 150 ? commentHeight : 150);
                                    
                                    this.getParent('li').getElement('.content').setStyle('display', 'none');
                                    form.inject(this.getParent('li').getElement('.line'), 'before');
                                    form.getElement('#submit-' + cid).addEvent('click', (function () {
                                        var query = this.getParent('li').getElement('.comment-form').toQueryString();
                                        
                                        var sRequest = new Request.JSON({
                                            url: this.getProperty('rel').replace('do=get', 'do=edit'),
                                            
                                            onComplete: (function () {
                                                var li = this.getParent('li');
                                            
                                                li.getElement('.content').setStyle('display', 'inline');
                                                li.getElement('.comment-form').destroy();
                                                var myFx = new Fx.Tween(li);
                                                
                                                var bg = li.getStyle('background-color');
                                                if (!bg || 'transparent' == bg) {
                                                    bg = '#F7FBE9';
                                                }
                                                
                                                myFx.addEvent('complete', (function () {
                                                    this.setStyle('background-color', '');
                                                }).bind(li));
                                                
                                                myFx.start('background-color', '#AACB36', bg);
                                            }).bind(this),
                                            
                                            onSuccess: (function (json) {
                                                if (json.success) {
                                                    
                                                    var commentMeta = '';
                                                    commentMeta += '<span class="' + json.comment.type + '"></span> ';
                                                    
                                                    if (json.comment.url) {
                                                        commentMeta += '<a target="_blank" href="' + json.comment.url + '">' + json.comment.author + '</a> | ';
                                                    } else {
                                                        commentMeta += json.comment.author + ' | ';
                                                    }
                                                    
                                                    if (json.comment.mail) {
                                                        commentMeta += '<a href="mailto:' + json.comment.mail + '">' + json.comment.mail + '</a> | ';
                                                    }
                                                    
                                                    commentMeta += json.comment.ip;
                                                    
                                                    this.getParent('li').getElement('.comment-meta').set('html', commentMeta);
                                                    this.getParent('li').getElement('.comment-content').set('html', json.comment.text);
                                                }
                                            }).bind(this)
                                        }).send(query + '&do=edit');
                                        
                                        //alert(query);
                                        return false;
                                        
                                    }).bind(this));
                                    
                                    this.getParent('.line').removeClass('loading');
                                } else {
                                    alert(json.comment);
                                }
                                
                            }).bind(this)
                        }).send();
                    }
                    
                }
                
                return false;
            });
        
        });
    })();
</script>
<?php
$this->load->view('admin/footer');
?>