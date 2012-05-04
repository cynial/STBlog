<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *	Plugin Name: Antispam: SPAM防治插件(Akismet)
 *	Plugin URI: http://www.cnsaturn.com/
 *	Description: 使用Wordpress提供的Akismet来过滤垃圾留言和引用
 *	Version: 0.1
 *	Author: Saturn
 *	Author Email: huyanggang@gmail.com
*/

require_once('Akismet.class.php');

class Antispam
{
	const API_KEY = 'e967c5f40d01'; //这里修改为你从注册wordpress.com所获得到的key
	
	private $_akismet;

	 /**
	 * 注册插件以及获得akismet对象handler
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(&$plugin)
	{
		$plugin->register(ST_CORE_HOOK_COMMENT_PREPROCESS, $this, 'spam_filter');
		$plugin->register(ST_CORE_HOOK_TRACKBACK_PREPROCESS, $this, 'spam_filter');
	
		$this->_akismet = new Akismet(site_url(), self::API_KEY); 
	}

	 /**
	 * 插件所触发的方法
	 * 
	 * @access public
	 * @param  array  $comment 评论/引用的内容
	 * @param  object $parent_post 日志内容(stdclass array) 
	 * @return array  $comment
	 */
	public function spam_filter($comment, $parent_post)
	{
		/** 检查API KEY是否有效 */
		if(!$this->_akismet->isKeyValid())
		{
			log_message('error', '你的Akismet API Key无效，请修改。如果没有申请过Key，请到wordpress.com免费申请.');
			
			return $comment;
		}
		
		/** 开始spam检测和过滤 */
		if($comment && is_array($comment))
		{
			$this->_akismet->setCommentAuthor($comment['author']);
			$this->_akismet->setCommentAuthorEmail($comment['mail']);
			$this->_akismet->setCommentAuthorURL($comment['url']);
			
			if('comment' == $comment['type'])
			{
				$this->_akismet->setCommentContent($comment['text']);	
			}
			else
			{
				$content = unserialize($comment['text']);
				$this->_akismet->setCommentContent($content['excerpt']);
			}
			
			$this->_akismet->setPermalink(site_url('posts/' . $parent_post->slug));

			if($this->_akismet->isCommentSpam())
			{
				/** 不直接删除, 仅标记comment为spam (防止误操作) */
				$comment['status'] = 'spam';
				
				$this->_akismet->submitSpam();
			}
			else
			{
				$this->_akismet->submitHam();
			}
		}
		
		return $comment;
	}

}

/* End of file Antispam.php */
/* Location: ./application/st_plugins/Antispam.php */