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
 * 返回来路
 *
 * @access public
 * @param string $anchor 附加地址
 * @param string $default 默认来路
 * @return void
 */
if ( ! function_exists('go_back'))
{
	function go_back($suffix = NULL, $default = NULL)
	{
	    //获取来源
	    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	
	    //判断来源
	    if (!empty($referer)) 
	    {
	        // 来自Typecho
	        if (!empty($suffix)) 
	        {
	            $parts = parse_url($referer);
	            $myParts = parse_url($suffix);
	            
	            if (isset($myParts['fragment'])) 
	            {
	                $parts['fragment'] = $myParts['fragment'];
	            }
	            
	            if (isset($myParts['query'])) 
	            {
	                $args = array();
	                if (isset($parts['query'])) 
	                {
	                    parse_str($parts['query'], $args);
	                }
	            
	                parse_str($myParts['query'], $currentArgs);
	                $args = array_merge($args, $currentArgs);
	                $parts['query'] = http_build_query($args);
	            }
	            
	            $referer = build_url($parts);
	        }
	        
	        redirect($referer);
	    } 
	    else if (!empty($default)) 
	    {
	        redirect($default);
	    }
	}
}

/**
 * 根据parse_url的结果重新组合url
 * 
 * @access public
 * @param array $params 解析后的参数
 * @return string
 */
if ( ! function_exists('build_url'))
{
	function build_url($params)
	{
	    return (isset($params['scheme']) ? $params['scheme'] . '://' : NULL)
	    . (isset($params['user']) ? $params['user'] . (isset($params['pass']) ? ':' . $params['pass'] : NULL) . '@' : NULL)
	    . (isset($params['host']) ? $params['host'] : NULL)
	    . (isset($params['port']) ? ':' . $params['port'] : NULL)
	    . (isset($params['path']) ? $params['path'] : NULL)
	    . (isset($params['query']) ? '?' . $params['query'] : NULL)
	    . (isset($params['fragment']) ? '#' . $params['fragment'] : NULL);
	}
}


/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */