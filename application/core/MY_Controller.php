<?php if (!defined('BASEPATH')) exit('No direct access allowed.');
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
 * STBLOG 前台父控制器
 *
 * 前台的所有控制器都需要继承这个类，它不包含验证
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class ST_Controller extends CI_Controller {
    
	function __construct() {
		
		parent::__construct();

		/** 检查服务器上的PHP版本 */
		$this->utility->check_compatibility();
		
		/** 检查站点当前状态 */
		$this->utility->check_blog_status();
	    
	    /** 设置当前使用的主题皮肤 */
		$this->load->theme = setting_item('current_theme');
	    
	    /** 前台页面均使用主题皮肤功能 */
	    $this->load->switch_theme_on();
    }


    /**
     * 加载某个主题页面下的VIEW
     *
     * 第1/2/4个参数分别对应CI原有的load view中的第1/2/3参数，这里的第三个参数用于一些特殊场合：
	 * 当整站缓存功能被开启时，为了避免当前被操作的页面缓存，可以设置第三个参数为FALSE避免。
     *
     *
     * @access   public
     * @param    string
     * @param    array
	 * @param	 bool
     * @param    bool
     * @return   void
     */
	function load_theme_view($view, $vars = array(), $cached = TRUE, $return = FALSE)
	{
		/** 加载对应主题下的view */
		if(file_exists(FCPATH. ST_THEMES_DIR. DIRECTORY_SEPARATOR . setting_item('current_theme'). DIRECTORY_SEPARATOR . $view .'.php')) 
		{
			echo $this->load->view($view, $vars,$return);
		}
		else 
		{
			show_404();
		}
		
		/** 是否开启缓存? */
		if(1 == intval(setting_item('cache_enabled')) && $cached)
		{
			$cache_expired = setting_item('cache_expire_time');
			
			$cache_expired = ($cache_expired && is_numeric($cache_expired)) ? intval($cache_expired) : 60;
			
			/** 开启缓存 */
			$this->output->cache($cache_expired);
		}
		
	}	

}

// ------------------------------------------------------------------------

/**
 * STBLOG 后台父控制器
 *
 * 后台的所有控制器都需要继承这个类，主要包含验证
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class ST_Auth_Controller extends CI_Controller {


    function __construct() {
        
		parent::__construct();
		
		/** 加载验证库 */
		$this->load->library('auth');
		
		/** 检查登陆 */		
		if(!$this->auth->hasLogin())
		{
			redirect('admin/login?ref='.urlencode($this->uri->uri_string()));
		}
		
		/** 加载后台控制器公共库 */
	   	$this->load->library('form_validation');
	   	$this->load->library('user');

		/** 加载后台控制器公共模型 */
		$this->load->model('users_mdl');
		
		/** 加载后台控制器helper */
		
		
	    /** 后台管理页面，不使用皮肤 */
	    $this->load->switch_theme_off();
    }
}

/* End of file MY_Controller.php */
/* Location: ./application/libraries/MY_Controller.php */
