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
 * STBLOG Posts Model Class
 *
 * 评论操作Model
 *
 * @package		STBLOG
 * @subpackage	Models
 * @category	Models
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Comments_mdl extends CI_Model {

	const TBL_USERS = 'users';
	const TBL_COMMENTS = 'comments';

	/**
     * 类型：评论/引用
     * 
     * @access private
     * @var array
     */
	private $_type = array('comment', 'trackback');

	/**
     * 状态：通过/待审核/垃圾
     * 
     * @access private
     * @var array
     */
	private $_status = array('approved', 'waiting', 'spam');
	
	/**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
		
		log_message('debug', "STBLOG: Comments Model Class Initialized");
    }
    
    /**
     * 获取单个评论
     * 
     * @access public
     * @param  int 		$cid 评论id
     * @return object
     */
    public function get_cmt($cid)
    {
    	return $this->db->where('cid',intval($cid))->get(self::TBL_COMMENTS)->row();
    }
    

    /**
     * 获取评论列表，支持分页
     * 
     * @access public
     * @param  int    $pid 		post id
     * @param  string $status 	评论状态
     * @param  string $type		评论类型，包括comment和ping back
     * @param  int    $limit 	limit
     * @param  int    $offset 	offset
     * @param  string $order 	DESC|ASC
     * @param  string $filter 	内容过滤关键字
     * @return object
     */
    public function get_cmts($pid = 0, $type = 'comment', $status = 'approved', $limit = 0, $offset = 0, $order = 'DESC', $filter = '')
    {
    	if($pid && is_numeric($pid))
    	{
    		$this->db->where('pid', intval($pid));
    	}
    	
    	if($type && in_array($type, $this->_type))
		{
			$this->db->where(self::TBL_COMMENTS.'.type', $type);
		}
		
		if($status && in_array($status,$this->_status))
		{
			$this->db->where(self::TBL_COMMENTS.'.status', $status);
		}
		
		if(!empty($filter))
		{
			$this->db->like('text', $filter);
		}
    	
    	if($limit && is_numeric($limit))
    	{
    		$this->db->limit(intval($limit));
    	}
    	
    	if($offset && is_numeric($offset))
    	{
    		$this->db->offset(intval($offset));
    	}
    	
    	if($order && in_array($order, array('DESC', 'ASC')))
    	{
    		$this->db->order_by(self::TBL_COMMENTS.'.cid', $order);
    	}

		return $this->db->get(self::TBL_COMMENTS);
    }

    /**
     * 根据文章作者获取评论列表，支持分页
     * 
     * @access public
     * @param  string $type 	评论类型，包括comment和trackback
     * @param  string $status 	评论状态
     * @param  int 	  $owner_id 作者ID
     * @param  int    $limit 	limit
     * @param  int    $offset 	offset
     * @param  string $order 	DESC|ASC
     * @param  string $filter 	内容过滤关键字
     * @return object
     */
    public function get_cmts_by_owner($type = 'comment', $status = 'approved', $owner_id = NULL, $limit = 0, $offset = 0, $order = 'DESC', $filter = '')
    {
    	if($owner_id && is_numeric($owner_id))
    	{
    		$this->db->where('ownerid', intval($owner_id));	
    	}
    	
    	if($type && in_array($type,$this->_type))
		{
			$this->db->where(self::TBL_COMMENTS.'.type', $type);
		}
		
		if($status && in_array($status,$this->_status))
		{
			$this->db->where(self::TBL_COMMENTS.'.status', $status);
		}
		
		if(!empty($filter))
		{
			$this->db->like('text', $filter);
		}
    	
    	if($limit && is_numeric($limit))
    	{
    		$this->db->limit(intval($limit));
    	}
    	
    	if($offset && is_numeric($offset))
    	{
    		$this->db->offset(intval($offset));
    	}
    	
    	if($order && in_array($order, array('DESC', 'ASC')))
    	{
    		$this->db->order_by(self::TBL_COMMENTS.'.cid', $order);
    	}

		return $this->db->get(self::TBL_COMMENTS);
    
    }

    /**
     * 根据IP获取评论列表
     * 
     * @access public
     * @param  string $type 评论类型，包括comment和trackback
     * @param  string $status 评论状态
     * @param  string $ip IP
     * @return object
     */
    public function get_cmts_by_ip($type, $status, $ip)
    {
    	if($type && in_array($type, $this->_type))
		{
			$this->db->where(self::TBL_COMMENTS.'.type', $type);
		}
		
		if($status && in_array($status,$this->_status))
		{
			$this->db->where(self::TBL_COMMENTS.'.status', $status);
		}
		
		$this->db->where(self::TBL_COMMENTS. '.ip', $ip);
		
		return $this->db->get(self::TBL_COMMENTS);
    }

    /**
     * 检查URL是否已经在评论中存在
     * 
     * @access public
     * @param  int    $pid
     * @param  string $url url
     * @param  string $type 评论类型，包括comment和trackback
     * @return object
     */
	public function check_url_exists($pid, $url, $type)
	{
		if($pid && is_numeric($pid))
    	{
    		$this->db->where('pid', intval($pid));
    	}
    	
		if($type && in_array($type, $this->_type))
		{
			$this->db->where(self::TBL_COMMENTS.'.type', $type);
		}
		
		$this->db->where(self::TBL_COMMENTS.'.url', $url);
		
		return $this->db->get(self::TBL_COMMENTS);
	}

// -----------------------CRUD---------------------------------------------
    
	/**
     * 添加评论
     * 
     * @access public
     * @param  array $comment 评论信息
     * @return mixed
     */ 
    public function add_cmt($comment = array())
    {
    	$this->db->insert(self::TBL_COMMENTS, $comment);
    	
    	return ($this->db->affected_rows()==1) ? $this->db->insert_id() : FALSE;
    }
    
    /**
     * 更新评论
     * 
     * @access public
     * @param  int $cid 评论ID
     * @param  array $comment 评论信息
     * @return bool
     */
    public function update_cmt($cid, $comment = array())
    {
    	$this->db->where('cid', intval($cid));
		$this->db->update(self::TBL_COMMENTS, $comment);
    	
    	return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
    }

    /**
     * 删除评论
     * 
     * @access public
     * @param  array $comment 评论信息
     * @return bool
     */
    public function remove_cmt($condition = array())
    {
		$this->db->delete(self::TBL_COMMENTS, $condition);
		
		return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
    }
}

/* End of file comments_mdl.php */
/* Location: ./application/models/comments_mdl.php */
