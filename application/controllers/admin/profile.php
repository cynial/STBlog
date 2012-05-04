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
 * STBLOG Profile Controller Class
 *
 * 个人设置控制器
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Profile extends ST_Auth_Controller {
	
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
		
		$this->_data['page_title'] = '个人设置';
		$this->_data['parentPage'] = 'dashboard';
		$this->_data['currentPage'] = 'profile';
	}
	
	 /**
	 * 默认执行函数
	 * 
	 * @access public
	 * @return void
	 */
	public function index()
	{
		$this->auth->exceed('contributor');
		
		$this->load->view('admin/profile', $this->_data);
	}
	
	 /**
     * 修改个人密码
     * 
     * @access public
     * @return void
     */
	public function updatePassword()
	{
		$this->form_validation->set_rules('password', '新的密码', 'required|min_length[6]|trim|matches[confirm]');
		$this->form_validation->set_rules('confirm', '确认的密码', 'required|min_length[6]|trim');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/profile', $this->_data);
		}
		else
		{
			$user = $this->users_mdl->get_user_by_id($this->user->uid);
			
			if($user)
			{	
				$user['password'] = $this->input->post('password', TRUE);
				
				$this->users_mdl->update_user($this->user->uid, $user, FALSE);
			}
			
			$this->session->set_flashdata('success', '您的密码已经更新');
			redirect('admin/profile');
		}

	}

	 /**
     * 修改个人信息
     * 
     * @access public
     * @return void
     */
	public function updateProfile()
	{
		$this->form_validation->set_rules('screenName', '昵称', 'trim|callback__screenName_check');
		$this->form_validation->set_rules('url', '个人主页', 'trim|prep_url');
		$this->form_validation->set_rules('mail', '邮箱地址', 'required|trim|valid_email|callback__email_check');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/profile', $this->_data);
		}
		else
		{
			$user = $this->users_mdl->get_user_by_id($this->user->uid);
			
			if($user)
			{
				$user['screenName'] = $this->input->post('screenName') ? $this->input->post('screenName',TRUE):trim($user['name']);
				$user['url'] = $this->input->post('url',TRUE);
				$user['mail'] = $this->input->post('mail',TRUE);
				
				$this->users_mdl->update_user($this->user->uid, $user);
			}
			
			$this->auth->process_login($user);//extend the lease
			$this->session->set_flashdata('success', '您的档案已经更新');
			
			redirect('admin/profile');
		}
	}
	
	 /**
     * 回调函数：检查Email是否唯一
     * 
     * @access public
     * @param $str 输入值
     * @return bool
     */
	public function _email_check($str)
	{
		if(!empty($str))
		{
			if($this->users_mdl->check_exist('mail', $str, $this->user->uid))
			{
				$this->form_validation->set_message('_email_check', '系统已经存在一个为 '.$str.' 的邮箱');
				
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
	
	 /**
     * 回调函数：检查screenName是否唯一
     * 
     * @access public
     * @param $str 输入值
     * @return bool
     */
	public function _screenName_check($str)
	{
		if(!empty($str))
		{
			if($this->users_mdl->check_exist('screenName',$str, $this->user->uid))
			{
				$this->form_validation->set_message('_screenName_check', '系统已经存在一个为 '.$str.' 的昵称');
				
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
	
}

/* End of file Profile.php */
/* Location: ./application/controllers/admin/Profile.php */