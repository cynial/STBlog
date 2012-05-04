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
 * STBLOG Settings Model Class
 *
 * Settings
 *
 * @package		STBLOG
 * @subpackage	Models
 * @category	Models
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Settings_mdl extends CI_Model {

	const TBL_SETTINGS = 'settings';
	
		
	/**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
       parent::__construct();
	   
	   log_message('debug', "STBLOG: Settings Model Class Initialized");
    }
	
	/**
     * 更新一个配置
     * 
     * @access public
     * @return void
     */
	public function update_setting_item($name, $value)
	{
		$this->db->where('name', $name);
		$this->db->update(self::TBL_SETTINGS, array('value'=> $value));
		
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

}

/* End of file settings_mdl.php */
/* Location: ./application/models/settings_mdl.php */
