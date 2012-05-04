<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| 博客系统的相关常数
|--------------------------------------------------------------------------
|
|
|
*/
define('ST_THEMES_DIR', 							'themes');
define('ST_PLUGINS_DIR', 							'st_plugins');
define('ST_DB_CACHE_DIR', 							'dbcache');
define('ST_NAME', 									'STBlog');
define('ST_SALT_LENGTH', 							9);
define('ST_VERSION', 								'0.1.2');
define('ST_AUTHOR', 								'Saturn');
define('ST_AUTHOR_URL', 							'http://www.cnsaturn.com/');
define('ST_CONTENT_BREAK', 							'[--break--]');
/** 系统核心内部规定的插件钩子名称 */
define('ST_CORE_HOOK_EDITOR',						'Core::Editor');
define('ST_CORE_HOOK_EDITOR_INSERT_ATTACH',			'Core::Editor::Insert::Attach');
define('ST_CORE_HOOK_EDITOR_INSERT_MORE',			'Core::Editor::Insert::More');
define('ST_CORE_HOOK_COMMENT_PREPROCESS',			'Core::Comment::PreProcess');//评论获取用户数据后的前期插件处理钩子
define('ST_CORE_HOOK_TRACKBACK_PREPROCESS',			'Core::Trackback::PreProcess');
define('RSS1', 'RSS 1.0');
define('RSS2', 'RSS 2.0');
define('ATOM', 'ATOM');


/* End of file constants.php */
/* Location: ./application/config/constants.php */
