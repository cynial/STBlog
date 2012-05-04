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
 * STBLOG Utility Library Class
 *
 * 实用函数
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Utility {

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

        $this->_CI =& get_instance();
		
		log_message('debug', 'STBlog: Utility class initialized.');
    }
    
	/**
    * 获取激活的插件
    * 
    * @access public
    * @return array
    */
	public function get_active_plugins()
	{
		$active_plugins = setting_item('active_plugins');
	
		if(empty($active_plugins))
		{
			return array();
		}
		
		$plugins = unserialize($active_plugins);
	
		return $plugins ? (is_array($plugins) ? $plugins : array($plugins)) : array();
	}
	
	/**
    * 检查博客当前状态
    * 
    * @access public
    * @return void
    */	
	public function check_blog_status()
	{
		if(setting_item('blog_status'))
		{
			if('off' == setting_item('blog_status'))
			{
				$title = sprintf('%s - Site Close Notice', setting_item('blog_title'));
				$heading = sprintf('%s is closed by its administrtor TEMPORARILY.', setting_item('blog_title'));
				$message = sprintf('Reason: %s', setting_item('offline_reason')?setting_item('offline_reason'):'n/a');
				
				echo <<<EOT
<html xmlns="http://www.w3.org/1999/xhtml" > <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>{$title}</title><style type="text/css">body{padding-right: 32px;margin-top: 40px;padding-left: 32px;font-size: 13px;background: #eee;padding-bottom: 32px;color: #000;padding-top: 32px;font-family:Verdana;}#main{border-right: #bbb 1px solid;border-top: #bbb 1px solid;background: #fff;padding-bottom: 32px;border-left: #bbb 1px solid;width: 550px;padding-top: 20px;border-bottom: #bbb 1px solid;text-align:left;padding-left:60px;padding-right:50px;}div#heading{padding-right: 0px;padding-left: 0px;font-weight: bold;font-size: 120%;padding-bottom: 15px;margin: 0px;color: #904;padding-top: 0px;font-family: arial;}h2{padding-right: 0px;padding-left: 0px;font-weight: bold;font-size: 105%;padding-bottom: 0px;margin: 0px 0px 8px;text-transform: uppercase;color: #999;padding-top: 0px;border-bottom: #ddd 1px solid;font-family: "trebuchet ms" , "" lucida grande "" , verdana, arial, sans-serif;}p{padding-right: 0px;padding-left: 0px;padding-bottom: 6px;margin: 0px;padding-top: 6px;}a:link{color: #002c99;font-size: 12px;}a:visited{color: #002c99;font-size: 12px;}a:hover{color: #cc0066;background-color: #f5f5f5;text-decoration: underline;font-size: 12px;}</style> </head> <body> <div style="width:100%;"><div align="center"> <div id="main"><div id="heading">{$heading}</div>{$message}</div></div> </div> </body></html>
EOT;
				exit();
			}
		}
	}
	
	/**
    * 检查PHP版本
    * 
    * @access public
    * @return void
    */	
	public function check_compatibility()
	{
		if (version_compare(PHP_VERSION, '5.0.0', '<')) 
		{
			die('Sorry, STBlog is for PHP5 and above ONLY.  The PHP version installed on your server is lower than that.  Time to upgrade?');
		}
	}
	
	/**
    * 清空缓存文件
    * 
    * @access public
    * @return void
    */	
	public function clear_file_cache()
	{
		$this->_CI->load->helper('file');
		
		$path = $this->_CI->config->item('cache_path');
		
		delete_files($path);
		
		@copy(APPPATH.'index.html', $this->_CI->config->item('cache_path').'/index.html');
	}

	
	/**
    * 清空数据库缓存文件
    * 
    * @access public
    * @return void
    */
	public function clear_db_cache()
	{
		$this->_CI->load->helper('file');
		
		delete_files(APPPATH . "dbcache" . DIRECTORY_SEPARATOR, TRUE);
		
		@copy(APPPATH . 'index.html', APPPATH . "dbcache/" . 'index.html');
	}
}

/* End of file Utiliy.php */
/* Location: ./application/libraries/Utiliy.php */