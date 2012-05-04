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
 * STBlog Feed控制器
 *
 *	主要用于控制Feed的功能表现
 *
 * @package		STBLOG
 * @subpackage	Controllers
 * @category	Front-controllers
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Feed extends ST_Controller {

	/**
     * 是否输出全文
     *
     * @access private
     * @var int
     */
	private $_feed_full_text = 1;
	
	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('feedwriter', RSS2);
		
		$this->_feed_full_text = intval(setting_item('feed_full_text'));
	}

	 /**
     * 请求分发
     * 
     * @access public
     * @param  string $key 类别
     * @param  string $value slug
     * @return void
     */
	public function index($key = '', $value = '')
	{	
		switch ($key)
		{
			case 'category':
				$this->generate_category_feed($value);
				break;
			case 'tag':
				$this->generate_tag_feed($value);
				break;
			case 'post':
				$this->generate_post_feed($value);
				break;
			case 'comments':
				$this->generate_comments_feed();
				break;
			default:
				$this->generate_feed();
				break;
		}
	}

	 /**
     * 生成所有日志feed
     * 
     * @access public
     * @return void
     */
	public function generate_feed()
	{
		$posts = $this->posts_mdl->get_posts('post', 'publish', NULL, 10, 0, 0, '', TRUE);
		
		/** init */
		$this->feedwriter->setTitle(setting_item('blog_title'));
		$this->feedwriter->setLink(site_url());
		$this->feedwriter->setDescription(setting_item('blog_description'));
		$this->feedwriter->setChannelElement('language', 'zh-CN');
  		$this->feedwriter->setChannelElement('pubDate', date(DATE_RSS, now()));
  		
  		$this->_generate($posts);
	}

	 /**
     * 生成所有评论feed
     * 
     * @access public
     * @return void
     */
	public function generate_comments_feed()
	{
		$comments = $this->comments_mdl->get_cmts(0, 'comment', 'approved', 100, 0, 'DESC', '');
		
		/** init */
		$this->feedwriter->setTitle(setting_item('blog_title') .'所有评论');
		$this->feedwriter->setLink(site_url());
		$this->feedwriter->setDescription(setting_item('blog_description'));
		$this->feedwriter->setChannelElement('language', 'zh-CN');
  		$this->feedwriter->setChannelElement('pubDate', date(DATE_RSS, now()));
  		
  		if($comments->num_rows() > 0)
  		{
  			foreach($comments->result() as $comment)
  			{
  				$parent_post = $this->posts_mdl->get_post_by_id('pid', $comment->pid);
  				
  				if(!$parent_post)
  				{
  					continue;
  				}
					
				$title = $comment->author . '评论：' . $parent_post->title;
				$permalink = site_url('posts/'. $parent_post->slug .'#comment-'. $comment->cid);
  					  				
  				$newItem = $this->feedwriter->createNewItem();
  				
  				$newItem->setTitle($title);
			    $newItem->setLink($permalink);
			  	$newItem->setDate($comment->created);
			  	$newItem->setDescription($comment->text);
			  	$newItem->addElement('author', $comment->author);
			  	$newItem->addElement('guid', $permalink,array('isPermaLink'=>'true'));
			  	
			  	$this->feedwriter->addItem($newItem);	
  			}
  		}
  		
  		$this->feedwriter->genarateFeed();
	}

	 /**
     * 生成指定日志feed
     * 
     * @access public
     * @param  string $slug
     * @return void
     */
	public function generate_post_feed($slug)
	{
		$post = $this->posts_mdl->get_post_by_id('slug', $slug);
		
		if(empty($post))
		{
			show_error('发生错误：内容不存在或已被删除');
			exit();
		}
		
		/** init */
		$this->feedwriter->setTitle(setting_item('blog_title') .' － '. $post->title .' 的评论');
		$this->feedwriter->setLink(site_url('posts/' . $post->slug));
		$this->feedwriter->setDescription(Common::subStr(strip_tags(Common::get_content($post->text)), 0, 200, '...'));
		$this->feedwriter->setChannelElement('language', 'zh-CN');
  		$this->feedwriter->setChannelElement('pubDate', date(DATE_RSS, now()));
  		
  		$comments = $this->comments_mdl->get_cmts($post->pid, 'comment', 'approved', 0, 0, 'DESC', '');
  		
  		if($comments->num_rows() > 0)
  		{
  			foreach($comments->result() as $comment)
  			{
				$title = $comment->author . ' 评';
				$permalink = site_url('posts/'. $post->slug .'#comment-'. $comment->cid);
  					  				
  				$newItem = $this->feedwriter->createNewItem();
  				
  				$newItem->setTitle($title);
			    $newItem->setLink($permalink);
			  	$newItem->setDate($comment->created);
			  	$newItem->setDescription($comment->text);
			  	$newItem->addElement('author', $comment->author);
			  	$newItem->addElement('guid', $permalink,array('isPermaLink'=>'true'));
			  	
			  	$this->feedwriter->addItem($newItem);	
  			}
  		}
  		
  		$this->feedwriter->genarateFeed();
	}

	 /**
     * 生成分类中的日志feed
     * 
     * @access public
     * @param  string $slug
     * @return void
     */
	public function generate_category_feed($slug)
	{
		$category = $this->metas_mdl->get_meta_by_slug($slug);
		
		if(empty($category))
		{
			show_error('发生错误：分类不存在或已被删除');
			exit();
		}

		/** init */
		$this->feedwriter->setTitle(setting_item('blog_title') . ' - 分类：' .$category->name);
		$this->feedwriter->setLink(site_url('category/'. $category->slug));
		$this->feedwriter->setDescription($category->description);
		$this->feedwriter->setChannelElement('language', 'zh-CN');
  		$this->feedwriter->setChannelElement('pubDate', date(DATE_RSS, now()));
  		
  		$posts = $this->posts_mdl->get_posts_by_meta($slug, 'category', 'post', 'publish', 'posts.*', 10, 0, TRUE);
		
		$this->_generate($posts);
	}

	 /**
     * 生成tag中的日志feed
     * 
     * @access public
     * @param  string $slug
     * @return void
     */
	public function generate_tag_feed($slug)
	{
		$tag = $this->metas_mdl->get_meta_by_slug($slug);
		
		if(empty($tag))
		{
			show_error('发生错误：标签不存在或已被删除');
			exit();
		}
		
		/** init */
		$this->feedwriter->setTitle(setting_item('blog_title') . ' - 标签：' .$tag->name);
		$this->feedwriter->setLink(site_url('tag/'. $tag->slug));
		$this->feedwriter->setDescription(setting_item('blog_description'));
		$this->feedwriter->setChannelElement('language', 'zh-CN');
  		$this->feedwriter->setChannelElement('pubDate', date(DATE_RSS, now()));
  		
  		$posts = $this->posts_mdl->get_posts_by_meta($slug, 'tag', 'post', 'publish', 'posts.*', 10, 0, TRUE);
		
		$this->_generate($posts);
	}

	 /**
     * 处理item节点，并生成xml文档
     * 
     * @access public
     * @param  string $slug
     * @return void
     */
	private function _generate($posts)
	{
		if($posts->num_rows() >0)
  		{
  			foreach($posts->result() as $post)
  			{
  				$permalink = site_url('posts/'. $post->slug);
  				$description = (1 == $this->_feed_full_text) ? Common::get_content($post->text) : Common::get_excerpt($post->text);
  				
  				$newItem = $this->feedwriter->createNewItem();
  				
  				$newItem->setTitle(htmlspecialchars($post->title));
			    $newItem->setLink($permalink);
			  	$newItem->setDate($post->created);
			  	$newItem->setDescription($description);
			  	$newItem->addElement('author', $post->screenName);
			  	$newItem->addElement('guid', $permalink,array('isPermaLink'=>'true'));
			  	
			  	$this->feedwriter->addItem($newItem);
  			}
  		}
  		
  		$this->feedwriter->genarateFeed();
	}

}

/* End of file feed.php */
/* Location: ./application/controllers/feed.php */