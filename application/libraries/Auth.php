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
 * STBLOG Auth Library Class
 *
 * 控制用户登陆和登出，以及一个简单的权限控制ACL实现
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Auth
{
	/**
     * 用户
     *
     * @access private
     * @var array
     */
    private $_user = array();
    
    /**
     * 是否已经登录
     * 
     * @access private
     * @var boolean
     */
    private $_hasLogin = NULL;
    
    /**
     * 用户组
     *
     * @access public
     * @var array
     */
    public $groups = array(
            'administrator' => 0,
            'editor'		=> 1,
            'contributor'	=> 2
            );
	
	/**
    * CI句柄
    * 
    * @access private
    * @var object
    */
	private $_CI;

	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 获取CI句柄 */
		$this->_CI = & get_instance();

		$this->_CI->load->model('users_mdl');
		
		$this->_user = unserialize($this->_CI->session->userdata('user'));
		
		log_message('debug', "STBLOG: Authentication library Class Initialized");
    }
	
    /**
     * 判断用户是否已经登录
     *
     * @access public
     * @return void
     */
	public function hasLogin()
	{
		/** 检查session，并与数据库里的数据相匹配 */
		if (NULL !== $this->_hasLogin)
		{
            return $this->_hasLogin;
        }
		else 
		{
			if(!empty($this->_user) && NULL !== $this->_user['uid'])
			{
				$user = $this->_CI->users_mdl->get_user_by_id($this->_user['uid']);
				
				if($user && $user['token'] == $this->_user['token'])
				{
					$user['activated'] = time();
					
					$this->_CI->users_mdl->update_user($this->_user['uid'],$user);
					
					return ($this->_hasLogin = TRUE);
				}
			}
			
			return ($this->_hasLogin = FALSE);
		}
	}
	
	 /**
     * 判断用户权限
     *
     * @access 	public
     * @param 	string 	$group 	用户组
     * @param 	boolean $return 是否为返回模式
     * @return 	boolean
     */
	public function exceed($group, $return = false)
	{
		/** 权限验证通过 */
        if(array_key_exists($group, $this->groups) && $this->groups[$this->_user['group']] <= $this->groups[$group]) 
		{
            return TRUE;
        }
		
		/** 权限验证未通过，同时为返回模式 */
		if($return)
		{
			return FALSE;
		}
		
		/** 非返回模式 */
		show_error('禁止访问：你的权限不足');
		return;
	}
	
	 /**
     * 处理用户登出
     * 
     * @access public
     * @return void
     */
	public function process_logout()
	{
		$this->_CI->session->sess_destroy();
		
		redirect('admin/login');
	}
	
	/**
     * 处理用户登录
     *
     * @access public
     * @param  array $user 用户信息
     * @return boolean
     */
	public function process_login($user)
	{
		/** 获取用户信息 */
		$this->_user = $user;
		
		/** 每次登陆时需要更新的数据 */
		$this->_user['logged'] = now();
		$this->_user['activated'] = $user['logged'];
		/** 每登陆一次更新一次token */
		$this->_user['token'] = sha1(now().rand());
		
		if($this->_CI->users_mdl->update_user($this->_user['uid'],$this->_user))
		{
			/** 设置session */
			$this->_set_session();
			$this->_hasLogin = TRUE;
			
			return TRUE;
		}
		
		return FALSE;
	}

	/**
     * 设置session
     *
     * @access private
     * @return void
     */
	private function _set_session() 
	{
		$session_data = array('user' => serialize($this->_user));
		
		$this->_CI->session->set_userdata($session_data);
	}

}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */
