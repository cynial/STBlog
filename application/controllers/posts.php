<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * STBlog Blogging System
 *
 * 基于Codeigniter的单用户多权限开源博客系统
 * 
 * STBlog is an open source multi-privilege blogging System built on the 
 * well-known PHP framework Codeigniter.
 *
 * @package		STBLOG
 * @author		Saturn <huyanggang@gmail.com>
 * @copyright	Copyright (c) 2009 - 2010, cnsaturn.com.
 * @license		GNU General Public License 2.0
 * @link		http://code.google.com/p/stblog/
 * @version		0.1.0
 */
 
// ------------------------------------------------------------------------

/**
 * STBlog Posts控制器
 *
 *	主要用于控制日志显示的功能表现
 *
 * @package		STBLOG
 * @subpackage	Controllers
 * @category	Front-controllers
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Posts extends ST_Controller {
	 
	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();	
	}

	 /**
     * 默认执行函数, 处理流程
     * 
     * @access public
     * @return void
     */
	function index($slug = '')
	{
		/** 如为空，则跳转 */
		if(empty($slug))
		{
			redirect(site_url());
		}
		
		/** 获取日志内容 */
		$post = $this->posts_mdl->get_post_by_id('slug', $slug);
		
		/** 不存在？ */
		if(!$post)
		{
			show_404();
		}
		
		/** 内容显示格式化 */
		$post = $this->_prepare_post($post);
		
		/** 是否存在评论? */
		$comments = $this->comments_mdl->get_cmts($post->pid, '', 'approved', 0, 0, 'ASC');
		
		/** 评论显示格式化 */
		if($comments->num_rows() >0)
		{
			$comments = $this->_prepare_comments($post, $comments);
		}
		
		/** 页面初始化 */
		$data['page_title'] = $post->title;
		$data['page_description'] = Common::subStr(strip_tags($post->content), 0, 100, '...');
		$data['page_keywords'] = Common::format_metas($post->tags, ',', FALSE);
		$data['parsed_feed'] = Common::render_feed_meta('post', $post->slug, $post->title);
		$data['post'] = $post;
		$data['comments'] = $comments;
		
		$this->load_theme_view('post', $data);

	}
	
	 /**
     * 内容格式化
     * 
     * @access private
     * @param  stdClass $post
     * @return stdClass
     */
	private function _prepare_post($post)
	{
		/** 日志发表日期 */
		$post->published = setting_item('post_date_format') 
								? date(setting_item('post_date_format'), $post->created) 
								: date('Y-m-d', $post->created);
		
		$post->modified = setting_item('post_date_format') 
								? date(setting_item('post_date_format'), $post->modified) 
								: date('Y-m-d', $post->modified);
		
		$this->metas_mdl->get_metas($post->pid);
		/** 日志分类 */
		$post->categories = $this->metas_mdl->metas['category'];
		/** 日志标签 */
		$post->tags = $this->metas_mdl->metas['tag'];
	
		$post->content = Common::get_content($post->text);
		
		$post->comment_allowed = (0 == $post->allowComment || Common::auto_closed($post->created, now())) ? FALSE : TRUE;
		
		$post->ping_allowed = (1 == $post->allowPing) ? TRUE : FALSE;
		
		$post->comment_post_url = site_url('comment/' . $post->pid); 
		
		unset($post->text);
		
		return $post;
	}

	 /**
     * 评论格式化
     * 
     * @access private
     * @param  stdClass $post
     * @param  array $comments
     * @return array
     */
	private function _prepare_comments($post, $comments)
	{
		foreach($comments->result() as $comment)
		{
			$comment->published = setting_item('comments_date_format') 
								? date(setting_item('comments_date_format'), $comment->created) 
								: date('Y-m-d', $comment->created);
								
			$comment->permalink = site_url('posts/'. $post->slug . '#comment-' . $comment->cid);
			
			$comment->author_link = $comment->author;
			
			if(!empty($comment->url))
			{
				$nofollow = array(); 
				
				if('1' == setting_item('comments_url_no_follow'))
				{
					$nofollow = array('rel' => 'external nofollow');
				}
				
				$comment->author_link = anchor($comment->url, $comment->author, $nofollow);
			}
			
			if('trackback' == $comment->type)
			{
				$text = unserialize($comment->text);
				
				$comment->author_link = '来自' . $comment->author_link .'在文章'. anchor($comment->url, $text['title']) .'中的引用';
				$comment->text = $text['excerpt'];
			}

			$comment->content = Common::cut_paragraph($comment->text);	
			
			unset($comment->text);
		}
		
		return $comments;
	}
}

/* End of file posts.php */
/* Location: ./application/controllers/posts.php */