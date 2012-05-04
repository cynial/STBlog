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
 * STBLOG Metas Model Class
 *
 * 元数据操作Model
 *
 * @package		STBLOG
 * @subpackage	Models
 * @category	Models
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Metas_mdl extends CI_Model {

	const TBL_METAS = 'metas';
	const TBL_RELATIONSHIPS = 'relationships';
	const TBL_POSTS = 'posts';

	/**
     * 内容类型：分类/标签
     * 
     * @access private
     * @var array
     */
	private $_type = array('category','tag');
	
	/**
     * 文章元数据
     * 
     * @access public
     * @var mixed
     */
	public $metas = NULL;
	
	/**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
		log_message('debug', "STBLOG: Metas Model Class Initialized");
    }

	/**
     * 根据post id获取元数据列表
     *
     *	本函数的目的是一次性读出文章的所有metas，然后通过$this->metas_mdl->metas['YOUR_KEY']读取对应的meta，比如category。
     *	
     * 
     * @access public
	 * @param int $pid 内容ID
	 * @param bool $return 是否返回模式
	 * @return array
     */
	public function get_metas($pid = 0, $return = FALSE)
	{
		//清空metas数组
		$this->metas = NULL;
		
		$metas = array();
		
		//读取DB
		if(!empty($pid))
		{
			$this->db->select(self::TBL_METAS.'.*,'.self::TBL_RELATIONSHIPS.'.pid');
			$this->db->join(self::TBL_RELATIONSHIPS,self::TBL_RELATIONSHIPS.'.mid = '.self::TBL_METAS.'.mid AND '.self::TBL_RELATIONSHIPS.'.pid='.intval($pid), 'INNER');
		}
		
		$query = $this->db->get(self::TBL_METAS);
		
		if ($query->num_rows() > 0)
        {
            $metas = $query->result_array();
        }
		
		$query->free_result();
		
		//如果是返回模式
		if($return)
		{
			return $metas;
		}
		
		//初始化一个metas数组
		foreach($this->_type as $type)
		{
			$this->metas[$type] = array();
		}
	
		if(!empty($metas))
		{
			//根据不同的metas类型自动push进对应的数组
			foreach($metas as $meta)
			{
				foreach($this->_type as $type)
				{
					if($type == $meta['type'])
					{
						array_push($this->metas[$type], $meta);
					}
				}
			}	
		}
	}

	/**
     * 获取所有metas
     * 
     * @access public
     * @param  strint $type 类型
     * @return object
     */
	public function list_metas($type = 'category')
	{
		if(in_array($type, $this->_type))
		{
			$this->db->where(self::TBL_METAS.'.type', $type);
		}
		
		return $this->db->get(self::TBL_METAS);
	}

	/**
     * 计算metas个数
     * 
     * @access public
     * @param  strint $type 类型
     * @return int
     */
	public function count_metas($type = 'category')
	{
		if(in_array($type, $this->_type))
		{
			$this->db->where(self::TBL_METAS.'.type', $type);
		}

		return $this->db->count_all_results(self::TBL_METAS);
	}

	/**
     *  获取元数据
     * 
     *  @access public
	 *	@param string $type 元数据类别：｛"category"|"tag"|"byID"｝
	 *	@param string $name 元数据名称
	 *	@return object － result object
     */
	public function get_meta($type = 'category', $name = '')
	{
		if(empty($name)) exit();
		
		if($type && in_array($type, $this->_type))
		{
			$this->db->where(self::TBL_METAS.'.type',$type);
			$this->db->where(self::TBL_METAS.'.name',$name);
		}
		
		if($type && strtoupper($type) == 'BYID')
		{
			$this->db->where(self::TBL_METAS.'.mid', intval($name));
		}
		
		return $this->db->get(self::TBL_METAS)->row();
	}

	/**
     * 根据缩略名获取meta信息
     * 
     * @access public
	 * @param array $meta_data  内容
     * @return object
     */
	public function get_meta_by_slug($slug)
	{
		$this->db->where(self::TBL_METAS.'.slug', $slug);
		
		return $this->db->get(self::TBL_METAS)->row();
	}

	/**
     * 检查meta是否存在
     * 
     * @access public
	 * @param string - $type 类型
	 * @param string - $key 栏位名
	 * @param string - $value 内容
	 * @param int    - $exclude_mid 要排除的mid
     * @return bool
     */
	public function check_exist($type = 'category', $key = 'name', $value = '', $exclude_mid = 0)
	{
		$this->db->select('mid')->from(self::TBL_METAS)->where($key, trim($value));
		
		if(!empty($exclude_mid) && is_numeric($exclude_mid))
		{
			$this->db->where('mid !=', $exclude_mid);	
		}
		
		if($type && in_array($type, $this->_type))
		{
			$this->db->where('type', $type);
		}
		
		$query = $this->db->get();
		
		$num = $query->num_rows();
		
		$query->free_result();
		
		return ($num > 0) ? TRUE : FALSE;	
	}
	 
	 /**
	 * 根据内容的指定类别和状态更新相关meta的计数信息
	 * 
	 * @access public
	 * @param int $mid meta id
	 * @param string $type 类别
	 * @param string $status 状态
	 * @return void
	 */
	public function refresh_count($mid, $type, $status = 'publish')
	{
		//calculation
		$num = $this->db->select(self::TBL_POSTS.'.pid')
					->from(self::TBL_POSTS)
					->join(self::TBL_RELATIONSHIPS, self::TBL_POSTS.'.pid = '.self::TBL_RELATIONSHIPS.'.pid')
        			->where(self::TBL_RELATIONSHIPS.'.mid', $mid)
        			->where(self::TBL_POSTS.'.type', $type)
        			->where(self::TBL_POSTS.'.status', $status)
        			->count_all_results();
		
		//update
		$this->update_meta($mid, array('count' => $num));
	}
	
    /**
     * 合并数据
     * 
     * @access public
     * @param int $mid 数据主键
     * @param string $type 数据类型
     * @param array $metas 需要合并的数据集
     * @return void
     */
	public function merge_meta($mid, $type, $metas = array())
	{
		$query = $this->db->select('pid')
        	 		  ->from(self::TBL_RELATIONSHIPS)
        	  		  ->where('mid', $mid)->get();

       	$posts = Common::array_flatten($query->result_array(), 'pid');
        	  		  
       	$query -> free_result();
       	
       	foreach($metas as $meta)
       	{
       		if($mid !== $meta)
       		{
       			$query = $this->db->select('pid')
        	 		  ->from(self::TBL_RELATIONSHIPS)
        	  		  ->where('mid', $meta)->get();
        	 	
        	 	//record posts previously categorized under this special meta
        	  	$exist_posts = Common::array_flatten($query->result_array(),'pid');
        	  	
        	  	$query->free_result();
        	  	
        	  	//delete this special meta
        		$this->db->delete(self::TBL_METAS,
						  array(
						  	'mid'	=>	$meta,
						  	'type'	=>	$type
						 ));
				
				//only get the diff posts that we need to operate on.
				$diff_posts = array_diff($exist_posts, $posts);
				
				//delete the relationship
				$this->remove_relationship('mid',$meta);
				
				//add new relationship
				foreach($diff_posts as $diff_post)
				{
					$this->add_relationship(array('mid'=> $mid,'pid'=> $diff_post));
				}
				
				unset($exist_posts);
       		}
       	}
       	
       	//get new count
       	$num = $this->db->select(self::TBL_RELATIONSHIPS.'.mid')
					->from(self::TBL_RELATIONSHIPS)
        			->where(self::TBL_RELATIONSHIPS.'.mid', $mid)
        			->count_all_results();
       	
       	//update new count
       	$this->update_meta($mid, array('count' => $num));
	}

    /**
     * meta个数自减一
     * 
     * @access public
     * @param int $mid meta id
     * @return void
     */
	public function meta_num_minus($mid)
	{
		$this->db->query('UPDATE '.self::TBL_METAS.' SET `count` = `count`-1 WHERE `mid`='.$mid.'');
	}

    /**
     * meta个数自增一
     * 
     * @access public
     * @param int $mid meta id
     * @return void
     */
	public function meta_num_plus($mid)
	{
		$this->db->query('UPDATE '.self::TBL_METAS.' SET `count` = `count`+1 WHERE `mid`='.$mid.'');
	}
	
    /**
     * 根据tag获取ID
     * 
     * @access public
     * @param  mixed $inputTags 标签名
     * @return mixed
     */
    public function scan_tags($inputTags)
    {
        $tags = is_array($inputTags) ? $inputTags : array($inputTags);
        $result = array();
        
        foreach ($tags as $tag) 
        {
            if (empty($tag)) 
            {
                continue;
            }
        
        	$row = $this->db->select('*')
        					->from(self::TBL_METAS)
        					->where('type','tag')
        					->where('name',$tag)
        					->limit(1)
        					->get()
        					->row();
            
            if ($row) 
            {
                $result[] = $row->mid;
            } 
            else 
            {
                $slug = Common::repair_slugName($tag);
                
                if ($slug) 
                {
                    $result[] = $this->add_meta(array(
			                        'name'  =>  $tag,
			                        'slug'  =>  $slug,
			                        'type'  =>  'tag',
			                        'count' =>  0,
			                        'order' =>  0,
			                    ));
                }
            }
        }
        
        return is_array($inputTags) ? $result : current($result);
    }
    
// -----------------------CRUD---------------------------------------------
	/**
     * 添加meta
     * 
     * @access public
	 * @param  array $meta_data  内容
     * @return boolean 成功与否
     */
	public function add_meta($meta_data)
	{
		$this->db->insert(self::TBL_METAS, $meta_data);
		
		return ($this->db->affected_rows() ==1) ? $this->db->insert_id() : FALSE;
	}

	/**
     * 添加元数据/内容关系
     * 
     * @access public
	 * @param  array $relation_data  内容
     * @return boolean 成功与否
     */
	public function add_relationship($relation_data)
	{
		$this->db->insert(self::TBL_RELATIONSHIPS, $relation_data);
		
		return ($this->db->affected_rows()==1) ? $this->db->insert_id() : FALSE;
	}
	
	/**
     * 删除关系
     * 
     * @access public
	 * @param  string   $column  唯一PK
	 * @param  int $value  值
     * @return boolean 成功与否
     */
	public function remove_relationship($column = 'pid', $value)
	{
		$this->db->delete(self::TBL_RELATIONSHIPS, array($column => intval($value))); 
	
		return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
	}
	
	/**
     * 删除关系
     * 
     * @access public
	 * @param  int   $pid  内容ID
	 * @param  int 	 $mid  meta ID
     * @return boolean 成功与否
     */
	public function remove_relationship_strict($pid, $mid)
	{
		$this->db->delete(self::TBL_RELATIONSHIPS,
						  array(
						  	'pid'=> intval($pid),
						  	'mid'=> intval($mid)
						 )); 
		
		return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
	}

	/**
    * 修改内容
    * 
    * @access public
	* @param int - $data 内容信息
    * @return boolean - success/failure
    */	
	public function update_meta($mid, $data)
	{
		$this->db->where('mid', intval($mid));
		$this->db->update(self::TBL_METAS, $data);
		
		return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
	}

	/**
     * 删除一个内容
     * 
     * @access public
	 * @param int - $mid 内容id
     * @return boolean - success/failure
     */
	public function remove_meta($mid)
	{
		$this->db->delete(self::TBL_METAS, array('mid' => intval($mid))); 
		
		return ($this->db->affected_rows() ==1) ? TRUE : FALSE;
	}
	
}
/* End of file Metas_mdl.php */
/* Location: ./application/models/Metas_mdl.php */
