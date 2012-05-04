<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * STBlog
 *
 * 基于Codeigniter的单用户多权限开源博客系统
 * 
 *
 * @package		STBLOG
 * @author		Saturn
 * @copyright	Copyright (c) 2009 - 2010, cnsaturn.com.
 * @license		GNU General Public License 2.0
 * @link		http://code.google.com/p/stblog/
 * @since		Version 0.1
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * STBLOG Medias Class
 *
 * 本类用于附件管理逻辑
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Medias extends ST_Auth_Controller {
	
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
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		
		$this->auth->exceed('contributor');
		
		/** common data */
		$this->_data['parentPage'] = 'manage-posts';
		$this->_data['currentPage'] = 'manage-medias';
		$this->_data['page_title'] = '管理附件';
	}

	/**
     * 默认执行函数
     *
     * @access public
     * @return void
     */
	public function index()
	{
		redirect('admin/medias/manage');
	}
	
	/**
     * 管理页面
     *
     * @access public
     * @param  string $status 状态
     * @return void
     */
	public function manage($status = 'attached')
	{
		$query = array();
		
		/** 标题过滤，用于搜索 */
		$filter = $this->input->get('keywords',TRUE);
		if(!empty($filter))
		{
			$this->_data['page_title'] = '搜索附件：'.$filter;	
			
			$query[] = 'keywords='.$filter;
		}
		
		/** 分页设置 */
		$page = $this->input->get('p',TRUE);
		$page = (!$page && is_numeric($page))?$page:1;
		$limit = 10;
		$offset = ($page - 1) * $limit;
		$pagination = '';
		
		if($offset < 0)
		{
			redirect('admin/medias/manage');
		}

		$attachments = $this->posts_mdl->get_posts('attachment', $status, NULL, $limit, $offset, 0, $filter);
		
		$attachments_count = $this->posts_mdl->get_posts('attachment', $status, NULL, 10000, 0, 0, $filter)->num_rows();
		
		if($attachments->num_rows() >0)
		{
			foreach($attachments->result() as $attachment)
			{
				$attachment->parentPost = NULL;
				
				if($attachment->order && is_numeric($attachment->order))
				{
					$attachment->parentPost = $this->posts_mdl->get_post_by_id('pid', $attachment->order);
				}
			}
			
			if($attachments_count > $limit)
			{	
				$this->dpagination->currentPage($page);
				$this->dpagination->items($attachments_count);
				$this->dpagination->limit($limit);
				$this->dpagination->adjacents(5);
				$this->dpagination->target(site_url('admin/medias/manage/'. $status .'?'.implode('&',$query)));
				$this->dpagination->parameterName('p');
				$this->dpagination->nextLabel('下一页');
				$this->dpagination->PrevLabel('上一页');
				
				$pagination = $this->dpagination->getOutput();
			}
		}
		
		$this->_data['attachments'] = $attachments;
		$this->_data['pagination'] = $pagination;
		$this->_data['status'] = $status;
		
		$this->load->view('admin/manage_medias',$this->_data);
	}

	 /**
     * 操作附件
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
			case 'delete':
				$this->_delete();
				break;
			default:
				show_404();
				break;
		}
	}

	 /**
     * 附件详细
     * 
     * @access public
     * @return void
     */
	public function detail($pid = 0)
	{
		if(empty($pid))
		{
			redirect('admin/metas/manage');
		}
		
		$attachment = $this->posts_mdl->get_post_by_id('pid', $pid);

		$this->_data['page_title'] = '编辑附件：'. $attachment->title;
		$this->_data['attachment'] = $attachment;
		
		$this->load->view('admin/media',$this->_data);
	}

	 /**
     * 删除附件
     * 
     * @access private
     * @return void
     */
	private function _delete()
	{
		$attachments = $this->input->post('pid',TRUE);
		
		$deleted = 0;
		
		if($attachments)
		{
			if(is_array($attachments))
			{
				foreach($attachments as $pid)
				{
					if($this->_exec_delete($pid))
					{
						$deleted++;
					}
				}
			}
			else
			{
				$this->_exec_delete($attachments);
			}
		}
		
		/** 删除请求来自ajax */
		$from = $this->input->post('from', TRUE);
		if($from && 'ajax' == $from)
		{
			return;
		}
		
		($deleted >0)
				?$this->_redirect_with_msg('success', '成功删除附件')
				:$this->_redirect_with_msg('error', '没有附件被删除');

		
	}

	 /**
     * 执行删除操作
     * 
     * @access private
     * @param  object $pid 附件ID
     * @return bool
     */
	public function _exec_delete($pid)
	{
		$attachment = $this->posts_mdl->get_post_by_id('pid', $pid);
				
		if($attachment && $this->_is_writable($attachment))
		{
			$info = unserialize($attachment->text);
		
			@unlink($info['path']);
			
			$this->posts_mdl->remove_post($pid);
			
			//fix issue 3#2
			return TRUE;
		}
		
		return FALSE;
	}

	 /**
     * 是否可写
     * 
     * @access private
     * @param  object $comment 附件对象
     * @return bool
     */
	private function _is_writable($attachment)
	{
		if($this->auth->exceed('editor',TRUE))
		{
			return TRUE;
		}
		
		if($attachment->authorId == $this->user->uid)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	private function _redirect_with_msg($flag, $msg)
	{
		$this->session->set_flashdata($flag, $msg);
		
		go_back();
	}
	
}

/* End of file Medias.php */
/* Location: ./application/controllers/admin/Medias.php */