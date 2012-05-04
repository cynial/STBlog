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
 * STBlog常用公共类库
 *	
 *	部分函数方法来自Typecho(版权归typecho.org所有)
 *
 * @package		STBLOG
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Common
{
	/** 默认不解析的标签列表 */
    const LOCKED_HTML_TAG = 'code|pre|script';
    
    /** 需要去除内部换行的标签 */
    const ESCAPE_HTML_TAG = 'div|blockquote|object|pre|table|fieldset|tr|th|td|li|ol|ul|h[1-6]';
    
    /** 元素标签 */
    const ELEMENT_HTML_TAG = 'div|blockquote|pre|td|li';
    
    /** 布局标签 */
    const GRID_HTML_TAG = 'div|blockquote|pre|code|script|table|ol|ul';
    
    /** 独立段落标签 */
    const PARAGRAPH_HTML_TAG = 'div|blockquote|pre|code|script|table|fieldset|ol|ul|h[1-6]';

	/**
     * 锁定的代码块
     *
     * @access private
     * @var array
     */
    private static $_lockedBlocks = array('<p></p>' => '');
    
    /**
     * 锁定标签回调函数
     * 
     * @access private
     * @param array $matches 匹配的值
     * @return string
     */
    public static function __lock_html(array $matches)
    {
        $guid = '<code>' . uniqid(time()) . '</code>';
        self::$_lockedBlocks[$guid] = $matches[0];
        
        return $guid;
    }
    
    /**
     * 根据count数目来输出字符
     * <code>
     * echo Common::split_by_count(20, 10, 20, 30, 40, 50);
     * </code>
     * 
     * @access public
     * @return string
     */
    public static function split_by_count($count)
    {
        $sizes = func_get_args();
        array_shift($sizes);
        
        foreach ($sizes as $size) 
        {
            if ($count < $size) 
            {
                return $size;
            }
        }
        
        return 0;
    }
    
     /**
     * 按分割数输出字符串
     * 
     * @access public
     * @param string $param 需要输出的值
     * @return integer
     */
    public function split($count)
    {
        $args = func_get_args();
        array_unshift($args, $count);
        
        return call_user_func_array(array('Common', 'split_by_count'), $args);
    }
    
    /**
     * 自闭合html修复函数
     * 使用方法:
     * <code>
     * $input = '这是一段被截断的html文本<a href="#"';
     * echo Common::fixHtml($input);
     * //output: 这是一段被截断的html文本
     * </code>
     *
     * @access public
     * @param string $string 需要修复处理的字符串
     * @return string
     */
    public static function fix_html($string)
    {
        //关闭自闭合标签
        $startPos = strrpos($string, "<");
        
        if (false == $startPos) 
        {
            return $string;
        }
        
        $trimString = substr($string, $startPos);

        if (false === strpos($trimString, ">")) 
        {
            $string = substr($string, 0, $startPos);
        }

        //非自闭合html标签列表
        preg_match_all("/<([_0-9a-zA-Z-\:]+)\s*([^>]*)>/is", $string, $startTags);
        preg_match_all("/<\/([_0-9a-zA-Z-\:]+)>/is", $string, $closeTags);

        if (!empty($startTags[1]) && is_array($startTags[1])) 
        {
            krsort($startTags[1]);
            $closeTagsIsArray = is_array($closeTags[1]);
            foreach ($startTags[1] as $key => $tag) 
            {
                $attrLength = strlen($startTags[2][$key]);
                if ($attrLength > 0 && "/" == trim($startTags[2][$key][$attrLength - 1])) 
                {
                    continue;
                }
                if (!empty($closeTags[1]) && $closeTagsIsArray) 
                {
                    if (false !== ($index = array_search($tag, $closeTags[1]))) 
                    {
                        unset($closeTags[1][$index]);
                        continue;
                    }
                }
                $string .= "</{$tag}>";
            }
        }

        return preg_replace("/\<br\s*\/\>\s*\<\/p\>/is", '</p>', $string);
    }
    
    /**
     * 去掉字符串中的html标签
     * 使用方法:
     * <code>
     * $input = '<a href="http://test/test.php" title="example">hello</a>';
     * $output = Common::stripTags($input, <a href="">);
     * echo $output;
     * //display: '<a href="http://test/test.php">hello</a>'
     * </code>
     *
     * @access public
     * @param string $string 需要处理的字符串
     * @param string $allowableTags 需要忽略的html标签
     * @return string
     */
    public static function stripTags($string, $allowableTags = NULL)
    {
        if (!empty($allowableTags) && preg_match_all("/\<([a-z]+)([^>]*)\>/is", $allowableTags, $tags)) 
        {
            
            if (in_array('code', $tags[1])) 
            {
                $string = preg_replace_callback("/<(code)[^>]*>.*?<\/\\1>/is", array('Common', '__lock_html'), $string);
            }
        
            $normalizeTags = '<' . implode('><', $tags[1]) . '>';
            $string = strip_tags($string, $normalizeTags);
            $attributes = array_map('trim', $tags[2]);
            
            $allowableAttributes = array();
            foreach ($attributes as $key => $val) 
            {
                $allowableAttributes[$tags[1][$key]] = array();
                if (preg_match_all("/([a-z]+)\s*\=/is", $val, $vals)) 
                {
                    foreach ($vals[1] as $attribute) 
                    {
                        $allowableAttributes[$tags[1][$key]][] = $attribute;
                    }
                }
            }
            
            foreach ($tags[1] as $key => $val) 
            {
                $match = "/\<{$val}(\s*[a-z]+\s*\=\s*[\"'][^\"']*[\"'])*\s*\>/is";
                
                if (preg_match_all($match, $string, $out)) 
                {
                    foreach ($out[0] as $startTag) 
                    {
                        if (preg_match_all("/([a-z]+)\s*\=\s*[\"'][^\"']*[\"']/is", $startTag, $attributesMatch)) 
                        {
                            $replace = $startTag;
                            foreach ($attributesMatch[1] as $attribute) 
                            {
                                if (!in_array($attribute, $allowableAttributes[$val])) 
                                {
                                    $startTag = preg_replace("/\s*{$attribute}\s*=\s*[\"'][^\"']*[\"']/is", '', $startTag);
                                }
                            }
                            
                            $string = str_replace($replace, $startTag, $string);
                        }
                    }
                }
            }
            
            return str_replace(array_keys(self::$_lockedBlocks), array_values(self::$_lockedBlocks), $string);
        } 
        else 
        {
            return strip_tags($string);
        }
    }
    
    /**
     * 过滤用于搜索的字符串
     * 
     * @access public
     * @param string $query 搜索字符串
     * @return string
     */
    public static function filter_search($query)
    {
        return str_replace(array('%', '?', '*', '/', '{', '}'), '', $query);
    }

    /**
     * 宽字符串截字函数
     *
     * @access public
     * @param string $str 需要截取的字符串
     * @param integer $start 开始截取的位置
     * @param integer $length 需要截取的长度
     * @param string $trim 截取后的截断标示符
     * @param string $charset 字符串编码
     * @return string
     */
    public static function subStr($str, $start, $length, $trim = "...", $charset = 'UTF-8')
    {
        if (function_exists('mb_get_info')) 
        {
            $iLength = mb_strlen($str, $charset);
            $str = mb_substr($str, $start, $length, $charset);
            
            return ($length < $iLength - $start) ? $str . $trim : $str;
        } 
        else 
        {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            $str = join("", array_slice($info[0], $start, $length));
            
            return ($length < (sizeof($info[0]) - $start)) ? $str . $trim : $str;
        }
    }
    
    /**
     * 获取宽字符串长度函数
     *
     * @access public
     * @param string $str 需要获取长度的字符串
     * @return integer
     */
    public static function strLen($str, $charset = 'UTF-8')
    {
        if (function_exists('mb_get_info')) 
        {
            return mb_strlen($str, $charset);
        } 
        else 
        {
            preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
            
            return sizeof($info[0]);
        }
    }

	/**
	* 修整缩略名
	*
	* @access public
	* @param string $str 需要生成缩略名的字符串
	* @param string $default 默认的缩略名
	* @param integer $maxLength 缩略名最大长度
	* @param string $charset 字符编码
	* @return string
	*/
	public static function repair_slugName($str, $default = NULL, $maxLength = 200,$charset = 'UTF-8')
	{
	    $str = str_replace(array("'", ":", "\\", "/"), "", $str);
	    $str = str_replace(array("+", ",", " ", ".", "?", "=", "&", "!", "<", ">", "(", ")", "[", "]", "{", "}"), "_", $str);
	    $str = trim($str, '_');
	    $str = empty($str) ? $default : $str;
	    
	    return function_exists('mb_get_info') ? mb_strimwidth($str, 0, 128, '', $charset) : substr($str, $maxLength);
	}
	
    /**
     * 去掉html中的分段
     * 
     * @access public
     * @param string $html 输入串
     * @return string
     */
    public static function remove_paragraph($html)
    {
        return trim(preg_replace(
        array("/\s*<p>(.*?)<\/p>\s*/is", "/\s*<br\s*\/>\s*/is",
        "/\s*<(" . self::PARAGRAPH_HTML_TAG . ")([^>]*)>/is", "/<\/(" . self::PARAGRAPH_HTML_TAG . ")>\s*/is", "/\s*\[\-\-break\-\-\]\s*/is"),
        array("\n\\1\n", "\n", "\n\n<\\1\\2>", "</\\1>\n\n", "\n\n[--break--]\n\n"), 
        $html));
    }
    
    /**
     * 美化格式
     * 
     * @access public
     * @param string $html 输入串
     * @return string
     */
    public static function beautify_format($html)
    {
        /** 锁定标签 */
        $html = preg_replace_callback("/<(" . self::LOCKED_HTML_TAG . ")[^>]*>.*?<\/\\1>/is", array('Common', '__lock_html'), $html);
    
        $html = preg_replace("/\s*<(" . self::ELEMENT_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>\s*/ise",
        "str_replace('\\\"', '\"', '
<\\1\\2>' . trim('\\3') . '</\\1>')", $html);
        
        $html = preg_replace("/<(p|" . self::PARAGRAPH_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>/ise", 
        "str_replace('\\\"', '\"', '

<\\1\\2>' . trim('\\3') . '</\\1>

')", $html);

        $tags = implode('|', array_diff(explode('|', self::GRID_HTML_TAG), explode('|', self::LOCKED_HTML_TAG)));
        $html = preg_replace("/<(" . $tags . ")([^>]*)>(.*?)<\/\\1>/ise", 
        "str_replace('\\\"', '\"', '<\\1\\2>
' . trim('\\3') . '
</\\1>')", $html);

        $html = preg_replace("/\r*\n\r*/", "\n", $html);
        $html = preg_replace("/\n{2,}/", "\n\n", $html);
        
        return trim(str_replace(array_keys(self::$_lockedBlocks), array_values(self::$_lockedBlocks), $html));
    }
    
    /**
     * 文本分段函数
     *
     * @param string $string 需要分段的字符串
     * @param boolean $paragraph 是否分段
     * @return string
     */
    public static function cut_paragraph($string, $paragraph = true)
    {
        /** 锁定自闭合标签 */
        $string = trim($string);
        
        /** 返回空字符串 */
        if (empty($string)) 
        {
            return '';
        }
        
        /** 锁定标签 */
        $string = preg_replace_callback("/<(" . self::LOCKED_HTML_TAG . ")[^>]*>.*?<\/\\1>/is", array('Common', '__lock_html'), $string);

        $string = preg_replace("/\s*<(" . self::ELEMENT_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>\s*/ise",
        "str_replace('\\\"', '\"', '<\\1\\2>' . Common::cut_paragraph(trim('\\3'), false) . '</\\1>')", $string);
        $string = preg_replace("/<(" . self::ESCAPE_HTML_TAG . '|' . self::LOCKED_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>/ise",
        "str_replace('\\\"', '\"', '<\\1\\2>' . str_replace(array(\"\r\", \"\n\"), '', '\\3') . '</\\1>')", $string);
        $string = preg_replace("/<(" . self::GRID_HTML_TAG . ")([^>]*)>(.*?)<\/\\1>/is", "\n\n<\\1\\2>\\3</\\1>\n\n", $string);
        
        /** fix issue 197 */
        $string = preg_replace("/\s*<p ([^>]*)>(.*?)<\/p>\s*/is", "\n\n<p \\1>\\2</p>\n\n", $string);

        /** 区分段落 */
        $string = preg_replace("/\r*\n\r*/", "\n", $string);
        
        if ($paragraph || false !== strpos($string, "\n\n")) 
        {
            $string = '<p>' . preg_replace("/\n{2,}/", "</p><p>", $string) . '</p>';
        }
        
        $string = str_replace("\n", '<br />', $string);
        
        /** 去掉不需要的 */
        $string = preg_replace("/<p><(" . self::ESCAPE_HTML_TAG . '|p|' . self::LOCKED_HTML_TAG
        . ")([^>]*)>(.*?)<\/\\1><\/p>/is", "<\\1\\2>\\3</\\1>", $string);
        
        return str_replace(array_keys(self::$_lockedBlocks), array_values(self::$_lockedBlocks), $string);
    }
    
	/**
	* Gravatar
	*
	* 从Gravatar官方网站获得gravatar头像
	*
	* @access  public
	* @param   string
	* @param   string
	* @param   integer
	* @param   string
	* @return  string
	*/
	public static function gravatar($email, $rating = 'X', $size = '80', $default = 'http://gravatar.com/avatar.php') 
	{
	    
	    /** 对email进行hash编码 */
	 	$email = md5($email);
	
	    /** 返回生成的url */
	    return "http://gravatar.com/avatar.php?gravatar_id="
	        .$email."&amp;rating="
	        .$rating."&amp;size="
	        .$size."&amp;default="
	        .$default;
	}

	/**
	* 词义化时间
	* 
	* @access public
	* @param string $from 起始时间
	* @param string $now 终止时间
	* @return string
	*/
	public static function dateWord($from, $now)
	{
		//fix issue 3#6 by saturn, solution by zycbob
		
		/** 如果不是同一年 */
        if (idate('Y', $now) != idate('Y', $from)) 
        {
            return date('Y年m月d日', $from);
        }
		
		/** 以下操作同一年的日期 */
		$seconds = $now - $from;
        $days = idate('z', $now) - idate('z', $from);
        
        /** 如果是同一天 */
        if ($days == 0) 
        {
        	/** 如果是一小时内 */
            if ($seconds < 3600) 
            {
            	/** 如果是一分钟内 */
                if ($seconds < 60)
                {
                    if (3 > $seconds) 
                    {
                        return '刚刚';
                    } 
                    else 
                    {
                        return sprintf('%d秒前', $seconds);
                    }
                }

                return sprintf('%d分钟前', intval($seconds / 60));
            }

            return sprintf('%d小时前', idate('H', $now) - idate('H', $from));
        }

		/** 如果是昨天 */
        if ($days == 1) 
        {
            return sprintf('昨天 %s', date('H:i', $from));
        }
        
        /** 如果是前天 */
        if ($days == 2) 
        {
        	return sprintf('前天 %s', date('H:i', $from));
        }

        /** 如果是7天内 */
        if ($days < 7) 
        {
            return sprintf('%d天前', $days);
        }

        /** 超过一周 */
        return date('n月j日', $from);
	}
	
	/**
     * 格式化metas输出
     * 
     * @access public
	 * @param array - $metas metas内容数组
	 * @param string - $split 分割符
	 * @param boolean - $link 是否输出连接
     * @return string - 格式化输出
     */
	public static function format_metas($metas = array(), $split = ',', $link = true)
    {
    
    	$format = '';
    	
        if ($metas) 
        {
            $result = array();
            
            foreach ($metas as $meta) 
            {
                $result[] = $link ? '<a href="' . site_url($meta['type'].'/'.$meta['slug']) . '">'
                . $meta['name'] . '</a>' : $meta['name'];
            }

            $format = implode($split, $result);
        }
        
        return $format;
    }

    /**
     * 对字符串进行hash加密
     * 
     * @access public
     * @param string $string 需要hash的字符串
     * @param string $salt 扰码
     * @return string
     */
    public static function do_hash($string, $salt = NULL)
    {
		if(null === $salt)
		{
		    $salt = substr(md5(uniqid(rand(), true)), 0, ST_SALT_LENGTH);
		}
		else
		{
		    $salt = substr($salt, 0, ST_SALT_LENGTH);
		}

    	return $salt . sha1($salt . $string);
    }
    
    /**
     * 判断hash值是否相等
     * 
     * @access public
     * @param string $source 源字符串
     * @param string $target 目标字符串
     * @return boolean
     */
    public static function hash_Validate($source, $target)
    {
        return (self::do_hash($source, $target) == $target);
    }

    /**
     * 抽取多维数组的某个元素,组成一个新数组,使这个数组变成一个扁平数组
     * 使用方法:
     * <code>
     * <?php
     * $fruit = array(array('apple' => 2, 'banana' => 3), array('apple' => 10, 'banana' => 12));
     * $banana = Common::arrayFlatten($fruit, 'banana');
     * print_r($banana);
     * //outputs: array(0 => 3, 1 => 12);
     * ?>
     * </code>
     *
     * @access public
     * @param array $value 被处理的数组
     * @param string $key 需要抽取的键值
     * @return array
     */
    public static function array_flatten($value = array(), $key)
    {
        $result = array();

        if($value) 
        {
            foreach ($value as $inval) 
            {
                if(is_array($inval) && isset($inval[$key])) 
                {
                    $result[] = $inval[$key];
                } 
                else 
                {
                    break;
                }
            }
        }

        return $result;
    }
    
    /**
     * 寻找匹配的mime图标
     * 
     * @access public
     * @param string $mime mime类型
     * @return string
     */
    public static function mimeIconType($mime)
    {
        $parts = explode('/', $mime);
        
        if(count($parts) < 2) 
        {
            return 'unknown';
        }
        
        list($type, $stream) = $parts;
        
        if(in_array($type, array('image', 'video', 'audio', 'text', 'application'))) 
        {
            switch (true) 
            {
                case in_array($stream, array('msword', 'msaccess', 'ms-powerpoint', 'ms-powerpoint')):
                case 0 === strpos($stream, 'vnd.'):
                    return 'office';
                case false !== strpos($stream, 'html') || false !== strpos($stream, 'xml') || false !== strpos($stream, 'wml'):
                    return 'html';
                case false !== strpos($stream, 'compressed') || false !== strpos($stream, 'zip') || 
                in_array($stream, array('application/x-gtar', 'application/x-tar')):
                    return 'archive';
                case 'text' == $type && 0 === strpos($stream, 'x-'):
                    return 'script';
                default:
                    return $type;
            }
        } 
        else 
        {
            return 'unknown';
        }
    }

    /**
     * 根据分割符的位置获取摘要
     * 
     * @access public
     * @param string $string 输入串
     * @return string
     */
	public static function get_excerpt($string)
	{
		/** 检查是否存在分割符标记 */
        list($excerpt) = explode(ST_CONTENT_BREAK, $string);
        
        $excerpt = (empty($excerpt))?$string:$excerpt;
  	
  		$CI = &get_instance();
  		
        /** 如果没有安装任何编辑器插件，则需程序自动分段 */
        if(!$CI->plugin->check_hook_exist(ST_CORE_HOOK_EDITOR))
        {
        	$excerpt = self::remove_paragraph($excerpt);
        	$excerpt = self::cut_paragraph($excerpt);	
        }
        
        return self::fix_html($excerpt);
	}

    /**
     * 获取美化后的内容
     * 
     * @access public
     * @param string $string 输入串
     * @return string
     */
	public static function get_content($string)
	{
        if(empty($string)) return;
  		
  		if(self::has_break($string))
  		{
  			$string = self::remove_break($string);
  		}
  		  	
  		$CI = &get_instance();
  		
        /** 如果没有安装任何编辑器插件，则需程序自动分段 */
        if(!$CI->plugin->check_hook_exist(ST_CORE_HOOK_EDITOR))
        {
        	$string = self::remove_paragraph($string);
        	$string = self::cut_paragraph($string);	
        }
        
        return self::fix_html($string);
	}

    /**
     * 去除内容中的分割符标记
     * 
     * @access public
     * @param string $content 输入串
     * @return string
     */
	public static function remove_break($content)
	{	
		$content = str_replace(ST_CONTENT_BREAK, '', $content);
		
		return $content;
	}

    /**
     * 是否存在分割符标记
     * 
     * @access public
     * @param string $content 输入串
     * @return bool
     */
	public static function has_break($content)
	{
		if(strpos($content, ST_CONTENT_BREAK) !== FALSE)
		{
			return TRUE;
		}
		
		return FALSE;
	}

    /**
     * 是否自动关闭评论功能
     * 
     * @access public
     * @param string $content 输入串
     * @return bool
     */
	public static function auto_closed($created, $now)
	{
		$lifetime = intval(setting_item('comments_auto_close'));
		
		if(0 == $lifetime)
		{
			return FALSE;
		}
		
		$created = intval($created);
		$lifetime = intval($lifetime);
		
		if($created + $lifetime > $now)
		{
			return FALSE;
		}
		
		return TRUE;
	}

    /**
     * 输出头部feed meta信息
     * 
     * @access public
     * @param string $type 类型
     * @param mixed $slug slug
     * @param string $alt_title 
     * @return string
     */
	public static function render_feed_meta($type = 'default', $slug = NULL , $alt_title = '')
	{	
		if(empty($type) || !in_array($type, array('default', 'post', 'category', 'tag')))
		{
			return;
		}
		
		/** 初始化默认值 */
		$feed_rss_url = site_url('feed');
		$feed_atom_url = site_url('feed/atom');
		$alt_title = empty($alt_title) ? setting_item('blog_title') : htmlspecialchars($alt_title);
		
		$parsed_feed = <<<EOT
<link rel="alternate" type="application/rss+xml" href="{$feed_rss_url}" title="订阅 {$alt_title} 所有文章" />\r\n
<link rel="alternate" type="application/rss+xml" href="{$feed_rss_url}/comments" title="订阅 {$alt_title} 所有评论" />\r\n
EOT;

		if('default' === $type)
		{
			return $parsed_feed;
		}
		else
		{
			$title = '订阅';
			
			switch($type)
			{
				case 'post':
					$title .= $alt_title . '下的评论';
					break;
				case 'category':
					$title .= $alt_title . '分类下的文章';
					break;
				case 'tag':
					$title .= $alt_title . '标签下的文章';
					break;
			}
			
			return <<<EOT
<link rel="alternate" type="application/rss+xml" href="{$feed_rss_url}/{$type}/{$slug}" title="{$title}" />\r\n
EOT;

		}
	}

}
//END Common

// ------------------------------------------------------------------------

/**
* 获取用户配置
*
* @access	public
* @return	array
*/
function &get_settings()
{
	static $user_settings;
	
	if(!isset($user_settings))
	{
		$CI = &get_instance();
		
		$CI->load->library('stcache');
		
		$settings = $CI->stcache->get('settings');
		
		if(FALSE == $settings)
		{
			$query = $CI->db->get('settings');
		
			foreach($query->result() as $row)
			{
				$settings[$row->name] = $row->value;
			}
	
			$query->free_result();	
			
			$CI->stcache->set('settings', $settings);
		}
		
		$user_settings[0] = &$settings;
	}
	
	return $user_settings[0];
}

/**
* 获取一个选项
*
* @access	public
* @return	mixed
*/
function setting_item($item)
{
	static $setting_item = array();

	if (!isset($setting_item[$item]))
	{
		$settings = &get_settings();

		if (!isset($settings[$item]))
		{
			return FALSE;
		}
		
		$setting_item[$item] = $settings[$item];
	}

	return $setting_item[$item];
}

/* End of file Common.php */
/* Location: ./application/libraries/Common.php */
