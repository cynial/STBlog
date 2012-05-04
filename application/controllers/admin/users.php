<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * STBlog
 *
 * 基于Codeigniter的单用户多权限开源博客系统
 * 
 *
 * @package		STBLOG
 * @author		Saturn
 * @copyright	Copyright (c) 2009 - 2010, cnsaturn.com.
 * @license		GNU General Public License 2.0
 * @link		http://code.google.com/p/stblog/
 * @since		Version 0.1
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * STBLOG Users Class
 *
 * 本类用于Users管理逻辑
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Users extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();
	
	/**
     * 当前用户ID
     *
     * @access private
     * @var integer
     */	
	private $_uid = 0;

	/**
     * 解析函数
     *
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		/** privilege confirm */
		$this->auth->exceed('administrator');
		
		/** common data */
		$this->_data['parentPage'] = 'manage-posts';
		$this->_data['currentPage'] = 'manage-users';
		$this->_data['page_title'] = '管理用户';
	}

	 /**
     * 配置表单验证规则
     * 
     * @access private
     * @return void
     */
	private function _load_validation_rules()
	{
		$this->form_validation->set_rules('uname', '用户名', 'required|trim|alpha_numeric|callback__name_check|strip_tags');
		$this->form_validation->set_rules('password', '新的密码', 'required|min_length[6]|trim|matches[confirm]');
		$this->form_validation->set_rules('confirm', '确认的密码', 'required|min_length[6]|trim');
		$this->form_validation->set_rules('screenName', '昵称', 'trim|callback__screenName_check|strip_tags');
		$this->form_validation->set_rules('url', '个人主页', 'trim|prep_url');
		$this->form_validation->set_rules('mail', '邮箱地址', 'required|trim|valid_email|callback__email_check');
		$this->form_validation->set_rules('group', '用户组', 'trim');
	}
	
	
	 /**
     * 回调函数：检查Name是否唯一
     * 
     * @access 	public
     * @param 	$str 输入值
     * @return 	bool
     */
	public function _name_check($str)
	{
		if($this->users_mdl->check_exist('name', $str, $this->_uid))
		{
			$this->form_validation->set_message('_name_check', '系统已经存在一个为 '.$str.' 的用户名');
			
			return FALSE;
		}
			
		return TRUE;
	}
	
	
	 /**
     * 回调函数：检查Email是否唯一
     * 
     * @access 	public
     * @param 	$str 输入值
     * @return 	bool
     */
	public function _email_check($str)
	{
		if($this->users_mdl->check_exist('mail', $str, $this->_uid))
		{
			$this->form_validation->set_message('_email_check', '系统已经存在一个为 '.$str.' 的邮箱');
			
			return FALSE;
		}
			
		return TRUE;
	}
	
	 /**
     * 回调函数：检查screenName是否唯一
     * 
     * @access 	public
     * @param 	$str 输入值
     * @return 	bool
     */
	public function _screenName_check($str)
	{
		if($this->users_mdl->check_exist('screenName', $str, $this->_uid))
		{
			$this->form_validation->set_message('_screenName_check', '系统已经存在一个为 '.$str.' 的昵称');
			
			return FALSE;
		}
			
		return TRUE;
	}

	 /**
     * 添加一个用户
     * 
     * @access 	private
     * @return 	void
     */
	private function _add_user()
	{
		$this->_data['page_title'] = '增加用户';
		$this->_data['group'] = 'contributor';
			
		$this->_load_validation_rules();
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/add_user',$this->_data);
		}
		else
		{
			$this->users_mdl->add_user(
				array(
					'name' 		=>	$this->input->post('uname',TRUE),
					'password' 	=>	$this->input->post('password',TRUE),
					'mail'		=>	$this->input->post('mail',TRUE),
					'url'		=>	$this->input->post('url',TRUE),
					'screenName'=>	($this->input->post('screenName'))?$this->input->post('screenName',TRUE):$this->input->post('uname',TRUE),
					'created'	=>	time(),
					'activated'	=>	0,
					'logged'	=>	0,
					'group'		=>	$this->input->post('group',TRUE)
				)
			);
			
			$this->session->set_flashdata('success', '成功添加一个用户账号');
			go_back();
		}
	}

	 /**
     * 编辑一个用户
     * 
     * @access 	private
     * @param 	$uid
     * @return 	void
     */
	private function _edit_user($uid)
	{
		$this->_uid = $uid;
		$user = $this->users_mdl->get_user_by_id($uid);

		if(!$user)
		{
			show_error('用户不存在或已经被删除');
			exit();
		}
		
		$this->_data['uid'] = $user['uid'];
		$this->_data['uname'] = $user['name'];
		$this->_data['screenName'] = $user['screenName'];
		$this->_data['url'] = $user['url'];
		$this->_data['mail'] = $user['mail'];
		$this->_data['password'] = '';
		$this->_data['group'] = $user['group'];
		$this->_data['page_title'] = '编辑用户: '.$user['name'];
	
		$this->_load_validation_rules();
		
		if ($this->form_validation->run() == FALSE)
		{	
			$this->load->view('admin/add_user',$this->_data);
		}
		else
		{
			$this->users_mdl->update_user(
				$uid,
				array(
					'password' 	=>	$this->input->post('password',TRUE),
					'mail'		=>	$this->input->post('mail',TRUE),
					'url'		=>	$this->input->post('url',TRUE),
					'screenName'=>	($this->input->post('screenName'))?$this->input->post('screenName',TRUE):$this->input->post('name',TRUE),
					'group'		=>	$this->input->post('group',TRUE)
				),
				FALSE
			);
			
			$this->session->set_flashdata('success', '成功修改用户 '. $user['name'] .'的账号信息');
			go_back();
		}
	}
	
	 /**
     * 默认执行函数
     * 
     * @access public
     * @return void
     */
	public function index()
	{
		redirect('admin/users/manage');
	}

	 /**
     * 用户管理列表
     * 
     * @access public
     * @return void
     */
	public function manage()
	{
		$users = $this->users_mdl->get_users();
		
		foreach($users->result() as $user)
		{
			$user->posts_num = $this->posts_mdl->get_posts_by_author($user->uid)->num_rows();
		}
		
		$this->_data['users'] = $users;
		
		$this->load->view('admin/manage_users',  $this->_data);	
	
	}

	 /**
     * 用户操作分发器
     * 
     * @access public
     * @return void
     */
	public function user()
	{
		if (FALSE === $this->uri->segment(4))
		{
			$this->_add_user();
		}
		else
		{
			$uid = $this->security->xss_clean($this->uri->segment(4));
			is_numeric($uid)?$this->_edit_user($uid):show_error('禁止访问：危险操作');
		}
	
	}

	 /**
     * 批量删除用户
     * 
     * @access public
     * @return void
     */
	public function remove()
	{
		$users = $this->input->post('uid',TRUE);
		$deleted = 0;
		
		if($users && is_array($users))
		{
			foreach($users as $user)
			{
				/** 不能删除自己 */
				if($user == $this->user->uid)
				{
					continue;
				}
				
				$posts_num = $this->posts_mdl->get_posts_by_author($user)->num_rows();
				
				/** 不能删除文章数大于0的作者 */
				if($posts_num > 0)
				{
					continue;
				}
				
				$this->users_mdl->remove_user($user);
				$deleted++;
			}
		
		}
		
		$msg = ($deleted > 0)?'用户已经删除':'没有用户被删除';
        $notify = ($deleted > 0)?'success':'error';
        
        $this->session->set_flashdata($notify, $msg);
		go_back();
	
	}
	
		
}

/* End of file Users.php */
/* Location: ./application/controllers/admin/Users.php */
