<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: 分类列表Widget
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 显示博客的分类列表
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Categories
{
	private $_CI;

	public function __construct(&$plugin)
	{
		$plugin->register('Widget::Categories', $this, 'show_categories');
		
		$this->_CI = &get_instance();
	}
	
	public function show_categories($format)
	{
		/** 输出格式为空?*/
		if(empty($format)) return;
		
		$categories = $this->_CI->stcache->get('Widget::Categories');
		
		if(FALSE == $categories)
		{
			$categories = $this->_CI->metas_mdl->list_metas()->result();
		
        
        	$this->_CI->stcache->set('Widget::Categories', $categories);	
		}
					
		if($categories)
		{
			foreach($categories as $category)
			{				
				$wildcards = array(
								'{permalink}', 
								'{description}', 
								'{title}', 
								'{count}'
								);
				
				$replaces = array(
								site_url('category/'. $category->slug), 
								$category->description, 
								$category->name, 
								$category->count
								);
				
				echo str_replace($wildcards, $replaces, $format) . "\r\n";
			}	
		
		}		
		
	}
}

/* End of file Categories.php */
/* Location: ./application/st_plugins/Categories.php */