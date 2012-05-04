<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_URI extends CI_URI
{
	
	/**
	 * Filter segments for malicious characters
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _filter_uri($str)
	{
		if ($str != '' && $this->config->item('permitted_uri_chars') != '')
		{
			if ( ! preg_match("/^[\%\.\:\~A-Za-z0-9_\-\x{4e00}-\x{9fa5}]+$/u", $str))
			{
				show_error('The URI you submitted has disallowed characters.', 400);
			}
		}

		// Convert programatic characters to entities
		$bad	= array('$', 		'(', 		')',	 	'%28', 		'%29');
		$good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');

		return str_replace($bad, $good, $str);
	}

}