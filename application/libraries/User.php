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
 * STBLOG User library Class
 *
 * 本类包含用户Domain的核心逻辑 
 *			
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class User
{
	/**
     * user domain
     *
     * @access private
     * @var array
     */
    private $_user = array();

	/**
     * 用户ID
     *
     * @access public
     * @var integer
     */
	public $uid = 0;

	/**
     * 登录用户名
     *
     * @access public
     * @var string
     */
	public $name = '';

	/**
     * Email
     *
     * @access public
     * @var string
     */
	public $mail = '';

	/**
     * 昵称
     *
     * @access public
     * @var string
     */
	public $screenName = '';

	/**
     * 帐号创建日期
     *
     * @access public
     * @var string
     */
	public $created = 0;

	/**
     * 最后活跃时间
     *
     * @access public
     * @var string
     */
	public $activated = 0;

	/**
     * 上次登录
     *
     * @access public
     * @var string
     */
	public $logged = 0;

	/**
     * 所属用户组
     *
     * @access public
     * @var string
     */
	public $group = 'visitor';

	/**
     * 本次登录Token
     *
     * @access public
     * @var string
     */
	public $token = '';

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
		
		$this->_user = unserialize($this->_CI->session->userdata('user'));
		
		/** 初始化工作 */
		if(!empty($this->_user))
		{
			$this->uid = $this->_user['uid'];
			$this->name = $this->_user['name'];
			$this->mail = $this->_user['mail'];
			$this->url = $this->_user['url'];
			$this->screenName = $this->_user['screenName'];
			$this->created = $this->_user['created'];
			$this->activated = $this->_user['activated'];
			$this->logged = $this->_user['logged']; 
			$this->group = $this->_user['group']; 
			$this->token = $this->_user['token'];
		}
		
		log_message('debug', "STBLOG: User Domain library Class Initialized");
    }
}

/* End of file User.php */
/* Location: ./application/libraries/User.php */