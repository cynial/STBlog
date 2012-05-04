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
 * STBLOG Pages Controller Class
 *
 * 页面的创建和管理
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Pages extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();

	public function __construct()
	{
		parent::__construct();
		
		/** 权限确认 */
		$this->auth->exceed('contributor');
		
		/** 页面标题 */
		$this->_data['parentPage'] = 'post';
		$this->_data['currentPage'] = 'page';
		$this->_data['page_title'] = '创建新页面';
	}
	
	/**
     * 添加一个页面
     *
     * @access private
     * @return void
     */
	private function _write()
	{		
		/** populated data */
		$this->_data['attachments'] = $this->posts_mdl->get_posts('attachment','unattached',$this->user->uid,100,0);
		$this->_data['allow_comment'] = 1;
		$this->_data['allow_ping'] = 1;
		$this->_data['allow_feed'] = 1;
		
		/** validation rules */
		$this->_load_validation_rules();
		
		/** validation passed or failed? */
		if($this->form_validation->run() === FALSE)
		{
			/** validation failed */
			$this->form_validation->month = date('n');
			$this->form_validation->day = date('j');
			$this->form_validation->year = date('Y');
			$this->form_validation->hour = date('G');
			$this->form_validation->minute = date('i');
			
			$this->load->view('admin/write_page',$this->_data);
		}
		else
		{
			/** 获取表单数据 */
			$content = $this->_get_form_data();
			/** 文章类型 */
			$content['type'] = 'page';
			/** 文章状态 */
			$draft = $this->input->post('draft', TRUE);
			$content['status'] = $draft ? 'draft' : (($this->auth->exceed('editor', TRUE) && !$draft) ? 'publish' : 'draft');
			/** 处理相关时间 */
			$content['created'] = $this->_get_created();
			$content['commentsNum'] = 0;
			
			$insert_struct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'created'       =>  empty($content['created']) ? now() : $content['created'],
            'modified'      =>  now(),
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'order'         =>  empty($content['order']) ? 0 : intval($content['order']),
            'authorId'      =>  isset($content['authorId']) ? $content['authorId'] : $this->user->uid,
            'type'          =>  empty($content['type']) ? 'page' : $content['type'],
            'status'        =>  empty($content['status']) ? 'publish' : $content['status'],
            'commentsNum'   =>  empty($content['commentsNum']) ? 0 : $content['commentsNum'],
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 1 : 0,
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 1 : 0,
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 1 : 0
        	);
        	
        	/** 核心数据进主库 */
			$insert_id = $this->posts_mdl->add_post($insert_struct);
				
			/** 应用缩略名 */
			$this->_apply_slug($insert_id);
			
			if($insert_id >0)
			{   
	            /** 同步附件 */
	            $this->_attachment_related($insert_id, $content['attachment']);
			}
			
			if($content['status'] == 'draft')
			{
				$this->session->set_flashdata('success', '页面草稿"'.$content['title'].'"已经保存');
				redirect('admin/pages/write'.'/'. $insert_id);
			}
			else
			{
				$this->session->set_flashdata('success', '页面 <b>'.$content['title'].'</b> 已经被创建');
				redirect('admin/pages/manage');
			}
		
		}
	}
	
	/**
     * 修改一个页面
     *
     * @access private
     * @return void
     */
	private function _edit($pid)
	{
		$post_db = $this->posts_mdl->get_post_by_id('pid', $pid);
		
		if(empty($post_db))
		{
			show_error('发生错误：文章不存在或已被删除。');
			exit();
		}
		
		/** 标题 */
		$this->_data['page_title'] = '编辑文章：'.$post_db->title;
		
		/** populated data */
		$this->_data['pid'] = $pid;
		$this->_data['title'] = $post_db->title;
		$this->_data['text'] = $post_db->text;
		$this->_data['created'] = $post_db->created;
		$this->_data['slug'] = $post_db->slug;
		$this->_data['order'] = $post_db->order;
		$this->_data['attachments'] = $this->posts_mdl->get_posts('attachment','unattached',$this->user->uid,100,0);
		$this->_data['allow_comment'] = $post_db->allowComment;
		$this->_data['allow_ping'] = $post_db->allowPing;
		$this->_data['allow_feed'] = $post_db->allowFeed;
		
		/** validation stuff **/
		$this->_load_validation_rules();
		$this->form_validation->month = date('n', $post_db->created);
		$this->form_validation->day = date('j',$post_db->created);
		$this->form_validation->year = date('Y', $post_db->created);
		$this->form_validation->hour = date('G', $post_db->created);
		$this->form_validation->minute = date('i', $post_db->created);
		
		/** validation passed or failed? **/
		if($this->form_validation->run() === FALSE)
		{
			$this->load->view('admin/write_page',$this->_data);
		}
		else
		{
			/** 获取表单数据 */
			$content = $this->_get_form_data();
			/** 文章类型 */
			$content['type'] = 'page';
			/** 文章状态 */
			$draft = $this->input->post('draft', TRUE);
			$content['status'] = $draft ? 'draft' : (($this->auth->exceed('editor', TRUE) && !$draft) ? 'publish' : 'draft');
			/** 处理相关时间 */
			$content['created'] = $this->_get_created();
	
			
			$update_struct = array(
	            'title'         =>  empty($content['title']) ? NULL : $content['title'],
	            'created'       =>  empty($content['created']) ? now() : $content['created'],
	            'modified'      =>  now(),
	            'text'          =>  empty($content['text']) ? NULL : $content['text'],
	            'order'         =>  empty($content['order']) ? 0 : intval($content['order']),
	            'authorId'      =>  isset($content['authorId']) ? $content['authorId'] : $this->user->uid,
	            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
	            'status'        =>  empty($content['status']) ? 'publish' : $content['status'],
	            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 1 : 0,
	            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 1 : 0,
	            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 1 : 0
	        );
	        
	        /** 核心数据进主库 */
			$updated_rows = $this->posts_mdl->update_post($pid, $update_struct);
				
			/** 应用缩略名 */
			$this->_apply_slug($pid);
			
			if($updated_rows >0)
			{
	            /** 同步附件 */
	            $this->_attachment_related($pid, $content['attachment']);
			}
			
			if($content['status'] == 'draft')
			{
				$this->session->set_flashdata('success', '页面草稿"'.$content['title'].'"已经保存');
				redirect('admin/pages/write'.'/'.$pid);
			}
			else
			{
				$this->session->set_flashdata('success', '页面 <b>'.$content['title'].'</b> 已经被创建');
				redirect('admin/pages/manage');
			}			
		}
	}
	
	private function _attachment_related($pid,$attachments = array())
	{
		if(empty($pid) || empty($attachments))
			return;
		
		foreach($attachments as $attachment)
		{
			$this->posts_mdl->update_post($attachment,array('order' => $pid,'status' => 'attached'));
		}
	}
	
	private function _load_validation_rules()
	{
		$this->form_validation->set_rules('title', '标题', 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('text', '内容', 'required|trim');	
		$this->form_validation->set_rules('slug', '缩略名', 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('allowComment', '允许评论', 'trim');
		$this->form_validation->set_rules('allowPing', '允许被引用', 'trim');
		$this->form_validation->set_rules('allowFeed', '允许在聚合中出现', 'trim');
		$this->form_validation->set_rules('slug', '缩略名', 'trim|alpha_dash|htmlspecialchars');
		$this->form_validation->set_rules('order', '页面顺序', 'trim|integer');	
	}
	
	/**
     * 获取表单数据
     *
     * @access private
     * @return array
     */
	private function _get_form_data()
	{
		$form_data = array();

		$draft = $this->input->post('draft',TRUE);
		
		$form_data = array(
			'title' 		=> 	$this->input->post('title', TRUE),
			'text' 			=> 	$this->input->post('text', TRUE),
			'allowComment' 	=> 	$this->input->post('allowComment', TRUE),
			'allowPing' 	=> 	$this->input->post('allowPing', TRUE),
			'allowFeed' 	=>	$this->input->post('allowFeed', TRUE),
			'trackback' 	=> 	$this->input->post('trackback', TRUE),
			'attachment' 	=> 	$this->input->post('attachment', TRUE),
			'slug' 			=> 	$this->input->post('slug', TRUE),
			'order'			=>	$this->input->post('order', TRUE)
		);
		
		return $form_data;
	}

    /**
     * 获取创建时间
     *
     *	TODO: 时区设置
     *	
     * 
     * @access private
     * @return integer
     */
	private function _get_created()
	{
	    $created = now();
	    
		$second = 0;
		$min = intval($this->input->post('min',TRUE));
		$hour = intval($this->input->post('hour',TRUE));
            
        $year = intval($this->input->post('year',TRUE));
        $month = intval($this->input->post('month',TRUE));
        $day = intval($this->input->post('day',TRUE));
        
        return mktime($hour, $min, $second, $month, $day, $year);
	}
	
	/**
     * 为内容应用缩略名
     * 
     * @access private
     * @param string $slug 缩略名
     * @param mixed $cid 内容id
     * @return string
     */
	private function _apply_slug($pid)
	{
		$slug = $this->input->post('slug',TRUE);
		$slug = (!empty($slug))?$slug:NULL;
		$slug = Common::repair_slugName($slug,$pid);
		
		$this->posts_mdl->update_post($pid, array('slug' => $this->posts_mdl->get_slug_name($slug, $pid)));
	}
	
	
	/**
     * 默认执行函数
     *
     * @access public
     * @return void
     */
	public function index()
	{
		redirect('admin/pages/write');
	}

	/**
     * function dispatcher
     *
     * @access public
     * @return void
     */
	public function write()
	{
		if (FALSE === $this->uri->segment(4))
		{
			$this->_write();
		}
		else
		{
			$pid = $this->security->xss_clean($this->uri->segment(4));
			is_numeric($pid)?$this->_edit($pid):show_error('禁止访问：危险操作');
		}
	}
	
	
	/**
     * 管理页面
     *
     * @access public
     * @return void
     */
	public function manage($status = 'publish')
	{
		/** privilege confirm */
		$this->auth->exceed('editor');
		
		/** default page title */
		$this->_data['page_title'] = '管理页面';
		
		/** query string */
		$query = array();
		
		/** check status */
		if(!in_array($status, array('publish', 'draft')))
		{
			redirect('admin/pages/manage');
		}
		
		
		/** title filter (pages search) */
		$title_filter = strip_tags($this->input->get('keywords',TRUE));
		if(!empty($title_filter))
		{
			$this->_data['page_title'] = '搜索页面：'.$title_filter;	
			
			$query[] = 'keywords='.$title_filter;	
		}
		
		/** 分页设置 */
		$page = $this->input->get('p',TRUE);
		$page = (!empty($page) && is_numeric($page)) ? intval($page) : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;
		
		if($offset < 0)
		{
			redirect('admin/pages/manage');
		}
		
		$posts = $this->posts_mdl->get_posts('page',$status,NULL,$limit,$offset,0,$title_filter);
		$posts_count = $this->posts_mdl->get_posts('page',$status,NULL,10000,0,0,$title_filter)->num_rows();
		
		if($posts)
		{
			$pagination = '';
			
			if($posts_count > $limit)
			{
				
				$this->dpagination->currentPage($page);
				$this->dpagination->items($posts_count);
				$this->dpagination->limit($limit);
				$this->dpagination->adjacents(5);
				$this->dpagination->target(site_url("admin/pages/manage/$status?".implode('&',$query)));
				$this->dpagination->parameterName('p');
				$this->dpagination->nextLabel('下一页');
				$this->dpagination->PrevLabel('上一页');
				
				$pagination = $this->dpagination->getOutput();
			}
			
			$this->_data['pagination'] = $pagination;
		}
		
		$this->_data['parentPage'] = 'manage-pages';
		$this->_data['currentPage'] = 'manage-pages';
		$this->_data['posts'] = $posts;
		$this->_data['status'] = $status;
		
		$this->load->view('admin/manage_pages',$this->_data);
	}

	/**
     * 批量删除页面
     *
     * @access public
     * @return void
     */
	public function remove()
	{
		/** privilege confirm */
		$this->auth->exceed('editor');
		
		$pages = $this->input->post('pid', TRUE);
		$deleted = 0;
		
		if($pages && is_array($pages))
		{
			foreach($pages as $page)
			{
				if(empty($page))
				{
					continue;
				}
				/** remove post */
				$this->posts_mdl->remove_post($page);
					
				/** remove related attachments */
				$attachments = $this->posts_mdl->get_posts_by_order($page);
					
				if($attachments->num_rows() > 0)
				{
					foreach($attachments->result() as $attachment)
					{
						$info = unserialize($attachment->text);
						
						/** delete the file physically */
						@unlink(FCPATH. $info['path']);
						
						/** delete the data in DB */
						$this->posts_mdl->remove_post($attachment->pid);
					}
				}
				
				$deleted++;
			}			
		
		}
		
		($deleted > 0)
					?$this->session->set_flashdata('success', '成功删除页面及其附件')
					:$this->session->set_flashdata('error', '没有页面被删除');
		
		go_back();
	}
	
}

/* End of file Pages.php */
/* Location: ./application/controllers/admin/Pages.php */
