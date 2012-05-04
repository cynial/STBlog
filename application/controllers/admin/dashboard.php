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
 * STBLOG Dashboard Controller Class
 *
 * 控制台控制器
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Dashboard extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();

	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		/** 页面导航栏和标题 */
		$this->_data['page_title'] = '网站概要';
		$this->_data['parentPage'] = 'dashboard';
		$this->_data['currentPage'] = 'dashboard';
	}
	
	 /**
     * 默认执行函数
     * 
     * @access public
     * @return void
     */
	public function index()
	{
		/** 权限确认 */
		$this->auth->exceed('contributor');
		
		/** 最新日志文章5条 */
		$my_recent_posts = $this->posts_mdl->get_posts('post', 'publish', $this->user->uid, 5, 0);
		
		if($my_recent_posts->num_rows() >0)
		{
			foreach($my_recent_posts->result() as $recent_post)
			{
				$this->metas_mdl->get_metas($recent_post->pid);
				
				$recent_post->categories = Common::format_metas($this->metas_mdl->metas['category']);
			}
		}
		
		/** 最新评论5条 */
		$my_recent_cmts = $this->comments_mdl->get_cmts_by_owner('comment', 'approved', $this->user->uid, 5, 0);
		
		if($my_recent_cmts->num_rows() > 0)
		{
			foreach($my_recent_cmts->result() as $recent_cmt)
			{
				$recent_cmt->parent_post = $this->posts_mdl->get_post_by_id('pid', $recent_cmt->pid);
			}
		}
		
		$this->_data['my_recent_posts'] = $my_recent_posts;
		$this->_data['my_recent_cmts'] = $my_recent_cmts; 
		
		$this->load->view('admin/dashboard',$this->_data);
	}
}

/* End of file dashboard.php */
/* Location: ./application/controllers/admin/dashboard.php */