<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: 相关日志Widget
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 显示某篇日志的相关日志
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Related_posts
{
	private $_CI;

	public function __construct(&$plugin)
	{
		$plugin->register('Widget::Posts::Related', $this, 'show_related_posts');
		
		$this->_CI = &get_instance();
	}
	
	public function show_related_posts($post_id, $list_size, $format)
	{
		/** 输出格式为空?*/
		if(empty($format) || !is_numeric($post_id) || empty($list_size)) return;
		
		/** 参数初始化 */
		$post_id = intval($post_id);
		$list_size = ($list_size && is_numeric($list_size)) ? intval($list_size) : 10;
		$date_format = setting_item('post_date_format');
		$date_format = !empty($date_format)? $date_format : 'Y-m-d';

		$this->_CI->metas_mdl->get_metas($post_id);
		$tags = implode(',', Common::array_flatten($this->_CI->metas_mdl->metas['tag'], 'mid'));
		
		if(empty($tags))
		{
			echo "<p>没有相关文章</p>\r\n";
			
			return;
		}
		
		$posts = $this->_CI->db->select('posts.slug, posts.title, posts.created')
							   ->from('posts')
				 			   ->join('relationships', 'posts.pid = relationships.pid', 'INNER')
            				   ->where_in('relationships.mid', $tags)
            				   ->where('posts.pid <>', $post_id)
            				   ->where('posts.status', 'publish')
            				   ->where('posts.type', 'post')
        		 			   ->order_by('posts.created', 'DESC')
        		 			   ->limit($list_size)
        		 			   ->offset(0)
        		 			   ->get()
        		 			   ->result();
				
		if($posts)
		{
			foreach($posts as $post)
			{
				$wildcards = array('{permalink}', '{title}', '{date}');
				
				$replaces = array(site_url('posts/'. $post->slug), $post->title, date($date_format, $post->created));
				
				echo str_replace($wildcards, $replaces, $format) . "\r\n";
			}	
		
		}		
		
		return;
	}
}

/* End of file Related_posts.php */
/* Location: ./application/st_plugins/related_posts/Related_posts.php */