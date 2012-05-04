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
 * STBLOG Metas Class
 *
 * 本类用于Metas管理逻辑
 *
 * @package		STBLOG
 * @subpackage	Controller
 * @category	Admin Controller
 * @author		Saturn <huyanggang@gmail.com>
 * @link 		http://code.google.com/p/stblog/
 */
class Metas extends ST_Auth_Controller {
	
	/**
     * 传递到对应视图的数据
     *
     * @access private
     * @var array
     */
	private $_data = array();
	
	/**
     * 当前操作Meta的ID
     *
     * @access private
     * @var int
     */
	private $_mid = 0;
	
	/**
     * 当前操作Meta类型
     *
     * @access private
     * @var string
     */
	private $_type = 'category';

	/**
     * 中英文转化表
     *
     * @access private
     * @var array
     */
	private $_map = array('category' => '分类', 'tag' => '标签');

	public function __construct()
	{
		parent::__construct();
		
		/** privilege confirm */
		$this->auth->exceed('editor');
		
		/** 标题 */
		$this->_data['page_title'] = '分类与标签';
		$this->_data['parentPage'] = 'manage-posts';
		$this->_data['currentPage'] = 'manage-metas';
	}
	
	/**
     * 默认执行函数
     *
     * @access public
     * @return void
     */
	public function index()
	{
		redirect('admin/metas/manage');
	}
	
	
	/**
     * 管理页面
     *
     * @access public
     * @param  string $type 类型
     * @param  int 	  $mid  ID
     * @return void
     */
	public function manage($type = 'category', $mid = NUll)
	{
		$this->_data['type'] = $type;
		$this->_data[$type] = $this->metas_mdl->list_metas($type);

		if($mid && is_numeric($mid))
		{
			$this->_data['mid'] = $mid;
			
			$meta = $this->metas_mdl->get_meta('BYID', $mid);
			
			$this->_data['name'] = $meta->name;
			$this->_data['slug'] = $meta->slug;
			$this->_data['description'] = $meta->description;
			
			unset($meta);
		}
		
		$this->_operate($type, $mid);
		
		$this->load->view('admin/manage_metas',$this->_data);
	}

	/**
     * 新增和编辑标签/分类
     *
     * @access private
     * @param  string $type 类型
     * @param  int 	  $mid  ID
     * @return void
     */
	private function _operate($type, $mid)
	{
		/** 验证需要 */
		$this->_type = $type;
		$this->_mid = $mid;
		
		$this->_load_validation_rules($type);
		
		if($this->form_validation->run() === FALSE)
		{
			return;
		}
		else
		{
			$action = $this->input->post('do',TRUE);
			$name = $this->input->post('name',TRUE);
			$slug = $this->input->post('slug',TRUE);
			$description = $this->input->post('description',TRUE);
			
			$data = array(
				'name' => $name,
				'type' => $type,
				'slug' => Common::repair_slugName((!empty($slug))?$slug:$name),
				'description' => (!$description)? NULL : $description
			);
			
			
			if('insert' == $action)
			{
				$this->metas_mdl->add_meta($data);
				
				$this->session->set_flashdata('success', $this->_map[$type].'添加成功');
			}
			
			if('update' == $action)
			{
				$this->metas_mdl->update_meta($mid, $data);
				
				$this->session->set_flashdata('success', $this->_map[$type].'更新成功');
			}
			
			go_back();
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
		$this->form_validation->set_rules('name', '名称', 'required|trim|callback__name_check|callback__name_to_slug|htmlspecialchars');
		
		if('category' == $this->_type)
		{
			$this->form_validation->set_rules('slug', '缩略名', 'trim|callback__slug_check|alpha_dash|htmlspecialchars');
		}
		else
		{
			$this->form_validation->set_rules('slug', '缩略名', 'trim|callback__slug_check|htmlspecialchars');	
		}
		
		$this->form_validation->set_rules('description', '描述', 'trim|htmlspecialchars');	
	}

	/**
     * 操作分发
     *
     * @access public
     * @param  string $type 类型
     * @return void
     */
	public function operate($type)
	{
		$action = $this->input->post('do',TRUE);
		
		switch ($action)
		{
			case 'delete':
					$this->_remove($type);
					break;
			case 'refresh':
					$this->_refresh($type);
					break;
			case 'merge':
					$this->_merge($type);
					break;
			default:
					show_404();
					break;
		}
	}

	/**
     * 删除
     *
     * @access private
     * @param  string $type 类型
     * @return void
     */
	private function _remove($type)
	{
		$metas = $this->input->post('mid',TRUE);
        $deleted = 0;
        
        if ($metas && is_array($metas)) 
        {
            foreach ($metas as $meta) 
            {
                if($this->metas_mdl->remove_meta($meta))
                {
                	$this->metas_mdl->remove_relationship('mid',$meta);
                	$deleted ++;
                }
            }
        }
        
        $msg = ($deleted>0) ? $this->_map[$type].'删除成功' : '没有'.$this->_map[$type].'被删除';
        $notify = ($deleted>0) ? 'success':'error';
        
        $this->session->set_flashdata($notify, $msg);
		go_back();
	}

	/**
     * 刷新
     *
     * @access private
     * @param  string $type 类型
     * @return void
     */
	private function _refresh($type)
	{
		$metas = $this->input->post('mid',TRUE);
        
        if ($metas && is_array($metas)) 
        {
            foreach ($metas as $meta) 
            {
				$this->metas_mdl->refresh_count($meta, 'post', 'publish');
            }
            
            $this->session->set_flashdata('success', '分类刷新已经完成');
        }
        else
        {
        	$this->session->set_flashdata('error', '没有选择任何分类');
        }
        
        go_back();
	}
	
	/**
     * 合并
     *
     * @access private
     * @param  string $type 类型
     * @return void
     */
	private function _merge($type)
	{
		$metas = $this->input->post('mid',TRUE);
		
		if($metas && is_array($metas))
		{
			$merge = $this->input->post('merge',TRUE);

			if('tag' == $type)
			{
				$merge = $this->metas_mdl->scan_tags($merge);
				
				if(empty($merge))
				{
					$this->session->set_flashdata('error', '合并到的标签名不合法');
					redirect('admin/metas/manage/tag');
				}
			}
			
			$this->metas_mdl->merge_meta($merge, $type, $metas);
			
			$this->session->set_flashdata('success', $this->_map[$type].'已被合并');
		}
		else
		{
			$this->session->set_flashdata('error', '请选择需要合并到的'.$this->_map[$type]);
		}
		
		go_back();
	}
	
	 /**
     * 回调函数：检查Name是否唯一
     * 
     * @access public
     * @param $str 输入值
     * @return bool
     */
	public function _name_check($str)
	{
		if($this->metas_mdl->check_exist($this->_type, 'name', $str, $this->_mid))
		{
			$this->form_validation->set_message('_name_check', '已经存在一个为 '.$str.' 的名称');
			
			return FALSE;
		}
		
		return TRUE;
	}
	
	 /**
     * 回调函数：检查Slug是否唯一
     * 
     * @access public
     * @param $str 输入值
     * @return bool
     */
	public function _slug_check($str)
	{
		if($this->metas_mdl->check_exist($this->_type, 'slug', Common::repair_slugName($str), $this->_mid))
		{
			$this->form_validation->set_message('_slug_check', '已经存在一个为 '.$str.' 的缩略名');
			
			return FALSE;
		}
		
		return TRUE;
	}
	
	 /**
     * 回调函数：名称转化为缩略名
     * 
     * @access public
     * @param $str 输入值
     * @return bool
     */
	public function _name_to_slug($str)
	{
		$slug = Common::repair_slugName($str);
		
        if(empty($slug) || $this->metas_mdl->check_exist($this->_type, 'slug',$slug, $this->_mid)) 
        {
        	$this->form_validation->set_message('_name_to_slug', '分类无法转换为缩略名');
        	return FALSE;
        }
        
        return TRUE;
	}
	
}

/* End of file Metas.php */
/* Location: ./application/controllers/admin/Metas.php */