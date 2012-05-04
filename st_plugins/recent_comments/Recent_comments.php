<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: 最新评论Widget
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 显示博客最新评论
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Recent_comments
{
	private $_CI;

	public function __construct(&$plugin)
	{
		$plugin->register('Widget::Comments::Recent', $this, 'show_recent_comments');
		
		$this->_CI = &get_instance();
	}
	
	public function show_recent_comments($format, $length = 100, $trim = '...')
	{
		/** 处理输入数据 */
		if(empty($format)) return;
		$length = (!empty($length) && is_numeric($length))?intval($length):100;
		$trim = (!empty($trim))?$trim:'...';
		
		$limit = setting_item('comments_list_size');
		$limit = ($limit && is_numeric($limit))?intval($limit):10;
		
		$comments = $this->_CI->stcache->get('Widget::Comments::Recent');
		
		if(FALSE == $comments)
		{
			$comments = $this->_CI->db
							  ->select('comments.pid, comments.cid, comments.text, comments.author, posts.slug as post_slug, posts.title as post_title, posts.type as post_type')
				 			  ->from('comments')
				 			  ->join('posts','comments.pid = posts.pid','left')
        		 			  ->where('comments.type', 'comment')
        		 			  ->where('comments.status', 'approved')
        		 			  ->order_by('comments.created', 'DESC')
        		 			  ->limit($limit)
        		 			  ->offset(0)
        		 			  ->get()
        		 			  ->result();
        
        	$this->_CI->stcache->set('Widget::Comments::Recent', $comments);	
		}

		$sequence = 1;
		
		if($comments)
		{
			foreach($comments as $comment)
			{	
				$wildcards = array(
								'{permalink}', 
								'{parent_post_desc}', 
								'{title}', 
								'{content}'
								);
								
				$permalink = ('post' == $comment->post_type) 
								? site_url('posts/'. $comment->post_slug . '#comment-'. $comment->cid)
								: site_url('pages/'. $comment->post_slug . '#comment-'. $comment->cid);
				
				$replaces = array(
								$permalink, 
								'对[' . $comment->post_title . ']一文的评论:',
								$comment->author,
								Common::subStr(strip_tags($comment->text), 0, $length, $trim)
								);
				
				echo str_replace($wildcards, $replaces, $format) . "\r\n";
				
				$post = NULL;
				
				$sequence ++;
			}	
		
		}
		
	}
}

/* End of file Recent_comments.php */
/* Location: ./application/st_plugins/Recent_comments.php */