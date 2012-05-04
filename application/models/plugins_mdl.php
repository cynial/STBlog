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
 * STBLOG Plugins Class
 *
 * 本类包含插件操作Model
 *
 * @package		STBLOG
 * @subpackage	Models
 * @category	Models
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Plugins_mdl extends CI_Model {
	
	/**
     * 系统所在插件目录
     * 
     * @access private
     * @var string
     */
	public $plugins_dir = '';
	
	/**
     *	已经激活的插件 
     *
     * @access public
     * @var string
     */
	public $active_plugins = array();
	
	/**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
       parent::__construct();
       
       /** 初始化插件目录 */
       $this->plugins_dir = FCPATH. ST_PLUGINS_DIR . DIRECTORY_SEPARATOR ;
       
       /** 初始化已激活插件 */
       $this->active_plugins = $this->utility->get_active_plugins();
       
	   log_message('debug', "STBLOG: Plugins Model Class Initialized");
    }
    
	
	/**
	 * 激活一个插件
	 *
     * @access public
	 * @param array $plugin 需要激活的插件插件
	 * @return void
	 */
	public function active($plugin)
	{
		if (in_array($plugin, $this->active_plugins))
		{	
			return;
		} 
		else 
		{	
			$this->active_plugins[] = $plugin;
		}
		
		$active_plugins = serialize($this->active_plugins);
		
		$this->db->query("update settings set value='$active_plugins' where name='active_plugins'");
		
		$this->utility->clear_db_cache();
	}
	
	/**
	 * 禁用插件
	 *
     * @access public
	 * @param array $plugin 需要禁用的插件
	 * @return void
	 */
	public function deactive($plugin)
	{
		if (!in_array($plugin, $this->active_plugins))
		{
			return;
		} 
		else
		{
			$key = array_search($plugin, $this->active_plugins);
			
			unset($this->active_plugins[$key]);
		}
		
		$active_plugins = serialize($this->active_plugins);
		
		$this->db->query("update settings set value='$active_plugins' where name='active_plugins'");
		
		$this->utility->clear_db_cache();
	}

	/**
	 * 获取单个插件信息
	 *
     * @access public
	 * @param array $name 插件文件夹名
	 * @return array 插件信息
	 */
	public function get($plugin)
	{
		$plugin = strtolower($plugin);
		
		$path = $this->plugins_dir . $plugin;
				
		$file = $path . DIRECTORY_SEPARATOR . ucfirst($plugin) . '.php';
		
		$config = $path . DIRECTORY_SEPARATOR . ucfirst($plugin) . '.config.php';

		if(!is_file($path) && file_exists($file))
		{
			$fp = fopen($file, 'r' );
			
			/** 只取文件头部最多4K的数据进行分析 */
			$plugin_data = fread($fp, 4096);
			
			fclose($fp);
			
			preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $name );
			preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $uri );
			preg_match( '|Version:(.*)|i', $plugin_data, $version );
			preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
			preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
			preg_match( '|Author Email:(.*)$|mi', $plugin_data, $author_email );
			
			foreach( array('name', 'uri', 'version', 'description', 'author_name', 'author_email' ) as $field ) 
			{		
				${$field} = (!empty(${$field}))?trim(${$field}[1]):'';
			}
			
			return array(
						  'directory' => $plugin,
						  'name' => ucfirst($name), 
						  'plugin_uri' => $uri, 
						  'description' => $description, 
						  'author' => $author_name, 
						  'author_email' => $author_email, 
						  'version' => $version,
						  'configurable' => (file_exists($config))?TRUE:FALSE
						  );
		}
		
		return;
	}
	
	/**
     * 获取所有插件信息
     *
     *	递归获取所有存放于st_plugins中的插件信息：核心原理就是分析以.plugin.php结尾的插件文件的头部注释，
     *	提取其中的插件信息。(灵感来自wordpress，感谢WP，如果没有这个方案；那么可能就要多写一个xml或php文
     *	件存配制信息了)
     *	
     *	如果相让你写的插件被系统自动发现，需要至少遵循以下两个游戏规则：
     *	
     *	1.插件的实现文件必须以.plugin.php结尾，如一个fckeditor插件必须以fckeditor.plugin.php命名。值得
     *	  注意的是：本函数仅能识别"插件目录/插件/插件.plugin.php"目录下的插件
     *	2.在插件的实现文件的头部，必须以如下格式表明插件信息：
     *			/*
     *				Plugin Name: 插件名称
	 *				Plugin URI: 插件的项目主页
	 *				Description: 描述
	 *				Version: 版本号
	 *				Author: 作者
	 *				Author Email: 作者主页
     *
     *         
     * 
     * @access public
     * @return array - 所有插件信息
     */
	public function get_all_plugins_info()
	{
		$data = array();
				
		$this->load->helper('directory');
		
		$plugin_dirs = directory_map($this->plugins_dir, TRUE);
		
		if($plugin_dirs)
		{
			foreach($plugin_dirs as $plugin_dir)
			{
				$data[] = $this->get($plugin_dir);
			}
		}
		
		return $data;
	}
}

/* End of file plugins_mdl.php */
/* Location: ./application/models/plugins_mdl.php */
