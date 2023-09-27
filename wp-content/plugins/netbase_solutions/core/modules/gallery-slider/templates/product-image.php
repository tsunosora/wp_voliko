
<div class="images twist-wrap <?php echo $class;?><?php if(isset($settings['wc_gallery_slider_direction'])){echo ' nbt-gallery-'.$settings['wc_gallery_slider_direction'];}else{echo ' nbt-gallery-horizontal';}?>" data-gallery_layout="<?php if(isset($settings['wc_gallery_slider_direction']) && $settings['wc_gallery_slider_direction'] == 'vertical'){ echo 'true';}else{echo 'false';}?>">
	<?php if($attachment_ids){?>
	<div class="twist-pgs">
		<?php if ( $attachment_ids && has_post_thumbnail() ) {
			$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			$full_size_image_thumb = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			$image_title     = get_post_field( 'post_excerpt', $thumbnail_id );
			$thumb_attributes = array(
				'class' => 'attachment-shop_single size-shop_single wp-post-image',
				'title'                   => $image_title,
				'data-src'                => $full_size_image_thumb[0],
				'data-large_image'        => $full_size_image_thumb[0],
				'data-large_image_width'  => $full_size_image_thumb[1],
				'data-large_image_height' => $full_size_image_thumb[2],
			);?>
			<div class="woocommerce-product-gallery__image wc-product-main-image">
				<a  class="venobox" href="<?php echo $full_size_image_thumb[0];?>" data-title="<?php echo $image_title;?>" data-gall="pgs-thumbs" >
					<?php echo wp_get_attachment_image( $thumbnail_id, 'shop_single', false, $thumb_attributes );?>
				</a>
			</div>
			<?php
			foreach ( $attachment_ids as $k => $attachment_id ) {
				$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
				$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
				$image_title     = get_post_field( 'post_excerpt', $attachment_id );
				$thumb_attributes = array(
					'class' => 'attachment-shop_single size-shop_single wp-post-image',
					'title'                   => $image_title,
					'data-src'                => $full_size_image[0],
					'data-large_image'        => $full_size_image[0],
					'data-large_image_width'  => $full_size_image[1],
					'data-large_image_height' => $full_size_image[2],
				);
				?>
			<div class="woocommerce-product-gallery__image wc-product-gallery-image">
				<a  class="venobox" href="<?php echo $full_size_image[0];?>" data-title="<?php echo $image_title;?>" data-gall="pgs-thumbs" >
					<?php echo wp_get_attachment_image( $attachment_id, 'shop_single', false, $thumb_attributes );?>
				</a>
			</div>
				<?php
			}
		}?>
	</div>
	<div class="slider-nav" id="slide-nav-pgs">
		<?php if ( $attachment_ids && has_post_thumbnail() ) {
			$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			$full_size_image_thumb = wp_get_attachment_image_src( $thumbnail_id, 'shop_thumbnail' );
			$image_title     = get_post_field( 'post_excerpt', $thumbnail_id );
			$thumb_attributes = array(
				'class' => 'attachment-shop_single size-shop_single wp-post-image',
				'title'                   => $image_title,
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);

			$newWidth = 157;
			$newHeight = 157;

			$first_thumb = preg_replace(
			   array('/width="\d+"/i', '/height="\d+"/i'),
			   array(sprintf('width="%d"', $newWidth), sprintf('height="%d"', $newHeight)),
			   wp_get_attachment_image( $thumbnail_id, 'shop_single', false, $thumb_attributes ));

			?>
			<div>
				<a class="product-gallery__image_thumb" data-title="<?php echo $image_title;?>" data-gall="pgs-thumbs" data-href="<?php echo $full_size_image[0];?>"><img width="157" height="157" src="<?php echo $full_size_image_thumb[0];?>"></a>
			</div>
			<?php
			foreach ( $attachment_ids as $k => $attachment_id ) {
				$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				$image_title     = get_post_field( 'post_excerpt', $attachment_id );
				?>
				<div>
					<a href="javascript:;" data-title="<?php echo $image_title;?>" data-gall="pgs-thumbs"><img src="<?php echo $thumbnail[0];?>" ></a>
				</div>

				<?php
			}
		}?>
	</div>
	<?php }else{
			$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			$full_size_image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
			$image_title     = get_post_field( 'post_excerpt', $thumbnail_id );
			$thumb_attributes = array(
				'class' => 'attachment-shop_single size-shop_single wp-post-image',
				'title'                   => $image_title,
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);?>
			<div class="woocommerce-product-gallery__image wc-product-main-image">
				<a  class="venobox" href="<?php echo $full_size_image[0];?>" data-title="<?php echo $image_title;?>" data-gall="pgs-thumbs" >
					<?php echo wp_get_attachment_image( $thumbnail_id, 'shop_single', false, $thumb_attributes );?>
				</a>
			</div>
	<?php }?>
</div>