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
 * STBlog 内容控制器
 *
 *	主要用于控制日志相关内容的功能表现
 *
 * @package		STBLOG
 * @subpackage	Controllers
 * @category	Front-controllers
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Home extends ST_Controller 
{
	/**
     * 当前uri
     *
     * @access private
     * @var string
     */
	 private $_uri = '';

	/**
     * 当前页码
     *
     * @access private
     * @var string
     */
	 private $_current_page = 1;
	
	/**
     * 每页条目数
     *
     * @access private
     * @var int
     */
	 private $_limit = 5;

	/**
     * 偏移
     *
     * @access private
     * @var int
     */
	 private $_offset = 0;

	/**
     * 条目总数
     *
     * @access private
     * @var int
     */
	 private $_total_count = 0;

	/**
     * 条目总数
     *
     * @access private
     * @var array
     */
	 private $_posts = array();

	/**
     * 分页字符串 wrapper
     *
     * @access private
     * @var string
     */
	 private $_pagination = '';
	 
	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		$this->_uri = $this->uri->segment(1) . '/';
	}

	 /**
     * 初始化分页参数
     * 
     * @access public
     * @param  int  $current_page
     * @return void
     */
	private function _init_pagination($current_page)
	{
		/** 当前页 */
		$this->_current_page = ($current_page && is_numeric($current_page)) ? intval($current_page) : 1;
		
		/** 每页多少项 */
		$page_size = setting_item('posts_page_size');
		$this->_limit = ($page_size && is_numeric($page_size)) ? intval($page_size) : 5;
		
		/** 偏移量 */
		$this->_offset = ($this->_current_page - 1) * $this->_limit;
		
		if($this->_offset < 0)
		{
			redirect(site_url());
		}
	}
	
	 /**
     * 加工和处理posts数据
     * 
     * @access private
     * @param  array  $posts 内容stdClass对象数组
     * @return void
     */
	private function _prepare_posts()
	{
		foreach($this->_posts as &$post)
		{
			/** 日志固定链接 */
			$post->permalink = site_url('posts/'. $post->slug); 
			
			/** 日志发表日期 */
			$post->published = setting_item('post_date_format') 
									? date(setting_item('post_date_format'), $post->created) 
									: date('Y-m-d', $post->created);
			
			$this->metas_mdl->get_metas($post->pid);
			
			/** 日志分类 */
			$post->categories = $this->metas_mdl->metas['category'];
			
			/** 日志标签 */
			$post->tags = $this->metas_mdl->metas['tag'];
			
			/** 日志摘要 */
			$post->excerpt = Common::get_excerpt($post->text);
			
			/** 是否存在摘要 */
			$post->more = (Common::has_break($post->text)) ? TRUE : FALSE;
			
			unset($post->slug);
			unset($post->text);
		}
	}

	 /**
     * 应用分页规则
     * 
     * @access private
     * @param  string  $target_uri 目标uri
     * @param  bool  $url_friendly 开启友好url
     * @param  string  $parament_name  页码参数 e.g ?p=1
     * @param  string  $page  页码
     * @return void
     */
	private function _apply_pagination($target_uri, $url_friendly = TRUE, $parament_name = 'p')
	{
		if($this->_total_count > $this->_limit)
		{
			$this->dpagination->currentPage($this->_current_page);
			$this->dpagination->items($this->_total_count);
			$this->dpagination->limit($this->_limit);
			$this->dpagination->adjacents(2);
			$this->dpagination->target($target_uri);
			$this->dpagination->nextLabel('');
			$this->dpagination->PrevLabel('');
						
			if($url_friendly)
			{
				$this->dpagination->urlFriendly();
			}
			else
			{
				$this->dpagination->parameterName($parament_name);
			}
			
			$this->_pagination = $this->dpagination->getOutput();
		}
	}

	 /**
     * 提取归档提示语
     * 
     * @access private
     * @param  int  $year 归档年（必需）
     * @param  int  $month 归档月（可选）
     * @param  int  $day  归档日（可选）
     * @return string
     */
	private function _archive_hints($year, $month, $day)
	{
		if($year > 0)
		{
			if($month > 0)
			{
				if($day > 0)
				{
					$month = sprintf("%02d", $month);
					$day = sprintf("%02d", $day);
					$this->_uri .= "$year/$month/$day";
					 
					return date('Y年m月d日', mktime(0, 0, 0, $month, $day, $year));
				}
				
				$month = sprintf("%02d", $month);
				$this->_uri .= "$year/$month";
				
				return date('Y年m月', mktime(0, 0, 0, $month, 1, $year));
			}
			
			$this->_uri .= $year;
			
			return date('Y年', mktime(0, 0, 0, 1, 1, $year)); 
		}
		
		return;
	}

	 /**
     * 默认日志分页显示
     * 
     * @access public
     * @return void
     */
	public function index($page = 1)
	{
		/** 分页参数 */
		$this->_init_pagination($page);
		
		$this->_posts = $this->posts_mdl->get_posts('post', 'publish', NULL, $this->_limit, $this->_offset)->result();
		$this->_total_count = $this->posts_mdl->get_posts('post', 'publish', NULL, 10000, 0)->num_rows();
		
		if(!empty($this->_posts))
		{
			$this->_prepare_posts();
			
			$this->_apply_pagination(site_url('page').'/%');
		}

		/** 页面初始化 */
		$data['page_title'] = '首页';
		$data['page_description'] = setting_item('blog_description');
		$data['page_keywords'] = setting_item('blog_keywords');
		$data['posts'] = $this->_posts;
		$data['parsed_feed'] = Common::render_feed_meta();
		$data['pagination'] = $this->_pagination;
		
		/** 加载主题下的页面 */
		$this->load_theme_view('index', $data);
	}

	 /**
     * 分类浏览
     * 
     * @access public
     * @param  string $slug
     * @param  int    $page
     * @return void
     */
	public function category($slug, $page = 1)
	{
		if(empty($slug) || !is_numeric($page))
		{
			redirect(site_url());
		}
		
		$category = $this->metas_mdl->get_meta_by_slug(trim($slug));
		if(!$category)
		{
			show_error('分类不存在或已被管理员删除');
			exit();
		}
				
		/** 分页参数 */
		$this->_init_pagination($page);
		
		$this->_posts = $this->posts_mdl->get_posts_by_meta($slug, 'category', 'post', 'publish', 'posts.*', $this->_limit, $this->_offset)->result();
		$this->_total_count = $this->posts_mdl->get_posts_by_meta($slug, 'category', 'post', 'publish', 'posts.*', 10000, 0)->num_rows();
		
		if(!empty($this->_posts))
		{
			$this->_prepare_posts();
			
			$this->_apply_pagination(site_url('category/' . $slug) . '/%');
		}

		/** 页面初始化 */
		$data['page_title'] = $category->name;
		$data['page_description'] = $category->description;
		$data['page_keywords'] = setting_item('blog_keywords');
		$data['posts'] = $this->_posts;
		$data['parsed_feed'] = Common::render_feed_meta('category', $category->slug, $category->name);
		$data['current_view_hints'] = sprintf('%s 分类下的文章（第 %d 页 / 共 %d 篇）', $category->name, $this->_current_page, $this->_total_count);
		$data['pagination'] = $this->_pagination;
		
		$this->load_theme_view('index', $data);
	}

	 /**
     * 分类浏览
     * 
     * @access public
     * @param  string $slug
     * @param  int    $page
     * @return void
     */
	public function tag($slug, $page = 1)
	{
		if(empty($slug) || !is_numeric($page))
		{
			redirect(site_url());
		}
		
		$tag = $this->metas_mdl->get_meta_by_slug(trim($slug));
		
		if(!$tag)
		{
			show_error('标签不存在或已被主人删除');
			exit();
		}
				
		/** 分页参数 */
		$this->_init_pagination($page);
		
		$this->_posts = $this->posts_mdl->get_posts_by_meta($slug, 'tag', 'post', 'publish', 'posts.*', $this->_limit, $this->_offset)->result();
		$this->_total_count = $this->posts_mdl->get_posts_by_meta($slug, 'tag', 'post', 'publish', 'posts.*', 10000, 0)->num_rows();

		if(!empty($this->_posts))
		{
			$this->_prepare_posts();
			
			$this->_apply_pagination(site_url('tag/' . $slug) . '/%');
		}

		/** 页面初始化 */
		$data['page_title'] = $tag->name;
		$data['page_description'] = $tag->description;
		$data['page_keywords'] = setting_item('blog_keywords');
		$data['posts'] = $this->_posts;
		$data['parsed_feed'] = Common::render_feed_meta('tag', $tag->slug, $tag->name);
		$data['current_view_hints'] = sprintf('标记有标签 %s 的文章（第 %d 页 / 共 %d 篇）', $tag->name, $this->_current_page, $this->_total_count);
		$data['pagination'] = $this->_pagination;
		
		$this->load_theme_view('index', $data);
		
	}

	 /**
     * 按作者显示日志
     * 
     * @access public
     * @param  int  $uid
     * @param  string  $page  页码
     * @return void
     */
	public function authors($uid, $page = 1)
	{
		if(empty($uid) || !is_numeric($uid) || !is_numeric($page))
		{
			redirect(site_url());
		}
		
		/** 分页参数 */
		$this->_init_pagination($page);
		
		$uid = intval($uid);
		$author = NULL;
		
		$this->_posts = $this->posts_mdl
						     ->get_posts_by_author($uid, 'post', 'publish', $this->_limit, $this->_offset)
						     ->result();
		$this->_total_count = $this->posts_mdl
								   ->get_posts_by_author($uid, 'post', 'publish', 10000, 0)
								   ->num_rows();
		
		if(!empty($this->_posts))
		{
			$this->_prepare_posts();
			
			list($temp) = $this->_posts;
			$author = $temp->screenName;
			unset($temp); 
			
			$this->_apply_pagination(site_url('authors/' . $uid) . '/%');
		}

		/** 页面初始化 */
		$data['page_title'] = $author;
		$data['page_description'] = setting_item('blog_description');
		$data['page_keywords'] = setting_item('blog_keywords');
		$data['posts'] = $this->_posts;
		$data['parsed_feed'] = Common::render_feed_meta();
		$data['current_view_hints'] = sprintf('%s 所写的文章（第 %d 页 / 共 %d 篇）', $author, $this->_current_page, $this->_total_count);
		$data['pagination'] = $this->_pagination;
		
		$this->load_theme_view('index', $data);
	}

	 /**
     * 归档
     * 
     * @access public
     * @param  int  $year 归档年（必需）
     * @param  int  $month 归档月（可选）
     * @param  int  $day  归档日（可选）
     * @param  string  $page  页码
     * @return void
     */
	public function archives($year, $month = NULL, $day = NULL, $page = 'p1')
	{
		if(empty($year))
		{
			redirect(site_url());
		}
		
		/** 基本参数 */
		$year = intval($year);
		$month = intval($month);
		$day  = intval($day);		
		$date = $this->_archive_hints($year, $month, $day);

		/** 分页参数 */
		$page = str_replace('p','', $page);
		$this->_init_pagination($page);

		$this->_posts = $this->posts_mdl
							 ->get_posts_by_date($year, $month, $day, $this->_limit, $this->_offset)
							 ->result();
		$this->_total_count = $this->posts_mdl
								   ->get_posts_by_date($year, $month, $day, 10000, 0)
								   ->num_rows();
		
		if(!empty($this->_posts))
		{
			$this->_prepare_posts();

			$this->_apply_pagination(site_url($this->_uri).'/p%/');
		}
		
		/** 页面初始化 */
		$data['page_title'] = $date;
		$data['page_description'] = sprintf('日志归档：%s', $date);
		$data['page_keywords'] = setting_item('blog_keywords');
		$data['posts'] = $this->_posts;
		$data['parsed_feed'] = Common::render_feed_meta();
		$data['current_view_hints'] = sprintf('%s日志归档（第 %d 页 / 共 %d 篇）', $date, $this->_current_page, $this->_total_count);
		$data['pagination'] = $this->_pagination;
		
		$this->load_theme_view('index', $data);

	}

	 /**
     * 搜索
     * 
     * @access public
     * @return void
     */
	public function search()
	{
		$keywords = strip_tags($this->input->get('s', TRUE));
		$page = strip_tags($this->input->get('p', TRUE));
		
		/** 分页参数 */
		$this->_init_pagination($page);

		$this->_posts = $this->posts_mdl
							 ->get_posts('post', 'publish', NULL, $this->_limit, $this->_offset, 0, $keywords, TRUE)
							 ->result();
							 
		$this->_total_count = $this->posts_mdl
								   ->get_posts('post', 'publish', NULL, 10000, 0, 0, $keywords, TRUE)
								   ->num_rows();
								   
		if(!empty($this->_posts))
		{
			$this->_prepare_posts();

			$this->_apply_pagination(site_url('search?s='. urlencode($keywords)), FALSE, 'p');
		}

		/** 页面初始化 */
		$data['page_title'] = sprintf('搜索：%s', $keywords);
		$data['page_description'] = setting_item('blog_description');
		$data['page_keywords'] = setting_item('blog_keywords');
		$data['posts'] = $this->_posts;
		$data['parsed_feed'] = Common::render_feed_meta();
		$data['current_view_hints'] = sprintf('关键字 %s 的搜索结果（第 %d 页 / 共 %d 篇）', $keywords, $this->_current_page, $this->_total_count);
		$data['pagination'] = $this->_pagination;
		
		$this->load_theme_view('index', $data);
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
