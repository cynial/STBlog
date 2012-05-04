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
 * 内容操作Model
 *
 * @package		STBLOG
 * @subpackage	Models
 * @category	Models
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Posts_mdl extends CI_Model {

	const TBL_POSTS = 'posts';
	const TBL_METAS = 'metas';
	const TBL_RELATIONSHIPS = 'relationships';
	const TBL_COMMENTS = 'comments';

	/**
     * 内容类型 日志/附件/独立页面
     * 
     * @access private
     * @var array
     */
	private $_post_type = array('post', 'attachment', 'page');

	/**
     * 内容状态：发布/草稿/未归档/等待审核
     * 
     * @access private
     * @var array
     */
	private $_post_status = array('publish', 'draft', 'unattached', 'attached', 'waiting');
	
	/**
     * 内容的唯一栏：pid/slug
     * 
     * @access private
     * @var array
     */
	private $_post_unique_field = array('pid','slug');
	
	/**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
		
		log_message('debug', "STBLOG: Posts Model Class Initialized");
    }
	
	/**
     * 获取内容列表
     * 
     * @access public
	 * @param string  $type  			内容类型
	 * @param string  $status 			内容状态
	 * @param int 	  $author_id 		作者ID (optional)
	 * @param int 	  $limit 			条数	  (optional)
	 * @param int 	  $offset 			偏移量 (optional)
	 * @param int 	  $category_filter 	需要过滤的栏目ID (optional)
	 * @param int 	  $title_filter 	需要过滤的标题关键字 (optional)
	 * @param bool    $feed_filter		是否显示在feed里面 (optional)
     * @return array  内容列表信息
     */
	public function get_posts($type = 'post',$status = 'publish',$author_id = NULL,$limit = NULL,$offset = NULL,$category_filter = 0, $title_filter = '', $feed_filter = FALSE)
	{
		$this->db->select('posts.*, users.screenName');
		$this->db->join('users','users.uid = posts.authorId');
		
		//type
		if($type && in_array($type, $this->_post_type))
		{
			$this->db->where('posts.type', $type);
		}
		
		//status
		if($status && in_array($status,$this->_post_status))
		{
			$this->db->where('posts.status', $status);
		}
		
		//author
		if(!empty($author_id))
		{
			$this->db->where('posts.authorId', intval($author_id));
		}
		
		//category filter
		if(!empty($category_filter))
		{
			$this->db->join('relationships','posts.pid = relationships.pid','left');
			$this->db->where('relationships.mid', intval($category_filter));
		}
		
		//title filter
		if(!empty($title_filter))
		{
			$this->db->like('posts.title', $title_filter);
		}
		
		//feed filter
		if($feed_filter)
		{
			$this->db->where('allowFeed', 1);
		}
		
		$this->db->order_by('posts.created','DESC');
		
		//limit
		if($limit && is_numeric($limit))
		{
			$this->db->limit(intval($limit));
		}
		
		//offset
		if($offset && is_numeric($offset))
		{
			$this->db->offset(intval($offset));
		}
		
		return $this->db->get(self::TBL_POSTS);
	}
	

	/**
     * 获取内容条目最大ID
     * 
     * @access public
	 * @return int 
     */
	public function get_post_max_id()
	{
		$this->db->select_max('pid', 'pid');
		
		$query = $this->db->get(self::TBL_POSTS);
		$max = ($query->num_rows() > 0) ? $query->row()->pid : 0;
		$query->free_result();
		
		return $max;
	}
	
	/**
     * 根据唯一键获取单个内容信息
     * 
     * @access public
	 * @param  string $identity 内容标识栏位：{"pid"｜"slug"}
	 * @param  mixed  $value    标识栏位对应的值
     * @return array  内容信息
     */
	public function get_post_by_id($identity, $value)
	{
		if(!in_array($identity, $this->_post_unique_field))
		{
			return FALSE;
		}
		
		$this->db->select('posts.*,users.screenName');
		$this->db->join('users', 'users.uid = posts.authorId');
		$this->db->where($identity, $value);

		return $this->db->get(self::TBL_POSTS)->row();
	}
	
	/**
     * 根据元数据获取内容
     * 
     * @access public
	 * @param string $meta_slug 	元数据缩略名
	 * @param string $meta_type 	元数据类型：{"category"｜"tag"}
	 * @param string $post_type 	内容类型
	 * @param string $post_status 	内容状态	 
	 * @param string $post_status 	要筛选的栏位值 (optional)
	 * @param int    $limit 		条数 (optional)
	 * @param int    $offset 		偏移量 (optional)
	 * @param bool   $feed_filter	是否显示在feed里面 (optional)
     * @return array - 内容信息
     */	
	public function get_posts_by_meta($meta_slug, $meta_type = 'category', $post_type = 'post', $post_status = 'publish', $fields = 'posts.*', $limit = NULL, $offset = NULL, $feed_filter = FALSE)
	{
		$this->db->select($fields . ',users.screenName');
		$this->db->from('posts,metas,relationships');
		$this->db->join('users','users.uid = posts.authorId');
		$this->db->where('posts.pid = relationships.pid');
		$this->db->where('posts.type', $post_type);
		$this->db->where('posts.status', $post_status);
		$this->db->where('metas.mid = relationships.mid');
		$this->db->where('metas.type',$meta_type);
		$this->db->where('metas.slug',$meta_slug);
		$this->db->order_by('posts.created','DESC');
		
		if($feed_filter)
		{
			$this->db->where('allowFeed', 1);
		}
		
		if($limit && is_numeric($limit))
		{
			$this->db->limit(intval($limit));
		}
		
		if($offset && is_numeric($offset))
		{
			$this->db->offset(intval($limit));
		}
		
		return $this->db->get();
	}

	/**
     * 根据order值获得内容信息
     * 
     * @access public
	 * @param string $order
     * @return array - 内容信息
     */	
	public function get_posts_by_order($order)
	{
		$this->db->where('order', intval($order));
		
		return $this->db->get(self::TBL_POSTS);
	}
	
	/**
     * 日志归档：按日/按月/按年归档
     * 
     * @access public
	 * @param int optional $year 归档年
	 * @param int optional $month 归档月
	 * @param int optional $day 归档日
	 * @param int    $limit 条数
	 * @param int    $offset 偏移量	 
     * @return array - 内容信息
     */	
	public function get_posts_by_date($year = NULL, $month = NULL, $day = NULL, $limit = NULL, $offset = NULL)
	{
		//neither of the args are given, so exit from the func.
		if(empty($year) && empty($month) && empty($day)) exit();
		
		//archive by day
		if(!empty($year) && !empty($month) && !empty($day)) 
		{
			$from = mktime(0, 0, 0, $month, $day, $year);
            $to = mktime(23, 59, 59, $month, $day, $year);
		}
		//archive by month
		else if(!empty($year) && !empty($month)) 
		{
			$from = mktime(0, 0, 0, $month, 1, $year);
            $to = mktime(23, 59, 59, $month, date('t', $from), $year);
		}
		//archive by year
		else if(!empty($year)) 
		{
			$from = mktime(0, 0, 0, 1, 1, $year);
            $to = mktime(23, 59, 59, 12, 31, $year);
		}
		
		$this->db->select('posts.*,users.screenName');
		$this->db->join('users','users.uid = posts.authorId');
		$this->db->where('posts.created >=', $from);
		$this->db->where('posts.created <=', $to);
		$this->db->where('posts.status','publish');
		$this->db->where('posts.type','post');
		
		if($limit && is_numeric($limit))
		{
			$this->db->limit(intval($limit));
		}
		
		if($offset && is_numeric($offset))
		{
			$this->db->offset(intval($limit));
		}
		
		return $this->db->get(self::TBL_POSTS);
	}
	
	/**
     * 根据作者ID获取文章
     * 
     * @access public
	 * @param int 		$uid 
	 * @param string 	$type 
	 * @param string 	$status 
	 * @param int 		$limit
	 * @param int 		$offset
     * @return array - 内容信息
     */	
	public function get_posts_by_author($uid, $type = 'post', $status = 'publish', $limit = NULL, $offset = NULL)
	{
		$this->db->select('posts.* ,users.screenName');
		$this->db->join('users','users.uid = posts.authorId');
		
		//uid
		$this->db->where('posts.authorId', intval($uid));
		
		//type
		if($type && in_array($type, $this->_post_type))
		{
			$this->db->where('posts.type', $type);
		}
		
		//status
		if($status && in_array($status,$this->_post_status))
		{
			$this->db->where('posts.status', $status);
		}
		
		//limit
		if($limit && is_numeric($limit))
		{
			$this->db->limit($limit);
		}
		
		//offset
		if($offset && is_numeric($offset))
		{
			$this->db->offset($offset);
		}
		
		return $this->db->get(self::TBL_POSTS);
	}
	
	/**
     * 获取合法的slug名称
     * 
     * @access public
	 * @param string $slug slug name
	 * @param int $pid 内容id
     * @return string slug
     */
	public function get_slug_name($slug, $pid)
	{
		$result = $slug;
		$count = 1;
		
		while($this->db->select('pid')->where('slug',$result)->where('pid <>',$pid)->get(self::TBL_POSTS)->num_rows() > 0)
		{
			$result = $slug . '_' . $count;
			$count ++;
		}
		
		return $result;
	}
	
//----------------------CRUD-------------------------------------------------
	
	/**
     * 添加一个内容
     * 
     * @access public
	 * @param array $content_data  内容
     * @return mixed {post_id | FALSE} 
     */
	public function add_post($content_data)
	{
		$this->db->insert(self::TBL_POSTS, $content_data);
		
		return ($this->db->affected_rows() ==1) ? $this->db->insert_id() : FALSE;
	}

	/**
    * 修改一个内容
    * 
    * @access public
	* @param int $pid 内容ID
	* @param array   $data 内容数组
    * @return boolean 成功或失败
    */	
	public function update_post($pid,$data)
	{
		$this->db->where('pid', intval($pid));
		$this->db->update(self::TBL_POSTS, $data);
		
		return ($this->db->affected_rows() == 1)?TRUE:FALSE;
	}

	/**
    * 评论个数自减一
    * 
    * @access public
	* @param int $pid 内容ID
    * @return void
    */	
	public function cmts_num_minus($pid)
	{
		$this->db->query('UPDATE '.self::TBL_POSTS.' SET `commentsNum` = `commentsNum`-1 WHERE `pid`='. intval($pid) .'');
	}

	/**
    * 评论个数自曾一
    * 
    * @access public
	* @param int $pid 内容ID
    * @return void
    */	
	public function cmts_num_plus($pid)
	{
		$this->db->query('UPDATE '.self::TBL_POSTS.' SET `commentsNum` = `commentsNum`+1 WHERE `pid`='. intval($pid) .'');
	}
	
	/**
     * 删除一个内容
     * 
     * @access public
	 * @param int $pid 内容id
     * @return boolean 成功或失败
     */
	public function remove_post($pid)
	{
		$this->db->delete(self::TBL_POSTS, array('pid' => intval($pid)));
		
		return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
	}
	
    /**
	 * 更新评论数
	 * 
	 * @access public
	 * @param int $pid
	 * @return void
	 */
	public function refresh_comments_count($pid)
	{
		//calculation
		$num = $this->db->select(self::TBL_COMMENTS.'.cid')
					->from(self::TBL_COMMENTS)
        			->where(self::TBL_COMMENTS.'.status', 'approved')
        			->where(self::TBL_COMMENTS.'.pid', $pid)
        			->count_all_results();
		
		//update
		$this->update_post($pid, array('commentsNum' => $num));
	}
	
}

/* End of file posts_mdl.php */
/* Location: ./application/models/posts_mdl.php */
