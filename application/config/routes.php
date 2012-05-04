<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['404_override'] = '';

$route['default_controller'] = "home";
$route['admin'] = "admin/login/index";
$route['page/(:num)'] = 'home/index/$1';
$route['posts/(:any)'] = 'posts/index/$1';
$route['category/(:any)'] = 'home/category/$1';
$route['comment/(:num)'] = 'comment/index/$1';
$route['trackback/(:num)'] = 'comment/trackback/$1';
$route['tag/(:any)'] = 'home/tag/$1';
$route['authors/(:any)'] = 'home/authors/$1';
$route['search'] = 'home/search';
$route['feed/(:any)'] = 'feed/index/$1';
//注意路由的顺序
$route['archives/(\d{4})'] = 'home/archives/$1/0/0/p1';
$route['archives/(\d{4})/(\d{2})'] = 'home/archives/$1/$2/0/p1';
$route['archives/(\d{4})/(\d{2})/(\d{2})'] = 'home/archives/$1/$2/$3/p1';
$route['archives/(\d{4})/p([0-9]+)'] = 'home/archives/$1/0/0/$2';
$route['archives/(\d{4})/(\d{2})/p([0-9]+)'] = 'home/archives/$1/$2/0/$3';
$route['archives/(\d{4})/(\d{2})/(\d{2})/p([0-9]+)'] = 'home/archives/$1/$2/$3/$4';
$route['pages/(:any)'] = 'pages/index/$1';



/* End of file routes.php */
/* Location: ./application/config/routes.php */
