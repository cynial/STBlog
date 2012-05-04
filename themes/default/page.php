<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->view('header');
?>
    <div id="main"><!-- main -->
    		 <div class="post">
    				
    				<h3><strong><?php echo $post->title;?></strong></h3>
    				
    				<h4 class="metadata">
						<span>创建于: <?php echo $post->published; ?></span>
						<span class="no-border">最后更新: <?php echo $post->modified;?></span>
					</h4>
					
					<div class="content">
						<?php echo $post->content;?>					
					</div>
					
					<hr />
				</div>


			<?php $this->load->view('comments');?>


    </div><!-- //main -->
            
<?php $this->load->view('sidebar');?>
<?php $this->load->view('footer');?>