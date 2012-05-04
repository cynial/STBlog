<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div id="comments">
			<?php if($comments && ($comments->num_rows() > 0)):?>
				<h6><?php echo $comments->num_rows();?> 个评论 <a href="#comments">»</a></h6>
				<ol>
				<?php foreach($comments->result() as $comment):?>
					<li id="comment-<?php echo $comment->cid;?>"<?php echo ($comment->cid % 2) == 0?'':'class="alt"';?>>
						<cite class="comment-author">
							<img class="avatar" src="<?php echo Common::gravatar($comment->mail);?>" alt="<?php echo $comment->author;?>" height="64" width="64" />
								<?php echo $comment->author_link;?>
						</cite><br />
						<small class="comment-date"><a href="<?php echo $comment->permalink;?>"><?php echo $comment->published;?></a></small>
						<div class="comment-text">
							<?php echo $comment->content; ?>
						</div>
					</li>
				<?php endforeach;?>
				</ol>
			<?php endif;?>
			
            <?php if($post->comment_allowed):?>
			<h6><strong>回应此文</strong></h6>
			<form action="<?php echo $post->comment_post_url;?>" method="post" id="comment-form">
				<p><input name="author" id="author" value="" class="text" type="text"><label for="author">Name (required)</label></p>
				<p><input name="mail" id="mail" value="" class="text" type="text"><label for="mail">Email (will not be published, required)</label></p>
				<p><input name="url" id="url" value="" class="text" type="text"><label for="url">Website</label></p>
				<p><textarea cols="60" name="text" rows="10"></textarea></p>

				<p><input value="提交留言" class="button" type="submit"></p>
			</form>
			<?php else:?>
			<h6><strong>评论已关闭</strong></h6>
			<?php endif;?>
			
			<?php if($post->ping_allowed):?>
			<p>你也可以选择<?php echo anchor('trackback/'. $post->pid, '引用此文章', array('rel' => 'nofollow'));?>.</p>
			<?php endif;?>
</div><!-- end #comment -->