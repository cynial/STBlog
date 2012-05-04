<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: 日志归档列表Widget
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 显示日志按月归档列表
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

class Archive
{
	private $_CI;

	public function __construct(&$plugin)
	{
		$plugin->register('Widget::Posts::Archive', $this, 'show');
		
		$this->_CI = &get_instance();
	}

	/**
	*
	*
	*
	*/
	public function show($format, $type = 'month', $date_format = 'Y年m月')
	{
		
		/** 输出格式为空?*/
		if(empty($format)) return;
		
		$posts = $this->_CI->stcache->get('Widget::Posts::Archive');
		
		if(FALSE == $posts)
		{
			$posts = $this->_CI->db->select('created')
				 ->from('posts')
        		 ->where('type', 'post')
        		 ->where('status', 'publish')
        		 ->order_by('created', 'DESC')
        		 ->get()
        		 ->result();
        
        	$this->_CI->stcache->set('Widget::Posts::Archive', $posts);	
		}
	
		$data = array();
		
		if($posts)
		{
			foreach($posts as $post)
			{
				$timestamp = $post->created;
	            $date = date($date_format, $timestamp);
	
	            if(isset($data[$date])) 
	            {
	                $data[$date]['count'] ++;
	            } 
	            else 
	            {
	                $data[$date]['year'] = date('Y', $timestamp);
	                $data[$date]['month'] = date('m', $timestamp);
	                $data[$date]['day'] = date('d', $timestamp);
	                $data[$date]['date'] = $date;
	                $data[$date]['count'] = 1;
	           	}
			}
		}
		
		foreach($data as $key => $val)
		{
			$permalink = site_url('archives') . '/' . $val['year'] . '/' . $val['month'];
			
			$wildcards = array(
								'{permalink}',  
								'{title}', 
								'{count}'
								);
				
			$replaces = array(
							$permalink, 
							$val['date'], 
							$val['count']
							);
			
			echo str_replace($wildcards, $replaces, $format) . "\r\n";
		}
	}
}

/* End of file Archive.php */
/* Location: ./application/st_plugins/Archive.php */