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
 * STBLOG Posts Controller Class
 *
 * 日志撰写和管理
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Posts extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();

	/**
     * 解析函数
     *
     * @access public
     * @var array
     */
	public function __construct()
	{
		parent::__construct();
		
		/** 权限确认 */
		$this->auth->exceed('contributor');
		
		/** 导航栏和标题 */
		$this->_data['parentPage'] = 'post';
		$this->_data['currentPage'] = 'post';
		$this->_data['page_title'] = '管理文章';
	}
	
	/**
     * 添加一个日志(与用户交互)
     *
     * @access private
     * @return void
     */
	private function _write()
	{
		/** set title */
		$this->_data['page_title'] = '撰写新文章';
		
		/** populated data */
		$this->_data['all_categories'] = $this->metas_mdl->list_metas('category');
		$this->_data['all_tags'] = $this->metas_mdl->list_metas('tag');
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
			
			$this->load->view('admin/write_post',$this->_data);
		}
		else
		{
			$this->_insert_post();
		}
	}

    /**
     * 设置内容标签
     * 
     * @access public
     * @param integer $cid
     * @param string $tags
     * @param boolean $count 是否参与计数
     * @return string
     */
    private function _set_tags($pid, $tags, $before_count = true, $after_count = true)
    {
        $tags = str_replace('，', ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));
        
        /** 取出已有meta */
        $this->metas_mdl->get_metas($pid);

        /** 取出已有tag */
        $exist_tags = Common::array_flatten($this->metas_mdl->metas['tag'], 'mid');
        
        /** 删除已有tag */
        if ($exist_tags) 
        {
            foreach ($exist_tags as $tag) 
            {
                $this->metas_mdl->remove_relationship_strict($pid, $tag);
                
                if ($before_count) 
                {
                    $this->metas_mdl->meta_num_minus($tag);
                }
            }
        }
        
        /** 取出插入tag */
        $insert_tags = $this->metas_mdl->scan_tags($tags);
        
        /** 插入tag */
        if ($insert_tags) 
        {
            foreach ($insert_tags as $tag) 
            {
                $this->metas_mdl->add_relationship(array('pid' => $pid,'mid' => $tag));
                
                if ($after_count)
                {
                    $this->metas_mdl->meta_num_plus($tag);
                }
            }
        }
    }

    /**
     * 设置分类
     * 
     * @access public
     * @param integer $cid 内容id
     * @param array $categories 分类id的集合数组
     * @param boolean $count 是否参与计数
     * @return integer
     */
    public function _set_categories($pid, $categories = array(), $before_count = true, $after_count = true)
    {
        $categories = array_unique(array_map('trim', $categories));
        
        /** 取出已有meta */
        $this->metas_mdl->get_metas($pid);

        /** 取出已有category */
        $exist_categories = Common::array_flatten($this->metas_mdl->metas['category'], 'mid');
        
        /** 删除已有category */
        if ($exist_categories) 
        {
            foreach ($exist_categories as $category) 
            {
                $this->metas_mdl->remove_relationship_strict($pid, $category);
                
                if ($before_count) 
                {
                    $this->metas_mdl->meta_num_minus($category);
                }
            }
        }
        
        /** 插入新的category */
        if ($categories) 
        {
            foreach ($categories as $category) 
            {
                /** 如果分类不存在 */
                if (!$this->metas_mdl->get_meta('BYID', $category)) 
                {
                    continue;
                }
            
                $this->metas_mdl->add_relationship(array('pid' => $pid,'mid' => $category));
                
                if ($after_count) 
                {
                    $this->metas_mdl->meta_num_plus($category);
                }
            }
        }
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
     * 添加一个日志(与数据库交互)
     *	
     * 
     * @access private
     * @return void
     */
	private function _insert_post()
	{
		/** 获取表单数据 */
		$content = $this->_get_form_data();
		/** 文章类型 */
		$content['type'] = 'post';
		/** 文章状态 */
		$draft = $this->input->post('draft', TRUE);
		$content['status'] = $draft ? 'draft' : (($this->auth->exceed('editor', TRUE) && !$draft) ? 'publish' : 'waiting');
		/** 处理相关时间 */
		$content['created'] = $this->_get_created();
		/** 文章排序，默认为0 */
		$content['order'] = 0;
		$content['commentsNum'] = 0;
		
		$insert_struct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'created'       =>  empty($content['created']) ? now() : $content['created'],
            'modified'      =>  now(),
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'order'         =>  empty($content['order']) ? 0 : intval($content['order']),
            'authorId'      =>  isset($content['authorId']) ? $content['authorId'] : $this->user->uid,
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
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
			/** 插入分类 */
            $this->_set_categories($insert_id, $content['category'], false, 'publish' == $content['status']);
            
            /** 插入标签 */
            $this->_set_tags($insert_id, empty($content['tags']) ? NULL : $content['tags'], false, 'publish' == $content['status']);
            
            /** 同步附件 */
            $this->_attachment_related($insert_id, $content['attachment']);
		}
		
		/** 发送trackback */
		$trackback = array_unique(preg_split("/(\r|\n|\r\n)/", trim($content['trackback'])));
		if(!empty($trackback))
		{
			$this->_send_trackback($insert_id, $trackback);	
		}
				
		if($content['status'] == 'draft')
		{
			$this->session->set_flashdata('success', '草稿"'.$content['title'].'"已经保存');
			redirect('admin/posts/write'.'/'.$insert_id);
		}
		else
		{
			$this->session->set_flashdata('success', '文章 <b>'.$content['title'].'</b> 已经被创建');
			redirect('admin/posts/manage');
		}
	}
	
	/**
     * 修改一个日志（与用户交互）
     *
     * @access private
     * @return void
     */
	private function _edit($pid)
	{
		/** get post data **/
		$post_db = $this->posts_mdl->get_post_by_id('pid', $pid);
		
		/** test if it exists or not **/
		if(empty($post_db))
		{
			show_error('发生错误：文章不存在或已被删除。');
			exit();
		}
		
		/** contributor can modify the post from himself ONLY **/
		if($this->user->group == 'contributor' && $this->user->uid != $post_db->authorId)
		{
			show_error('权限错误：你仅能修改自己的文章。');
			exit();
		}
		
		//populated data: tags and categories
		$this->metas_mdl->get_metas($pid);
		$pop_categories = Common::array_flatten($this->metas_mdl->metas['category'], 'mid');
		$pop_tags = Common::format_metas($this->metas_mdl->metas['tag'], ',' , FALSE);
		
		//populated the rest data to the view
		$this->_data['parentPage'] = 'post';
		$this->_data['currentPage'] = 'post';
		$this->_data['page_title'] = '编辑文章：'.$post_db->title;
		$this->_data['all_categories'] = $this->metas_mdl->list_metas('category');
		$this->_data['all_tags'] = $this->metas_mdl->list_metas('tag');
		$this->_data['pid'] = $pid;
		$this->_data['title'] = $post_db->title;
		$this->_data['text'] = $post_db->text;
		$this->_data['post_category'] = $pop_categories;
		$this->_data['created'] = $post_db->created;
		$this->_data['slug'] = $post_db->slug;
		$this->_data['tags'] = $pop_tags;
		$this->_data['attachments'] = $this->posts_mdl->get_posts('attachment','unattached',$this->user->uid,100,0);
		$this->_data['allow_comment'] = $post_db->allowComment;
		$this->_data['allow_ping'] = $post_db->allowPing;
		$this->_data['allow_feed'] = $post_db->allowFeed;
		
		
		//validation stuff
		$this->_load_validation_rules();
		$this->form_validation->month = date('n', $post_db->created);
		$this->form_validation->day = date('j',$post_db->created);
		$this->form_validation->year = date('Y', $post_db->created);
		$this->form_validation->hour = date('G', $post_db->created);
		$this->form_validation->minute = date('i', $post_db->created);
		
		//validation passed or failed?
		if($this->form_validation->run() === FALSE)
		{
			$this->load->view('admin/write_post',$this->_data);
		}
		else
		{
			$this->_update_post($pid, $post_db);	
		}
	}

	/**
     * 修改一个日志（与数据库交互）
     *
     * @access private
     * @return void
     */
	private function _update_post($pid, $exist_post)
	{
		/** 获取表单数据 */
		$content = $this->_get_form_data();
		/** 文章类型 */
		$content['type'] = 'post';
		/** 文章状态 */
		$draft = $this->input->post('draft', TRUE);
		$content['status'] = $draft ? 'draft' : (($this->auth->exceed('editor', TRUE) && !$draft) ? 'publish' : 'waiting');
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
			/** 插入分类 */
            $this->_set_categories($pid, $content['category'], 'publish' == $exist_post->status, 'publish' == $content['status']);
            
            /** 插入标签 */
            $this->_set_tags($pid, empty($content['tags']) ? NULL : $content['tags'], 'publish' == $exist_post->status, 'publish' == $content['status']);
            
            /** 同步附件 */
            $this->_attachment_related($pid, $content['attachment']);
		}
		
		/** 发送trackback */
		$trackback = array_unique(preg_split("/(\r|\n|\r\n)/", trim($content['trackback'])));
		if(!empty($trackback))
		{
			$this->_send_trackback($pid, $trackback);	
		}
				
		if($content['status'] == 'draft')
		{
			$this->session->set_flashdata('success', '草稿"'.$content['title'].'"已经保存');
			redirect('admin/posts/write'.'/'.$pid);
		}
		else
		{
			$this->session->set_flashdata('success', '文章 <b>'.$content['title'].'</b> 修改成功');
			redirect('admin/posts/manage');
		}
	}
	
	/**
     * 获取表单数据
     *
     * @access private
     * @return array
     */
	private function _get_form_data()
	{
		return array(
			'title' 		=> 	$this->input->post('title',TRUE),
			'text' 			=> 	$this->input->post('text',TRUE),
			'allowComment' 	=> 	$this->input->post('allowComment',TRUE),
			'allowPing' 	=> 	$this->input->post('allowPing',TRUE),
			'allowFeed' 	=>	$this->input->post('allowFeed',TRUE),
			'tags' 			=> 	$this->input->post('tags',TRUE),
			'trackback' 	=> 	$this->input->post('trackback',TRUE),
			'attachment' 	=> 	$this->input->post('attachment',TRUE),
			'category' 		=> 	$this->input->post('category',TRUE),
			'slug' 			=> 	$this->input->post('slug',TRUE)
		);
	}

	/**
     * 关连附件
     *
     * @access private
     * @return void
     */
	private function _attachment_related($pid, $attachments = array())
	{
		if(empty($pid) || empty($attachments))
			return;
		
		foreach($attachments as $attachment)
		{
			$this->posts_mdl->update_post($attachment,array('order' => $pid, 'status' => 'attached'));
		}
	}

	/**
     * 加载验证规则
     *
     * @access private
     * @return void
     */
	private function _load_validation_rules()
	{
		$this->form_validation->set_rules('title', '标题', 'required|trim|htmlspecialchars');
		$this->form_validation->set_rules('text', '内容', 'required|trim');
		$this->form_validation->set_rules('tags', '标签', 'trim|htmlspecialchars');
		$this->form_validation->set_rules('category[]', '分类', 'required|trim');	
		$this->form_validation->set_rules('allowComment', '允许评论', 'trim');
		$this->form_validation->set_rules('allowPing', '允许被引用', 'trim');
		$this->form_validation->set_rules('allowFeed', '允许在聚合中出现', 'trim');
		$this->form_validation->set_rules('slug', '缩略名', 'trim|alpha_dash|htmlspecialchars');	
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
		$slug = $this->input->post('slug', TRUE);
		$slug = (!empty($slug)) ? $slug : NULL;
		$slug = Common::repair_slugName($slug,$pid);
		
		$this->posts_mdl->update_post($pid,array('slug' => $this->posts_mdl->get_slug_name($slug,$pid)));
	}
	
	/**
     * 发送Ping
     *
     * @access private
     * @param  string $pid
     * @param  array  $trackbacks
     * @return void
     */
	private function _send_trackback($pid, $trackbacks = array())
	{
		if(empty($pid))
		{
			return;
		}
		
		$post = $this->posts_mdl->get_post_by_id('pid', $pid);
		
		$trackbacks = ($trackbacks) ? (is_array($trackbacks) ? $trackbacks : array($trackbacks)) : array();
		
		$this->load->library('trackback');
		
		foreach($trackbacks as $trackback)
		{
			if(empty($trackback))
			{
				continue;
			}
			
			$tb_data = array(
                'ping_url'  => $trackback,
                'url'       => site_url('posts/'. $post->slug),
                'title'     => $post->title,
                'excerpt'   => Common::get_excerpt($post->text),
                'blog_name' => setting_item('blog_title'),
                'charset'   => 'utf-8'
                );
             
            if ( ! $this->trackback->send($tb_data)) 
			{
			     log_message('error', $this->trackback->display_errors());
			}
		}
	}
	
	/**
     * 默认执行函数
     *
     * @access public
     * @return void
     */
	public function index()
	{
		redirect('admin/posts/write');
	}

	/**
     * 函数转发
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
     * 批量操作文章
     *
     * @access private
     * @return void
     */
	public function operate()
	{
		/** 尝试get获取数据 */
		$action = $this->input->post('do',TRUE);
		
		switch($action)
		{
			case 'delete':
				$this->_remove();
				break;
			case 'approved':
				$this->_approved();
				break;
			default:
				show_404();
				break;
		}
	}
	
	/**
     * 批量审核文章
     *
     * @access private
     * @return void
     */
	private function _approved()
	{
		$posts = $this->input->post('pid',TRUE);
		$approved = 0;
		
		if($posts && is_array($posts))
		{
			foreach($posts as $post)
			{
				if(empty($post))
				{
					continue;
				}
				
				$content = $this->posts_mdl->get_post_by_id('pid', $post);
				
				if($content && $this->auth->exceed('editor', TRUE))
				{
					if($this->posts_mdl->update_post($post, array('status' => 'publish')))
					{
						$approved++;	
					}
				}
				
				$content = NULL;
			}
		
		}
		
		($approved > 0)
					?$this->session->set_flashdata('success', '成功审核文章')
					:$this->session->set_flashdata('error', '没有文章被审核');
		
		go_back();
		
	}

	/**
     * 批量删除文章
     *
     * @access private
     * @return void
     */
	private function _remove()
	{
		$posts = $this->input->post('pid',TRUE);
		$deleted = 0;
		
		if($posts && is_array($posts))
		{
			foreach($posts as $post)
			{
				if(empty($post))
				{
					continue;
				}
				
				$content = $this->posts_mdl->get_post_by_id('pid', $post);
				
				if($content && ($this->auth->exceed('editor', TRUE) || $content->authorId == $this->user->uid))
				{
					/** remove post */
					$this->posts_mdl->remove_post($post);
						
					/** remove related attachments */
					$attachments = $this->posts_mdl->get_posts_by_order($post);
						
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
						
					$metas = $this->metas_mdl->get_metas($post, TRUE);
					
					/** remove the relationship */
					foreach($metas as $meta)
					{
						$this->metas_mdl->meta_num_minus($meta['mid']);
					}
					
					$this->metas_mdl->remove_relationship('pid', $post);
					
					$deleted++;
				}
				
				$content = NULL;
			}
		
		}
		
		($deleted > 0)
					?$this->session->set_flashdata('success', '成功删除文章及其附件')
					:$this->session->set_flashdata('error', '没有文章被删除');
		
		go_back();
	}
	
	/**
     * 管理日志
     *
     * @access public
     * @return void
     */
	public function manage($status = 'publish')
	{
		/** 默认标题 */
		$this->_data['page_title'] = '管理文章';
		
		/** 分页的query string */
		$query = array();
		
		/** 如果具有编辑以上权限,可以查看所有文章,反之只能查看自己的文章 */
		$author_id = $this->user->uid;
		
		if($this->auth->exceed('editor',TRUE))
		{
			$author = $this->input->get('author',TRUE);
			
			if($author && is_numeric($author))
			{	
				$author_info = $this->users_mdl->get_user_by_id($author);
				
				if($author_info)
				{
					$author_id = $author;
				
					$this->_data['author_id'] = $author;
					$this->_data['page_title'] = $author_info['screenName'].'的文章';
					
					$query[] = 'author='.$author;
				}
				
				unset($author_info);
			}
			
			/** 是否能够查看全部文章 */
			$all_posts = $this->input->get('__all_posts',TRUE);
			
			if(!empty($all_posts))
			{
				if('on' == $all_posts)
				{
					$this->session->set_userdata('__all_posts', 'on');
					
					$author_id = NULL;
				}
				else if('off' == $all_posts)
				{
					$this->session->unset_userdata('__all_posts');
				}
			}
			else
			{
				if('on' == $this->session->userdata('__all_posts'))
				{
					$author_id = NULL;
				}
			}
		}
		
		/** check status */
		if(!in_array($status, array('publish', 'draft', 'waiting')))
		{
			redirect('admin/posts/manage');
		}
		
		/** category filter */
		$category_filter = $this->input->get('category', TRUE);		
		$category_filter = (!empty($category_filter)) ? intval($category_filter) : 0;
		
		if(!empty($category_filter))
		{
			$query[] = 'category='.$category_filter;
		}
		
		/** title filter (posts search) **/
		$title_filter = strip_tags($this->input->get('keywords',TRUE));
		if(!empty($title_filter))
		{
			$this->_data['page_title'] = '搜索文章：'. $title_filter;	
			$query[] = 'keywords='.$title_filter;	
		}
		
		/** pagination stff */
		$page = $this->input->get('p',TRUE);
		$page = (!empty($page) && is_numeric($page)) ? intval($page) : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;
		
		if($offset < 0)
		{
			redirect('admin/posts/manage');
		}
		
		$posts = $this->posts_mdl->get_posts('post', $status, $author_id, $limit, $offset, $category_filter, $title_filter);
		$posts_count = $this->posts_mdl->get_posts('post', $status, $author_id, 10000, 0, $category_filter, $title_filter)->num_rows();
		
		if($posts)
		{
			foreach($posts->result() as $post)
			{
				$this->metas_mdl->get_metas($post->pid);
				$post->categories = $this->metas_mdl->metas['category'];
			}
			
			$pagination = '';
			
			if($posts_count > $limit)
			{	
				$this->dpagination->currentPage($page);
				$this->dpagination->items($posts_count);
				$this->dpagination->limit($limit);
				$this->dpagination->adjacents(5);
				$this->dpagination->target(site_url('admin/posts/manage?'.implode('&',$query)));
				$this->dpagination->parameterName('p');
				$this->dpagination->nextLabel('下一页');
				$this->dpagination->PrevLabel('上一页');
				
				$pagination = $this->dpagination->getOutput();
			}
			
			$this->_data['pagination'] = $pagination;
		}
		
		$this->_data['status'] = $status;
		$this->_data['parentPage'] = 'manage-posts';
		$this->_data['currentPage'] = 'manage-posts';
		$this->_data['posts'] = $posts;
		$this->_data['categories'] = $this->metas_mdl->list_metas('category');
		
		$this->load->view('admin/manage_posts',$this->_data);
	}
	
}

/* End of file Posts.php */
/* Location: ./application/controllers/admin/Posts.php */
