<?php
/**
 * @var $title
 * @var $caption
 * @var $title_position
 * @var $image
 * @var $size
 * @var $image_fallback
 * @var $alt
 * @var $url
 * @var $new_window
 */

$src = siteorigin_widgets_get_attachment_image_src(
	$image,
	$size,
	$image_fallback
);

$attr = array();
if( !empty($src) ) {
	$attr = array(
		'src' => $src[0],
	);

	if(!empty($src[1])) $attr['width'] = $src[1];
	if(!empty($src[2])) $attr['height'] = $src[2];
	//if (function_exists('wp_get_attachment_image_srcset')) {
		//$attr['srcset'] = wp_get_attachment_image_srcset( $image, $size );
 	//}
}
$attr = apply_filters( 'siteorigin_widgets_image_banner_attr', $attr, $instance, $this );

if(!empty($title)) $attr['title'] = $title;
if(!empty($alt)) $attr['alt'] = $alt;

if(!empty( $link_addimg ) && !empty($linksc)){
?>
	<a href="<?php echo $linksc; ?>" <?php if($new_window) echo 'target="_blank"' ?> class="addlinkimg" >
<?php } ?>
<div class="nbt-image-banner-widget <?php if(!empty($nbtclass)) : echo $nbtclass; endif; ?>">
	<img <?php foreach($attr as $n => $v) echo $n.'="' . esc_attr($v) . '" ' ?> />	
	<div class="nbt-image-banner-info">
		<?php if(!empty($txt_primary)) :?>
		<div class="txt_primary"><?php echo $txt_primary;?></div>
		<?php endif;?>	
		<?php if(!empty($title)) :?>
		<div class="title"><?php echo $title;?></div>
		<?php endif;?>
		<?php if(!empty($caption)) :?>
		<div class="caption"><?php echo $caption;?></div>
		<?php endif;?>
		<?php if(empty( $link_addimg ) && !empty($linksc) && !empty($txt_btn)){?>
			<a href="<?php echo $linksc; ?>" <?php if($new_window) echo 'target="_blank"' ?> class="btn-banner">		
				<?php echo $txt_btn;  ?>			
			</a>
		<?php }  
		if(empty( $link_addimg ) && empty($linksc) && !empty($url) && !empty($txt_btn)) { ?>
			<a href="<?php echo sow_esc_url($url) ?>" <?php if($new_window) echo 'target="_blank"' ?> class="btn-banner">
				<?php echo $txt_btn;  ?>			
			</a>
		<?php } ?>
	</div>
</div>
<?php if(!empty( $link_addimg ) && !empty($linksc)){ echo '</a>';}?>