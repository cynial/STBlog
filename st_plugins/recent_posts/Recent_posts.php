<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: 最新日志Widget
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 显示博客最新日志
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Recent_posts
{
	private $_CI;

	public function __construct(&$plugin)
	{
		$plugin->register('Widget::Posts::Recent', $this, 'show_recent_posts');
		
		$this->_CI = &get_instance();
	}
	
	public function show_recent_posts($format)
	{
		/** 输出格式为空?*/
		if(empty($format)) return;
		
		/** 输出多少条? */
		$list_size = setting_item('posts_list_size');
		$list_size = ($list_size && is_numeric($list_size)) ? intval($list_size) : 10;
		
		$posts = $this->_CI->stcache->get('Widget::Posts::Recent');
		
		if(FALSE == $posts)
		{
			$posts = $posts = $this->_CI->db->select('slug, title')
				 ->from('posts')
        		 ->where('type', 'post')
        		 ->where('status', 'publish')
        		 ->order_by('created', 'DESC')
        		 ->limit($list_size)
        		 ->offset(0)
        		 ->get()
        		 ->result();
        
        	$this->_CI->stcache->set('Widget::Posts::Recent', $posts);	
		}
				
		if($posts)
		{
			foreach($posts as $post)
			{
				$wildcards = array('{permalink}', '{title}');
				
				$replaces = array(site_url('posts/'. $post->slug), $post->title);
				
				echo str_replace($wildcards, $replaces, $format) . "\r\n";
			}	
		
		}		
		
	}
}

/* End of file Recent_posts.php */
/* Location: ./application/st_plugins/Recent_posts.php */
