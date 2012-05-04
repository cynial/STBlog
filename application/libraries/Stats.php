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
 * Stats Library Class
 *
 * 常用数据统计类
 *
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Stats
{

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
        /** 获取CI句柄 */
		$this->_CI = & get_instance();
		
		log_message('debug', "STBLOG: Statistics library Class Initialized");
    }

	/**
    * 分类数目
    * 
    * @access public
    * @return integer
    */
	public function count_categories()
	{
		return $this->_CI->metas_mdl->count_metas();
	}
	
	/**
    * 根据作者计算评论个数
    * 
    * @access public
    * @param  string $type 类型
    * @param  string $status 状态
    * @param  integer $uid  作者ID
    * @return integer
    */
	public function count_cmts_by_owner($type = 'comment', $status = 'approved', $uid = NULL)
	{
		return $this->_CI->comments_mdl->get_cmts_by_owner($type, $status, $uid, 10000, 0)->num_rows();
	}

	/**
    * 计算评论个数
    * 
    * @access public
    * @param  string $pid 文章ID
    * @param  string $type 类型
    * @param  string $status 状态
    * @return integer
    */
	public function count_cmts($pid, $type = 'comment', $status = 'approved')
	{
		return $this->_CI->comments_mdl->get_cmts($pid, $type, $status, 10000, 0)->num_rows();
	}

	/**
    * 计算文章个数
    * 
    * @access public
    * @param  string $type 类型
    * @param  string $status 状态
    * @param  integer $uid  作者ID
    * @return integer
    */
	public function count_posts($type = 'post', $status = 'publish', $uid = NULL)
	{
		return $this->_CI->posts_mdl->get_posts($type, $status, $uid, 10000,0)->num_rows();
	}
}

/* End of file Stats.php */
/* Location: ./application/libraries/Stats.php */