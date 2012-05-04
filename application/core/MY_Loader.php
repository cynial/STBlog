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
 * MY_Loader
 *
 * Override the CI Loader library in order to make the whole system themeable.
 *
 * 重写CI的loader库，以支持现有架构下的博客皮肤系统
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class MY_Loader extends CI_Loader 
{

	/**
    * 系统当前皮肤
    * 
    * @access public
    * @var string
    */
	public $theme = 'default';
	
	 /**
	 * 构造函数
	 * 
	 * @access public
	 * @return void
	 */
    public function __construct() 
    {
		parent::__construct();
    }

	 /**
	 * 打开皮肤功能
	 * 
	 * @access public
	 * @return void
	 */ 
    public function switch_theme_on()
    {
		$this->_ci_view_paths = array(FCPATH . ST_THEMES_DIR . DIRECTORY_SEPARATOR . $this->theme . DIRECTORY_SEPARATOR	=> TRUE);
		
    }

	 /**
	 * 关闭皮肤功能
	 * 
	 * @access public
	 * @return void
	 */ 
    public function switch_theme_off()
    {
    	//just do nothing
    }
    
    
    
}

/* End of file MY_Loader.php */
/* Location: ./application/libraries/MY_Loader.php */
