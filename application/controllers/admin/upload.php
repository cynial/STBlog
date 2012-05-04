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
 * STBLOG Upload Class
 *	
 * 系统采用swf uploader处理上传文件，由于此类由flash触发，故不能采用系统默认的验证方式
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Upload extends CI_Controller {

	/**
     * 默认上传路径
     *
     * @access private
     * @var string
     */	
	private $_upload_dir = 'uploads/';
	
	/**
     * 默认允许上传后缀名
     *
     * @access private
     * @var string
     */
	private $_upload_exts = 'zip|gz|rar|swf|jpg|png|gif|jpeg';
	
	/**
     * 常用图片文件后缀名
     *
     * @access private
     * @var string
     */
	private $_image_exts = array('png','gif','jpg','jpeg','bmp','tiff','tif', 'ico', 'tga');
	 
	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('upload');
		$this->load->model('users_mdl');
		
		$this->_setup_upload_cfgs();
	}

	/**
     * 默认执行方法
     *
     * 本方法仅允许被Flash触发
     * 由于flash触发此方法时会产生一个新的session
     * 所以这里采用token来验证上传是否合法
     *
     * @access public
     * @return void
     */
	public function index()
	{
		$uid = $this->input->post('__uid', TRUE);
		$token = $this->input->post('__token', TRUE);
		
		if(empty($uid) || empty($token)) show_404();
		
		
		$user = $this->users_mdl->get_user_by_id($uid);
		
		if($user['token'] == $token && ('contributor' == $user['group'] || 'editor' == $user['group'] || 'administrator' == $user['group']))
		{
			/** 合法用户，设置执行参数并执行上传 */
			$config['upload_path'] = FCPATH.$this->_upload_dir;
			$config['allowed_types'] = $this->_upload_exts;
			$this->upload->initialize($config);
		
			if(!$this->upload->do_upload($filed = 'Filedata'))
			{
				log_message('debug', $this->upload->display_errors());
			}
			else
			{
				$upload_data = $this->upload->data();
				
				$file = array(
					'name'		=> $upload_data['file_name'],	
					'path'		=> $this->_upload_dir. $upload_data['file_name'],
					'size' 		=> $upload_data['file_size'],
					'mime' 		=> get_mime_by_extension($upload_data['orig_name']),
					'isImage' 	=> $this->_is_image($upload_data['file_name'])
				);
				
				//DB
				$attachment_data = array(
					'title' 		=>	$upload_data['file_name'],
					'slug' 			=>	$upload_data['file_name'],
					'created'		=>	time(),
					'modified'		=>	time(),
					'text'			=>	serialize($file),
					'order'			=>	0,
					'authorId'		=>	$uid,
					'type'			=>	'attachment',
					'status'		=>	'unattached',
					'commentsNum'	=>	0,
					'allowComment'	=>	0,
					'allowPing'		=>	0,
					'allowFeed'		=>	0
				);
				
				$insert_id = $this->posts_mdl->add_post($attachment_data);
				

				if(!empty($insert_id))
				{
					$this->load->helper('json');
					
					throwJson(array(
						'pid'       =>  $insert_id,
						'title'     =>  $upload_data['file_name'],
						'type'      =>  $this->_get_type($upload_data['file_name']),
						'size'      =>  $upload_data['file_size'],
						'isImage'   =>  $this->_is_image($upload_data['file_name']),
						'url'       =>  base_url().$this->_upload_dir.$upload_data['file_name'],
						'permalink' =>  site_url('attachment'.'/'.$insert_id)
					));
				}
			}
		}

		show_404();
	}
	
	/**
     * 替换上传文件
     *
     * 本方法仅允许被Flash触发
     * 由于flash触发此方法时会产生一个新的session
     * 所以这里采用token来验证上传是否合法
     *
     * @access  public
     * @param   int    $pid 文章ID
     * @return  void
     */
	public function modify($pid = 0)
	{
		$uid = $this->input->post('__uid',TRUE);
		$token = $this->input->post('__token',TRUE);
		
		if(empty($uid) || empty($token) || empty($pid) || !is_numeric($pid)) show_404();
	
		$user = $this->users_mdl->get_user_by_id($uid);
		
		$attachment = $this->posts_mdl->get_post_by_id('pid', $pid);
		
		if($attachment)
		{
			$info = unserialize($attachment->text);
		}
		else
		{
			show_404();
		}
		
		unset($attachment);
		
		if($user['token'] == $token && ('contributor' == $user['group'] || 'editor' == $user['group'] || 'administrator' == $user['group']))
		{
			/** 合法用户，设置执行参数并执行上传 */
			$config['upload_path'] = FCPATH . $this->_upload_dir;
			$config['allowed_types'] = $this->_upload_exts;
			$config['file_name'] = $info['name'];
			$config['overwrite'] = TRUE;
			
			$this->upload->initialize($config);
		
			if(!$this->upload->do_upload($filed = 'Filedata'))
			{
				log_message('debug', $this->upload->display_errors());
			}
			else
			{
				$upload_data = $this->upload->data();
				
				$file = array(
					'name'		=> $upload_data['file_name'],	
					'path'		=> $this->_upload_dir. $upload_data['file_name'],
					'size' 		=> $upload_data['file_size'],
					'mime' 		=> get_mime_by_extension($upload_data['orig_name']),
					'isImage' 	=> $this->_is_image($upload_data['file_name'])
				);
				
				//DB
				$attachment_data = array(
					'modified'		=>	time(),
					'text'			=>	serialize($file),
					'authorId'		=>	$uid,
				);
				
				if($this->posts_mdl->update_post($pid, $attachment_data))
				{
					$this->load->helper('json');
					
					throwJson(array(
						'pid'       =>  $pid,
						'title'     =>  $upload_data['file_name'],
						'type'      =>  $this->_get_type($upload_data['file_name']),
						'size'      =>  $upload_data['file_size'],
						'isImage'   =>  $this->_is_image($upload_data['file_name']),
						'url'       =>  base_url().$this->_upload_dir.$upload_data['file_name'],
						'permalink' =>  site_url('attachment'.'/'.$pid)
					));
				}
			}
		}
		show_404();
	}
	

	/**
     * 上传文件是否为图片
     *
	 *	由于SWFUPLOAD所有类型文件的MIME均为application/octet-stream
	 *	故使用Codeigniter自带的上传类无法准确获取是否为图片
     * 
     * @access private
     * @param  string  $file 文件名
     * @return string
     */
	private function _is_image($file)
	{
		$ext = $this->_get_type($file);
		
		return (in_array($ext, $this->_image_exts))?TRUE:FALSE;
	}

	/**
     * 根据文件名获取拓展名
     * 
     * @access private
     * @param  string  $file_name 文件名
     * @return string
     */
	private function _get_type($file)
	{
		$ext = '';
        
        $part = explode('.', $file);
        
        if (($length = count($part)) > 1) 
        {
            $ext = strtolower($part[$length - 1]);
        }
        
        return $ext;
	}

	/**
     * 初始化上传参数
     * 
     * @access private
     * @return void
     */
	private function _setup_upload_cfgs()
	{
		$settings = &get_settings();
		
		if(array_key_exists('upload_dir', $settings) && array_key_exists('upload_exts',$settings))
		{
			$this->_upload_dir = $settings['upload_dir'];
			
			if (!is_dir(FCPATH.$this->_upload_dir)) 
			{
				if(!$this->_make_upload_dir())
				{
					log_message('debug', '上传目录创建失败');
				}
			}
			
			$this->_upload_exts = str_replace(';','|',$settings['upload_exts']);
			$this->_upload_exts = str_replace('*.','',$this->_upload_exts);	
		}
	}
	
	/**
     * 创建上传路径
     * 
     * @access private
     * @param string $path 路径
     * @return boolean
     */
    private function _make_upload_dir($path)
    {
        if (!@mkdir($path)) 
        {
            return false;
        }
        
        $stat = @stat($path);
        $perms = $stat['mode'] & 0007777;
        @chmod($path, $perms);
        
        return true;
    }
}

/* End of file Upload.php */
/* Location: ./application/controllers/admin/Upload.php */
