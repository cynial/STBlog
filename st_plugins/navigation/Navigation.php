<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: 导航拦Widget
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 根据创建的页面自动生成导航栏
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Navigation
{
	private $_CI;
	
	public function __construct(&$plugin)
	{
		$plugin->register('Widget::Navigation', $this, 'render');
		
		$this->_CI = &get_instance();
	}

	/**
	*
	*
	*
	*/
	public function render($format)
	{
		/** 输出格式为空?*/
		if(empty($format)) return;
		
		$pages = $this->_CI->stcache->get('Widget::Navigation');
		
		if(FALSE == $pages)
		{
			$pages = $this->_CI->db->select('*')
				 ->from('posts')
        		 ->where('type', 'page')
        		 ->where('status', 'publish')
        		 ->order_by('order', 'ASC')
        		 ->get()
        		 ->result();
        
        	$this->_CI->stcache->set('Widget::Navigation', $pages);	
		}
		
		if(!empty($pages))
		{
			foreach($pages as $page)
			{
				$permalink = site_url('pages/' .$page->slug);
			
				$wildcards = array('{permalink}', '{title}');
					
				$replaces = array($permalink, $page->title);
				
				echo str_replace($wildcards, $replaces, $format) . "\r\n";
			}
		}
	}
}

/* End of file Navigation.php */
/* Location: ./application/st_plugins/Navigation.php */
