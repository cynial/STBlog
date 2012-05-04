<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('json/Json.php');

/**
* 抛出json回执信息
* 
* @access public
* @param string $message 消息体
* @param string $charset 信息编码
* @return void
*/
function throwJson($message, $charset = NULL)
{
   /** 设置http头信息 */
  header('content-Type: application/json; charset=' . (empty($charset) ? 'UTF-8' : $charset), true);
  echo json::encode($message);
  /** 终止后续输出 */
  exit;
}

/* End of file Json_pi.php */
/* Location: ./application/plugins/Json_pi.php */ 