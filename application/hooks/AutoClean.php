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
 * STBLOG Page Cache Auto Clean Hook Class
 *
 * stblog自动清除页面缓存Hook
 *
 * @package		STBLOG
 * @subpackage	Hooks
 * @category	Hooks
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class AutoClean
{
	/**
    * CI句柄
    * 
    * @access private
    * @var object
    */
	private $_CI;
	
	private $_cache_file_limit;
	
	public function __construct()
	{
		$this->_CI = & get_instance();
		
		$limit = setting_item('cache_file_limit');
		$this->_cache_file_limit = ($limit && is_numeric($limit)) ? intval($limit) : 200;
	}

	/**
    * 根据文件缓存文件个数自动清除整页缓存
    * 
    * @access public
    * @return void
    */
	public function clean_cache()
	{
		$path = $this->_CI->config->item('cache_path');
		
		$filecount = count(glob($path . '*'));
		
		if($filecount > $this->_cache_file_limit)
		{
			$this->_CI->utility->clear_file_cache();
		}
	
	}
}

/* End of file AutoClean.php */
/* Location: ./application/hooks/AutoClean.php */