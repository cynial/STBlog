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
 * STBLOG Settings Class
 *
 * 本类用于Settings管理逻辑
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Settings extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();

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
		
		$this->load->model('settings_mdl');
		
		/** load default settings from db cache */
		$this->_load_default_settings();
		
		$this->_data['parentPage'] = 'manage-settings';
		
		//var_dump($this->_data);
	}
	
	public function index()
	{
		redirect('admin/settings/general');
	}

	public function general()
	{
		$this->_data['currentPage'] = 'settings-general';
		$this->_data['page_title'] = '基本设置';
		
		$this->form_validation->set_rules('blog_title', '', 'trim|strip_tags');
		$this->form_validation->set_rules('blog_slogan', '', 'trim|strip_tags');
		$this->form_validation->set_rules('blog_description', '', 'trim|strip_tags');
		$this->form_validation->set_rules('blog_keywords', '', 'trim|strip_tags');
		$this->form_validation->set_rules('offline_reason', '', 'trim|strip_tags');
		$this->form_validation->set_rules('upload_dir', '', 'trim|strip_tags');
		$this->form_validation->set_rules('upload_exts', '', 'trim|strip_tags');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/settings_general', $this->_data);
		}
		else
		{
			/** 获取配置数据 */
			$setting = array();
			
			$setting['blog_title'] 	= $this->input->post('blog_title',TRUE);
			$setting['blog_slogan'] = $this->input->post('blog_slogan',TRUE);
			$setting['blog_description'] = $this->input->post('blog_description',TRUE);
			$setting['blog_keywords'] = $this->input->post('blog_keywords',TRUE);
			$setting['offline_reason'] = $this->input->post('offline_reason',TRUE);
			$setting['upload_dir'] = $this->input->post('upload_dir',TRUE);
			$setting['upload_exts'] = $this->input->post('upload_exts',TRUE);
			$setting['blog_status'] = $this->input->post('blog_status',TRUE);
			
			if(empty($setting['upload_dir']))
			{
				/** 如果上传路径为空则使用默认 */
				$setting['upload_dir'] = 'uploads/';
			}
			
			if(substr($setting['upload_dir'],strlen($setting['upload_dir'])-1) !== '/')
			{
				$setting['upload_dir'] .= '/'; 
			}
			
			if(empty($setting['upload_exts']))
			{
				$setting['upload_exts'] = '*.zip;*.tar.gz;*.rar;*.jpg;*.gif;*.png;*.jpeg;*.bmp;*.tiff';
			}
			
			/** 更新数据库 */
			foreach($setting as $key => $val)
			{
				$this->settings_mdl->update_setting_item($key, $val);
			}
			
			$this->cacheClear(FALSE);
			
			$this->session->set_flashdata('success', '配置已更新');
			redirect('admin/settings/general');
		}
	}
	
	
	public function discussion()
	{
		$this->_data['currentPage'] = 'settings-discussion';
		$this->_data['page_title'] = '评论设置';
		
		$this->form_validation->set_rules('comment_date_format', '', 'trim|strip_tags');
		$this->form_validation->set_rules('comments_list_size', '', 'trim|is_natural_no_zero|strip_tags');
		$this->form_validation->set_rules('comments_allowed_html', '', 'trim');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/settings_discussion', $this->_data);
		}
		else
		{
			/** 获取配置数据 */
			$setting = array();
			
			/**	boring stuff: get post data from form */
			$setting['comments_date_format']		= $this->input->post('comments_date_format',TRUE);
			$setting['comments_list_size']			= $this->input->post('comments_list_size',TRUE);
			$setting['comments_url_no_follow']		= $this->input->post('comments_url_no_follow',TRUE);
			$setting['comments_require_moderation'] = $this->input->post('comments_require_moderation',TRUE);
			$setting['comments_auto_close'] 		= $this->input->post('comments_auto_close',TRUE);
			$setting['comments_require_mail'] 		= $this->input->post('comments_require_mail',TRUE);
			$setting['comments_require_url'] 		= $this->input->post('comments_require_url',TRUE);
			$setting['comments_allowed_html'] 		= $this->input->post('comments_allowed_html',TRUE);
			
			if(empty($setting['comments_date_format']))
			{
				/** 如果日期格式为空则使用默认 */
				$setting['comments_date_format'] = 'Y-m-d';
			}
			
			if(empty($setting['comments_list_size']) || 0 > intval($setting['comments_list_size']))
			{
				$setting['comments_list_size'] = 10;
			}
			
			/** 更新数据库 */
			foreach($setting as $key => $val)
			{
				$this->settings_mdl->update_setting_item($key, $val);
			}
			
			$this->cacheClear(FALSE);
			
			$this->session->set_flashdata('success', '配置已更新');
			redirect('admin/settings/discussion');
		}
	}
	
	public function reading()
	{
		$this->_data['currentPage'] = 'settings-reading';
		$this->_data['page_title'] = '文章设置';
		
		$this->form_validation->set_rules('post_date_format', '', 'trim|strip_tags');
		$this->form_validation->set_rules('posts_page_size', '', 'trim|is_natural_no_zero|strip_tags');
		$this->form_validation->set_rules('posts_list_size', '', 'trim|is_natural_no_zero|strip_tags');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/settings_reading', $this->_data);
		}
		else
		{
			/** 获取配置数据 */
			$setting = array();
			
			/**	boring stuff: get post data from form */
			$setting['post_date_format']		= $this->input->post('post_date_format',TRUE);
			$setting['posts_page_size']			= $this->input->post('posts_page_size',TRUE);
			$setting['posts_list_size']			= $this->input->post('posts_list_size',TRUE);
			$setting['feed_full_text']		= $this->input->post('feed_full_text',TRUE);
			
			if(empty($setting['post_date_format']))
			{
				/** 如果日期格式为空则使用默认 */
				$setting['post_date_format'] = 'Y-m-d';
			}
			
			if(empty($setting['posts_list_size']) || 0 > intval($setting['posts_list_size']))
			{
				$setting['posts_list_size'] = 10;
			}
			
			if(empty($setting['posts_page_size']) || 0 > intval($setting['posts_page_size']))
			{
				$setting['posts_page_size'] = 5;
			}
			
			/** 更新数据库 */
			foreach($setting as $key => $val)
			{
				$this->settings_mdl->update_setting_item($key, $val);
			}
			
			$this->cacheClear(FALSE);
			
			$this->session->set_flashdata('success', '配置已更新');
			redirect('admin/settings/reading');
		}
	}
	
	public function cache()
	{
		$this->_data['currentPage'] = 'settings-cache';
		$this->_data['page_title'] = '静态缓存设置';
		
		$this->form_validation->set_rules('cache_expire_time', '', 'trim|is_natural_no_zero|strip_tags');
		$this->form_validation->set_rules('cache_file_limit', '', 'trim|is_natural_no_zero|strip_tags');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('admin/settings_cache', $this->_data);
		}
		else
		{
			/** 获取配置数据 */
			$setting = array();
			
			/**	boring stuff: get post data from form */
			$setting['cache_enabled']		= $this->input->post('cache_enabled',TRUE);
			$setting['cache_expire_time']	= $this->input->post('cache_expire_time',TRUE);
			$setting['cache_file_limit']	= $this->input->post('cache_file_limit',TRUE);
			
			if(empty($setting['cache_expire_time']) || 0 > intval($setting['cache_expire_time']))
			{
				$setting['cache_expire_time'] = 10;
			}
			
			if(empty($setting['cache_file_limit']) || 0 > intval($setting['cache_file_limit']))
			{
				$setting['cache_file_limit'] = 200;
			}
			
			/** 更新数据库 */
			foreach($setting as $key => $val)
			{
				$this->settings_mdl->update_setting_item($key, $val);
			}
			
			$this->cacheClear(FALSE);
			
			$this->session->set_flashdata('success', '配置已更新');
			redirect('admin/settings/cache');
		}
	}
	
	public function cacheClear($return = TRUE)
	{
		/** 清空cache */
		$this->utility->clear_file_cache();
		
		/** 清空db cache*/
		$this->utility->clear_db_cache();
		
		if($return)
		{
			$this->session->set_flashdata('success', '文件缓存已更新');
			redirect('admin/settings/cache');
		}
	}
	
	private function _load_default_settings()
	{
		$settings = &get_settings();
		
		/** 装载默认数据*/
		foreach($settings as $key => $val)
		{
			$this->_data[$key] = $settings[$key];
		}
	}
	
		
}

/* End of file Settings.php */
/* Location: ./application/controllers/admin/Settings.php */