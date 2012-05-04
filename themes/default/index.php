<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 Theme Name: default
 Theme URI: http://www.cnsaturn.com
 Description: 这是Stblog的第一个皮肤
 Version: 0.1
 Author: Saturn
 Author Email: huyanggang@gmail.com
*/

$this->load->view('header');
?>

    <div id="main"><!-- main -->
      
      <?php $this->load->view('posts');?>
     
    </div><!-- //main -->
            
<?php $this->load->view('sidebar');?>
<?php $this->load->view('footer');?>
