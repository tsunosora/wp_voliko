<?php
$widget_title =  wp_kses_post($instance['widget_title']);
$tabs_selection =  wp_kses_post($instance['tabs_selection']);
?>
<?php if($tabs_selection == 'horizontal'): ?>
<?php if ($widget_title) { ?>
    <h3 class="widget-title">
        <span><?php echo $widget_title ?></span>
    </h3>
<?php } ?>
<!-- tungpk bootstrap tabs -->
<div class="tab-so-widgets-bundle">    
    <ul class="nav nav-pills">

        <?php $ntitle=0;   
        
         $tabname=$instance['tab_name'];

        foreach( $instance['repeater'] as $i => $repeater ) : 
            if($ntitle==0){
                echo '<li class="active">';
            }else{
                echo '<li>';
            }
            ?>
            <?php
            echo '<a data-toggle="pill" href="#tab'.$tabname.$ntitle.'">';
                echo $repeater['tab_title']; ?>
            </a>

        </li>        
        <?php $ntitle ++;
         endforeach; ?>
    </ul> <!-- / tabs -->

    <div class="tab-content">
        
        <?php $ncontent=0;
        foreach( $instance['repeater'] as $i => $repeater ) : 

            if($ncontent==0){
                echo '<div id="tab'.$tabname.$ncontent.'" class="tab-pane fade in active">';
            }
            else{
                    echo '<div id="tab'.$tabname.$ncontent.'" class="tab-pane fade">';
                }
             echo $repeater['tab_content'] ?>

			</div>

        <?php $ncontent ++; endforeach; ?>

    </div> <!-- / tab_content -->
</div> <!-- / tab -->



<?php elseif($tabs_selection == 'vertical'): ?>

<!-- edit -->
<div id="rootwizard" class="tabbable tabs-left nbt-widget-tabs container">
<div class="row">

<?php $ntitle=0;   
       $tabname=$instance['tab_name'];?>
	   <div class="widget-vertical-tabs col-md-3">
	<?php if ($widget_title) { ?>
	<h3 class="widget-title">
        <span><?php echo $widget_title ?></span>
    </h3>
	<?php } ?>
	<ul class="tab-item-title">
	
	<?php  foreach( $instance['repeater'] as $i => $repeater ) : 
            if($ntitle==0){
                echo '<li class="active">';
            }else{
                echo '<li>';
            }
            ?>
            <?php
            echo '<a data-toggle="tab" href="#verticaltab'.$tabname.$ntitle.'">';
                echo $repeater['tab_title']; ?>
            </a>

        </li>        
        <?php $ntitle ++;
         endforeach; ?>
	  	
		
	</ul>
	<div class="block-tab-bottom">
	<?php echo $instance['tab_block'];?>
	</div>
	</div>
	<div class="tab-content col-md-9">
	<?php $ncontent=0;
        foreach( $instance['repeater'] as $i => $repeater ) : 

            if($ncontent==0){
                echo '<div id="verticaltab'.$tabname.$ncontent.'" class="tab-pane fade in active">';
            }
            else{
                    echo '<div id="verticaltab'.$tabname.$ncontent.'" class="tab-pane fade">';
                }
             echo $repeater['tab_content'] ?>

        </div> <!-- / tabs_item -->

        <?php $ncontent ++; endforeach; ?>
	    
	</div>	
</div>
</div>
<!-- end edit -->

<?php elseif($tabs_selection == 'accordion'):?>
   <div class="accordion-tabs-widget">
   <?php if ($widget_title) { ?>
	<h3 class="widget-title">
        <span><?php echo $widget_title ?></span>
    </h3>
	<?php } ?>
    <div class="panel-group" id="accordion">
        <?php $ncontent=0; $tabname=$instance['tab_name'];
        foreach( $instance['repeater'] as $i => $repeater ) : ?>
		<div class="panel">
            
            <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" <?php if($ncontent==0){echo 'aria-expanded="true"';}?> href="#collapse<?php echo $tabname.$ncontent;?>">
					<?php echo $repeater['tab_title']; ?></a>
            </h4>
            
            <div id="collapse<?php echo $tabname.$ncontent;?>" class="panel-collapse collapse <?php if($ncontent==0){echo 'in';}?>">
                <div class="panel-body">                    
					<?php echo $repeater['tab_content'] ;?>
					
                </div>
            </div>
        </div>
		<?php $ncontent ++; endforeach; ?>
       
    </div>
	
</div>
    
<?php endif; ?>