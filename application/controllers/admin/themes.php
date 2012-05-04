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
 * STBLOG Themes Class
 *
 * 本类用于Themes管理逻辑
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Themes extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();
	
	/**
     * 当前主题
     *
     * @access private
     * @var array
     */
	private $_current_theme = '';

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
		
		$this->_current_theme = setting_item('current_theme');
		
		$this->load->model('themes_mdl');
		
		$this->_data['parentPage'] = 'dashboard';
		$this->_data['currentPage'] = 'themes';
	}
	
	public function index()
	{
		redirect('admin/themes/manage');
	}

	public function manage()
	{
		$this->_data['page_title'] = '网站外观';
		$this->_data['themes'] = $this->themes_mdl->get_all();

		$this->load->view('admin/themes', $this->_data);
	}
		
	public function editor($theme = NULL)
	{
		if(empty($theme))
		{
			$theme = $this->_current_theme;
		}
		
		$theme = strtolower($theme);
		
		$current_file = $this->input->get('file',TRUE);
		$current_file = ($current_file) ? $current_file : 'index.php';
		
		if(preg_match("/^([_0-9a-z-\.\ ])+$/i", $theme)
        && is_dir($dir = FCPATH . ST_THEMES_DIR . DIRECTORY_SEPARATOR . $theme)) 
        {
        	$files = glob($dir . '/*.{php,PHP,js,JS,css,CSS,vbs,VBS}', GLOB_BRACE);
        	
        	foreach ($files as &$file) 
            {
                if (file_exists($file)) 
                {
                    $file = basename($file);
                }
                else
                {
                	unset($file);
                }
            }
        	
        	if(preg_match("/^([_0-9a-z-\.\ ])+$/i", $current_file)
            && file_exists($dir . DIRECTORY_SEPARATOR . $current_file)) 
            {
                $current_content = htmlspecialchars(read_file($dir . DIRECTORY_SEPARATOR  . $current_file));
                
                $this->_data['theme'] = $theme;
                $this->_data['page_title'] = '编辑文件：'.$current_file;
                $this->_data['current_file'] = $current_file;
                $this->_data['files'] = $files;
            	$this->_data['current_content'] = $current_content;
            	$this->_data['content_is_writeable'] = is_writeable($dir . DIRECTORY_SEPARATOR . $current_file);
            
            	$this->load->view('admin/theme_editor', $this->_data);
            	
            	return;
            }
        }
        
        show_error('风格文件不存在');
        exit();
	}
	
	
	public function activate($theme)
	{
		$theme = strtolower($theme);
		
		if(preg_match("/^([_0-9a-z-\.\ ])+$/i", $theme)
        && is_dir($dir = FCPATH . ST_THEMES_DIR . DIRECTORY_SEPARATOR . $theme)) 
        {
        	$this->themes_mdl->activate($theme);
        	
        	$this->session->set_flashdata('success', '已设置 '. $theme .' 为当前网站外观');
        	go_back();
        }
        
        show_error('主题'. $theme .'不存在');
        exit();
    }
    
    public function edit()
    {
    	$theme = $this->input->post('theme',TRUE);
    	$file = $this->input->post('file',TRUE);
    	$content = htmlspecialchars_decode($this->input->post('content',TRUE));
    	
    	$path = FCPATH . ST_THEMES_DIR . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR  . $file;
    	
    	if (file_exists($path) && is_writeable($path)) 
    	{
            if(write_file($path, $content))
           	{
                $this->session->set_flashdata('success', '文件 '. $file .' 的更改已经保存');
            }
            else
            {
            	$this->session->set_flashdata('success', '文件 '. $file .' 无法被写入');
            }
            
            go_back();
        } 
        else 
        {
            show_error('您编辑的文件不存在');
        	exit();
        }

    }
}

/* End of file Themes.php */
/* Location: ./application/controllers/admin/Themes.php */