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
 * STBLOG Comments Controller Class
 *
 * 留言和引用的管理
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Comments extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();
	
	 /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		/** 权限确认 */
		$this->auth->exceed('contributor');
		
		$this->load->helper('json');
		
		$this->_data['parentPage'] = 'manage-posts';
		$this->_data['currentPage'] = 'manage-comments';
	}
	
	 /**
     * 默认执行函数
     * 
     * @access public
     * @return void
     */
	public function index()
	{
		redirect('admin/comments/manage');
	}

	 /**
     * 管理留言和引用
     * 
     * @access public
     * @param  string $status 状态
     * @return void
     */
	public function manage($status = 'approved')
	{
		$query = array();
		
		/** 默认标题 */
		$this->_data['page_title'] = '管理评论和引用';
		
		/** 如果具有编辑以上权限,可以查看所有评论,反之只能查看自己文章的评论 */
		$owner_id = $this->user->uid;
		
		if($this->auth->exceed('editor',TRUE))
		{
			$all_posts = $this->input->get('__all_comments',TRUE);
			
			if(empty($all_posts))
			{
				if('on' == $this->session->userdata('__all_comments'))
				{
					$owner_id = NULL;
				}
			}
			else if('on' == $all_posts)
			{
				$this->session->set_userdata('__all_comments', 'on');
					
				$owner_id = NULL;
			}
			else if('off' == $all_posts)
			{
				$this->session->unset_userdata('__all_comments');
			}
		}
		
		/** 标题过滤，用于搜索 */
		$filter = $this->input->get('keywords', TRUE);
		if(!empty($filter))
		{
			$this->_data['page_title'] = '搜索评论：'. $filter;	
			
			$query[] = 'keywords='. $filter;
		}
		
		/** 查看日志下的评论 */
		$pid = $this->input->get('pid',TRUE);
		if(!empty($pid))
		{
			$post = $this->posts_mdl->get_post_by_id('pid', $pid);
			
			if(empty($post))
			{
				show_error('ERROR!');
				exit();
			}
			
			$this->_data['page_title'] = '文章：'. $post->title .' 的评论';
			
			unset($post);
			
			$query[] = 'pid='.$pid;	
		}
		
		/** 分页设置 */
		$page = $this->input->get('p', TRUE);
		$page = ($page && is_numeric($page)) ? intval($page) : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;
		$pagination = '';
		
		if($offset < 0)
		{
			redirect('admin/comments/manage');
		}
		
		$comments = ($pid && is_numeric($pid))
						?$this->comments_mdl->get_cmts($pid, '', $status, $limit, $offset, 'DESC', $filter)
						:$this->comments_mdl->get_cmts_by_owner('', $status, $owner_id, $limit, $offset, 'DESC',$filter);
		
		$comments_count = ($pid && is_numeric($pid))
						?$this->comments_mdl->get_cmts($pid, '', $status, 10000, 0, 'DESC', $filter)->num_rows()
						:$this->posts_mdl->get_posts('', $status, $owner_id, 10000, 0, 'DESC',$filter)->num_rows();
		
		if($comments)
		{
			foreach($comments->result() as $comment)
			{
				$comment->post = $this->posts_mdl->get_post_by_id('pid', $comment->pid);
			}
			
			$pagination = '';
			
			if($comments_count > $limit)
			{	
				$this->dpagination->currentPage($page);
				$this->dpagination->items($comments_count);
				$this->dpagination->limit($limit);
				$this->dpagination->adjacents(5);
				$this->dpagination->target(site_url('admin/comments/manage/'. $status .'?'.implode('&',$query)));
				$this->dpagination->parameterName('p');
				$this->dpagination->nextLabel('下一页');
				$this->dpagination->PrevLabel('上一页');
				
				$pagination = $this->dpagination->getOutput();
			}
			
			$this->_data['pagination'] = $pagination;
		}
	
		$this->_data['comments'] = $comments;
		$this->_data['status'] = $status;
		
		$this->load->view('admin/manage_comments',$this->_data);
	}

	 /**
     * 操作留言和引用
     * 
     * @access public
     * @return void
     */
	public function operate()
	{
		/** 尝试get获取数据 */
		$action = $this->input->get('do',TRUE);
		
		/* 不是get传递来的数据? */
		if(empty($action))
		{
			/** 换post试试 */
			$action = $this->input->post('do',TRUE);
		}
		
		switch($action)
		{
			case 'get':
				$this->_get();
				break;
			case 'edit':
				$this->_edit();
				break;
			case 'spam':
				$this->_spam();
				break;
			case 'approved';
				$this->_approved();
				break;
			case 'waiting':
				$this->_waiting();
				break;
			case 'delete':
				$this->_delete();
				break;
			case 'delete-spam':
				$this->_delete_spam();
				break;
			default:
				show_404();
				break;
		}
	}
	
	 /**
     * 获得一条评论内容（ajax）
     * 
     * @access private
     * @return void
     */
	private function _get()
	{
		$cid = $this->input->get('cid',TRUE);
		$comment = $this->comments_mdl->get_cmt($cid);
		
		if($comment && $this->_is_cmt_writable($comment))
		{
			throwJson(array(
                'success'   => 1,
                'comment'   => $comment
            ));
		}
		else
		{
			throwJson(array(
                'success'   => 0,
                'comment'   => '获取评论失败'
            ));
		}
	}

	 /**
     * 编辑
     * 
     * @access private
     * @return void
     */
	private function _edit()
	{
		$cid = $this->input->get('cid',TRUE);
		
		//to do: 添加验证配置
		
		$comment = $this->comments_mdl->get_cmt($cid);
		
		if($comment && $this->_is_cmt_writable($comment))
		{
			$data = array(
				'text' => trim($this->input->post('text',TRUE)),
				'author' => trim($this->input->post('author',TRUE)),
				'mail' => trim($this->input->post('mail',TRUE)),
				'url' => trim($this->input->post('url',TRUE))
			);
		
			$this->comments_mdl->update_cmt($cid, $data);
			
			$updated = $this->comments_mdl->get_cmt($cid);
			
			throwJson(array(
                'success'   => 1,
                'comment'   => $updated
            ));
		}

		throwJson(array(
            'success'   => 0,
            'comment'   => '修改评论失败'
        ));
	}

	 /**
     * 是否可写
     * 
     * @access private
     * @param  object $comment 评论对象
     * @return bool
     */
	private function _is_cmt_writable($comment)
	{
		if($this->auth->exceed('editor',TRUE))
		{
			return TRUE;
		}
		
		if($comment->ownerid == $this->user->uid)
		{
			return TRUE;
		}
		
		return FALSE;
	}

	 /**
     * 处理垃圾评论
     * 
     * @access private
     * @return void
     */
	private function _spam()
	{
		$comments = $this->_fetch_cid_as_array();
		$affected_rows = 0;
		
		if(!empty($comments))
		{
			foreach($comments as $comment)
			{
				if($this->mark($comment, 'spam'))
				{
					$affected_rows ++;
				}
			}	
		}
		
		($affected_rows >0)
				?$this->_redirect_with_msg('success', '评论已经被标记为垃圾')
				:$this->_redirect_with_msg('error', '没有评论被标记为垃圾');
	}

	 /**
     * 处理待审核评论
     * 
     * @access private
     * @return void
     */
	private function _waiting()
	{
		$comments = $this->_fetch_cid_as_array();
		$affected_rows = 0;
		
		if(!empty($comments))
		{
			foreach($comments as $comment)
			{
				if($this->mark($comment,'waiting'))
				{
					$affected_rows ++;
				}
			}	
		}
		
		($affected_rows >0)
				?$this->_redirect_with_msg('success', '评论已经被标记为待审核')
				:$this->_redirect_with_msg('error', '没有评论被标记为待审核');
	}

	 /**
     * 处理正常通过评论
     * 
     * @access private
     * @return void
     */
	private function _approved()
	{
		$comments = $this->_fetch_cid_as_array();
		$affected_rows = 0;
		
		if(!empty($comments))
		{
			foreach($comments as $comment)
			{
				if($this->mark($comment,'approved'))
				{
					$affected_rows ++;
				}
			}	
		}
		
		($affected_rows >0)
				?$this->_redirect_with_msg('success', '评论已经被标记为已通过')
				:$this->_redirect_with_msg('error', '没有评论被标记为已通过');
	}

	 /**
     * 删除评论
     * 
     * @access private
     * @return void
     */
	private function _delete()
	{
		$comments = $this->_fetch_cid_as_array();
		$deleted = 0;
		
		if(!empty($comments))
		{
			foreach($comments as $comment)
			{
				$comment = $this->comments_mdl->get_cmt($comment);
				
				/** 删除评论 */
				$this->comments_mdl->remove_cmt(array('cid' => $comment->cid));
				
				/** 更新相关评论数 */
				if('approved' == $comment->status)
				{
					$this->posts_mdl->cmts_num_minus($comment->pid);
				}
				
				$deleted ++;
			}
		}
		
		($deleted >0)
				?$this->_redirect_with_msg('success', '成功删除评论')
				:$this->_redirect_with_msg('error', '没有评论被删除');
	
	}
	
	 /**
     * 删除垃圾评论
     * 
     * @access private
     * @return void
     */
	private function _delete_spam()
	{
		$condition = array('status' => 'spam');
		
		if('on' !== $this->session->userdata('__all_comments') && !$this->auth->exceed('editor',TRUE))
		{
			array_push($condition, array('ownerid' => $this->user->uid));
		}
		
		if($this->input->get('pid',TRUE))
		{
			array_push($condition, array('pid' => $this->input->get('pid',TRUE)));
		}
		
		$this->comments_mdl->remove_cmt($condition);
		
		$this->_redirect_with_msg('success', '成功删除所有垃圾评论');
	}

    /**
     * 标记评论状态
     * 
     * @access private
     * @param integer $cid 评论主键
     * @param string $status 状态
     * @return boolean
     */
	public function mark($cid, $status)
	{
		$comment = $this->comments_mdl->get_cmt($cid);
		
		if($comment && $this->_is_cmt_writable($comment))
		{
			/** 不必更新的情况 */
			if($status == $comment->status)
			{
				return FALSE;
			}
			
			/** 更新评论 */
			$this->comments_mdl->update_cmt($cid, array('status' => $status));
			
			/** 更新相关内容的评论数 */
			if ('approved' == $comment->status && 'approved' != $status) 
			{
				$this->posts_mdl->cmts_num_minus($comment->pid);
            } 
            else if ('approved' != $comment->status && 'approved' == $status) 
            {
                $this->posts_mdl->cmts_num_plus($comment->pid);
            }
            
            return TRUE;
		}
		
		return FALSE;
	}

	 /**
     * 获取待操作的评论ID数组
     * 
     * @access private
     * @return array
     */
	private function _fetch_cid_as_array()
	{
		/** 尝试get获取数据 */
		$cid = $this->input->get('cid',TRUE);
		
		/* 不是get传递来的数据? */
		if(empty($cid))
		{
			/** 换post试试 */
			$cid = $this->input->post('cid',TRUE);
		}
		
		return $cid ? (is_array($cid) ? $cid : array($cid)) : array();
	}
	
	private function _redirect_with_msg($flag, $msg)
	{
		$this->session->set_flashdata($flag, $msg);
		
		go_back();
	}
}

/* End of file Comments.php */
/* Location: ./application/controllers/admin/Comments.php */
