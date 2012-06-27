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
 * STBLOG Login Controller Class
 *
 * 用户登陆和登出
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Login extends CI_Controller {

	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data;

	/**
     * Referer
     *
     * @access public
     * @var string
     */
	public $referrer;

	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		/** 由于Login继承自Controller，需要手动加载必须的库 */
		$this->load->library('auth');
		$this->load->library('form_validation');
		
		$this->load->model('users_mdl', 'users');
		
		$this->_check_referrer();
		
		$this->_data['page_title'] = '登录';
	}
	
	 /**
     * 检查referrer
     * 
     * @access private
     * @return void
     */
	private function _check_referrer()
	{
		$ref = $this->input->get('ref', TRUE);
		
		$this->referrer = (!empty($ref)) ? $ref : '/admin/dashboard';
	}

	 /**
     * 默认执行函数
     * 
     * @access public
     * @return void
     */
	public function index()
	{
		if($this->auth->hasLogin())
		{
			redirect($this->referrer);
		}

		$this->form_validation->set_rules('name', '用户名', 'required|min_length[2]|trim');
		$this->form_validation->set_rules('password', '密码', 'required|trim');
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		
		
		if($this->form_validation->run() === FALSE)
		{
			$this->load->view('admin/login', $this->_data);
		}
		else
		{
			$user = $this->users->validate_user(
								$this->input->post('name', TRUE), 
								$this->input->post('password', TRUE)
								);
			
			if(!empty($user))
			{
				if($this->auth->process_login($user))
				{
					redirect($this->referrer);
				}
			}
			else
			{
				sleep(1);//嘿嘿，谁爆破密码就让谁睡
				
				$this->session->set_flashdata('login_error', 'TRUE');
				
				$this->_data['login_error_msg'] = '用户名或密码无效';

				
				$this->load->view('admin/login', $this->_data);
			}
		}

	}

	 /**
     * 用户登出wrapper
     * 
     * @access public
     * @return void
     */
	public function logout()
	{
		$this->auth->process_logout();
	}
	
}

/* End of file login.php */
/* Location: ./application/controllers/admin/login.php */
