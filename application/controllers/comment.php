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
 * STBlog 用户评论控制器
 *
 *	主要用于处理用户评论
 *
 * @package		STBLOG
 * @subpackage	Controllers
 * @category	Front-controllers
 * @author		Saturn <huyanggang@gmail.com>
 * @link		http://code.google.com/p/stblog/
 */
class Comment extends ST_Controller 
{

	 /**
     * 解析函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('form_validation');
		
		log_message('debug', 'STBLOG: Comment Controller initialized.');
	}

	 /**
     * 默认执行函数
     * 
     * @access public
     * @param  int  $pid 文档ID
     * @return void
     */
	public function index($pid)
	{
		/** 参数不符合要求? */
		if(empty($pid) || !is_numeric($pid))
		{
			go_back();
		}
		
		/** 获得评论主题 */
		$parent_post = $this->posts_mdl->get_post_by_id('pid', intval($pid));
		
		/** 评论主题不存在? */
		if(!$parent_post)
		{
			show_error('评论失败：找不到内容');
			exit();
		}
		
		/** 此文不允许评论? */
		if(0 == intval($parent_post->allowComment))
		{
			show_error('评论失败：内容关闭了评论功能');
			exit();
		}
		
		/** 如果库中已经存在当前ip为spam的comment则直接拒绝 */
		$spams = $this->comments_mdl->get_cmts_by_ip('comment', 'spam', $this->input->ip_address());
		if($spams->num_rows() > 0)
		{
			show_404();
		}
		
		unset($spams);
		
		/*** 加载验证条件 */
		$this->_load_validation_rules();
		
		/*** 验证失败还是成功? */
		if($this->form_validation->run() === FALSE)
		{
			show_error(validation_errors());
		}
		else
		{
			/**
				To-do list:
					这里还有两个功能没做：评论盖楼和登陆用户的评论设置
			*/
		
			$this->load->library('user_agent');
			
			/** 获取提交的数据 */
			$comment = array(
	            'pid'       =>  $parent_post->pid,
	            'author'   	=>  $this->input->post('author', TRUE),
	            'mail'   	=>  $this->input->post('mail', TRUE),
	            'url'   	=>  $this->input->post('url', TRUE),
	            'created'   =>  now(),
	            'agent'     =>  $this->agent->agent_string(),
	            'ip'        =>  $this->input->ip_address(),
	            'ownerId'   =>  $parent_post->authorId,
	            'type'      =>  'comment',
	            'status'    =>  1 == intval(setting_item('comments_require_moderation')) ? 'waiting' : 'approved',
	            'text'		=>	$this->_filter_text($this->input->post('text', TRUE)),
	            'parent'	=> 0 //预留，用来盖楼
	        );
	        
	        /** 获取用户数据后的插件处理钩子 */
	        if($this->plugin->check_hook_exist(ST_CORE_HOOK_COMMENT_PREPROCESS))
	        {
	        	$comment = $this->plugin->trigger(ST_CORE_HOOK_COMMENT_PREPROCESS, $comment, $parent_post);	
	        }
	        
	        /** 主数据入库 */
	        $insert_id = $this->comments_mdl->add_cmt($comment);
	        
	        /** 更新评论数据 */
	        if('approved' == $comment['status'])
	        {
	        	$this->posts_mdl->refresh_comments_count($comment['pid']);
	        }
	        
	        if('waiting' == $comment['status'])
	        {
	        	$data['title'] = '评论提示';
	        	$data['heading'] = '你的留言需要审核后才能显示';
	        	$data['message'] = '由于本站开启了评论审核功能，你的留言需要审核后才能显示.';
	        	
	        	$this->load_theme_view('msg', $data, FALSE);
	        }
	        
	        if('spam' == $comment['status'])
	        {
	        	$data['title'] = '评论警告';
	        	$data['heading'] = '你的留言有SPAM嫌疑';
	        	$data['message'] = '你的留言IP已被系统自动屏蔽，如有误，请联系站点管理员.';
	        	
	        	$this->load_theme_view('msg', $data, FALSE);
	        }
	        
	        if(1 == setting_item('cache_enabled'))
	        {
	        	$cache_expired = intval(setting_item('cache_expire_time'));
	        	
	        	$data['title'] = '评论成功';
	        	$data['heading'] = '恭喜你，评论成功';
	        	$data['message'] = "您的评论至多{$cache_expired}分钟后就会被显示出来.";
	        	
	        	$this->load_theme_view('msg', $data, FALSE);
	        }
	        else
	        {
	        	go_back('#comment-' . $insert_id);	
	        }
		}
	}

	/**
     * trackback
     * 
     * @access public
     * @param  int   $pid
     * @return string
     */
	public function trackback($pid)
	{
		$this->load->library('trackback');
		
		/** 参数不符合要求? */
		if(empty($pid) || !is_numeric($pid))
		{
			$this->trackback->send_error("Unable to determine the entry ID");
		}
		
		/** 只接受POST */
		if($this->input->server('REQUEST_METHOD') != 'POST')
		{
			show_error('Trackback accepts POST request ONLY.');
		}
		
		/** 获得引用主题 */
		$parent_post = $this->posts_mdl->get_post_by_id('pid', intval($pid));
		
		/** 引用主题不存在? */
		if(!$parent_post)
		{
			$this->trackback->send_error("Unable to retrieve the article.");
		}
		
		/** 此文不允许引用? */
		if(0 == intval($parent_post->allowPing))
		{
			$this->trackback->send_error("Ping denied.");
		}
		
		/** 如果库中已经存在当前ip为spam的trackback则直接拒绝 */
		$spams = $this->comments_mdl->get_cmts_by_ip('trackback', 'spam', $this->input->ip_address());
		if($spams->num_rows() > 0)
		{
			$this->trackback->send_error("Ping denied, big brother is watching you.");
		}
		
		unset($spams);
        
        /*** 加载验证条件 */
		$this->form_validation->set_rules('blog_name', '博客名称', 'required|trim|min_length[2]|max_length[200]|htmlspecialchars');
		$this->form_validation->set_rules('title', '日志标题', 'trim|htmlspecialchars');
		$this->form_validation->set_rules('url', 'URL', 'trim|prep_url|strip_tags');
		$this->form_validation->set_rules('excerpt', '日志摘要', 'trim');
		
		/*** 验证失败还是成功? */
		if($this->form_validation->run() === FALSE)
		{
			$this->trackback->send_error(validation_errors());
		}
		else
		{
			$this->load->library('user_agent');
			
			if (!$this->trackback->receive())
			{
			    $this->trackback->send_error("The Trackback contains invalid data.");
			}
			
			$content = serialize(array(
								'title' => $this->trackback->data('title'),
								'excerpt' => $this->_filter_text($this->trackback->data('excerpt'))
							));
			
			/** 获取提交的数据 */
			$trackback = array(
	            'pid'       =>  $parent_post->pid,
	            'author'   	=>  $this->trackback->data('blog_name'),
	            'url'   	=>  $this->trackback->data('url'),
	            'created'   =>  now(),
	            'agent'     =>  $this->agent->agent_string(),
	            'ip'        =>  $this->input->ip_address(),
	            'ownerId'   =>  $parent_post->authorId,
	            'type'      =>  'trackback',
	            'status'    =>  1 == intval(setting_item('comments_require_moderation')) ? 'waiting' : 'approved',
	            'text'		=>	$content,
	            'parent'	=> 	0
	        );
	        
	        /** 截取长度 */
        	$trackback['text'] = Common::subStr($trackback['text'], 0, 100, '[...]');
        	
        	/** 如果库中已经存在重复url则直接拒绝 */
        	$duplicates = $this->comments_mdl->check_url_exists($trackback['pid'], $trackback['url'], 'trackback');
        	if($duplicates->num_rows() >0)
        	{
        		$this->trackback->send_error("please donnot send duplicated pings.");
        	}
	        
	        /** 获取用户数据后的插件处理钩子 */
	        if($this->plugin->check_hook_exist(ST_CORE_HOOK_TRACKBACK_PREPROCESS))
	        {
	        	$trackback = $this->plugin->trigger(ST_CORE_HOOK_TRACKBACK_PREPROCESS, $trackback, $parent_post);	
	        }

	        /** 主数据入库 */
	        $this->comments_mdl->add_cmt($trackback);
	        
	        /** 更新评论数据 */
	        if('approved' == $trackback['status'])
	        {
	        	$this->posts_mdl->refresh_comments_count($trackback['pid']);
	        }
	        
	        $this->trackback->send_success();
		}
	}
	
	/**
     * 加载验证规则
     * 
     * @access public
     * @return string
     */
	private function _load_validation_rules()
	{
		$this->form_validation->set_rules('author', '称呼', 'required|trim|min_length[2]|max_length[200]|strip_tags');
		
		/** 邮箱是否必需? */
		if(1 == intval(setting_item('comments_require_mail')))
		{
			$this->form_validation->set_rules('mail', 'Email', 'required|trim|valid_email|max_length[200]|strip_tags');
		}
		else
		{
			$this->form_validation->set_rules('mail', 'Email', 'trim|valid_email|max_length[200]|strip_tags');
		}
		
		/** 网站是否必需? */
		if(1 == intval(setting_item('comments_require_url')))
		{
			$this->form_validation->set_rules('url', '网站(URL)', 'required|trim|prep_url|max_length[200]|strip_tags');
		}
		else
		{
			$this->form_validation->set_rules('url', '网站(URL)', 'trim|prep_url|max_length[200]|strip_tags');
		}
		
		$this->form_validation->set_rules('text', '内容', 'required|trim');
	}
	
	/**
     * 过滤评论内容
     * 
     * @access public
     * @param string $text 评论内容
     * @return string
     */
	private function _filter_text($text)
	{
		$text = str_replace("\r", '', $text);
        $text = preg_replace("/\n{2,}/", "\n\n", $text);
    
        return Common::stripTags($text, setting_item('commentsHTMLTagAllowed'));
	}
}

/* End of file Comment.php */
/* Location: ./application/controllers/Comment.php */