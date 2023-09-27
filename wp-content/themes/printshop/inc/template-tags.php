<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Netbase
 */

/**
 * Render WordPress title ( Backwards compatibility for WP version < 4.1 )
 */
if ( ! function_exists( '_wp_render_title_tag' ) ) :
    function printshop_render_title() {
	?>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php
    }
    add_action( 'wp_head', 'printshop_render_title' );
endif;

/**
 * Get header base on user select.
 */
function printshop_get_header() {

	global $printshop_option;
	$header_name = null;

	if ( isset( $printshop_option['header_style'] ) ) {
		$header_layout = esc_html($printshop_option['header_style']);
	} else {
		$header_layout = '';
	}

	if ( 
		isset($printshop_option['header_style']) &&
		(
			$printshop_option['header_style'] == 'logoleft' 
			|| $printshop_option['header_style'] == 'centered' 
			|| $printshop_option['header_style'] == 'menubottom' 
			|| $printshop_option['header_style'] == 'creativeleft' 
			|| $printshop_option['header_style'] == 'creativeright' 
			|| $printshop_option['header_style'] == 'banner'
		)
	) {
		$header_name = esc_html($printshop_option['header_style']);
	} else {
		$header_name = '';
	}

	if ( isset( $_REQUEST['header-demo']) && ($_REQUEST['header-demo'] == 'logoleft' ) ) $header_name = 'logoleft';
	if ( isset( $_REQUEST['header-demo']) && ($_REQUEST['header-demo'] == 'centered' ) ) $header_name = 'centered';

	return get_header( $header_name );
}

/**
 * Display logo base on page settings.
 */
function printshop_logo_render(){
	global $post;
	global $woocommerce;
	
	$logo_url = printshop_get_option('site_logo', false, 'url');	
	
	return $logo_url;
}

/**
 * Display the page header at the top of single page.
 */
function printshop_get_page_header($postID) {
	// Page Header CSS
	$page_header_style = array();

	$page_header_style = implode('', $page_header_style);
	if ( $page_header_style ) {
		$page_header_style = wp_kses( $page_header_style, array() );
		$page_header_style = ' style="' . esc_attr($page_header_style) . '"';
	}
	
}

/**
 * Display navigation to next/previous set of posts when applicable.
 */
function printshop_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset ( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'printshop'     => $pagenum_link,
		'format'    => $format,
		'total'     => $GLOBALS['wp_query']->max_num_pages,
		'current'   => $paged,
		'mid_size'  => 1,
		'add_args'  => array_map( 'urlencode', $query_args ),
		'prev_text' =>  wp_kses(__('<i class=\'fa fa-chevron-left\'></i>', 'printshop' ), array('i' => array('class' => array()))),
		'next_text' => wp_kses(__( '<i class=\'fa fa-chevron-right\'></i>', 'printshop' ), array('i' => array('class' => array()))),
	) );

	if ( $links ) :

	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'printshop' ); ?></h1>
		<div class="pagination loop-pagination">
			<?php echo wp_kses($links, array(
				'a' => array(
					'href' => array(),
					'class' => array()
				),
				'i' => array(
					'class' => array()
				),
				'span' => array(
					'class' => array()
				)
			)); ?>
		</div><!--/ .pagination -->
	</nav><!--/ .navigation -->
	<?php
	endif;
}


if ( ! function_exists('printshop_post_nav') ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function printshop_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'printshop' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&</span>&nbsp;%title', 'Previous post link', 'printshop' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     'printshop' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists('printshop_posted_on') ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function printshop_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( ' on %s', 'post date', 'printshop' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	if ( is_sticky( ) ) {
		echo wp_kses(__('<span class="nbt-sticky-post">Sticky</span>', 'printshop'), array('span' => array('class' => array())));
	}
  if(get_the_author()){
  	$byline = sprintf(
  		_x( '<i class="fa fa-user"></i> %s', 'post author', 'printshop' ),
  		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
  	);
    echo '<span class="byline">'. $byline . '</span>';
  }
	echo '<span class="posted-on"> <i class="fa fa-calendar-o"></i>' . $posted_on . '</span>';

	$categories_list = get_the_category_list( esc_html__( ', ', 'printshop' ) );
  if ( $categories_list && printshop_categorized_blog() ) {
		printf( '<span class="categories-links">' . esc_html__( '%1$s', 'printshop' ) . '</span>', $categories_list );
	}

	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">'. wp_kses(__(' <i class="fa fa-comment-o"></i> ', 'printshop'), array('i' => array('class' => array())));
		comments_popup_link( esc_html__( '0', 'printshop' ), esc_html__( '1', 'printshop' ), esc_html__( '%', 'printshop' ) );
		echo '</span>';
	}
	if ( has_tag() ){	
      echo '<span class="tags-links">';
      printf( esc_html__('%1$s', 'printshop'), get_the_tag_list( '', ', ' ) );
      echo '</span>';
	}

}
endif;


if ( ! function_exists('printshop_entry_footer') ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function printshop_entry_footer() {
	
	$blog_single_author = printshop_get_option('blog_single_author');
	?>
	
	<?php
	$category_list = get_the_category_list();
	$tag_list      = get_the_tag_list( '<ul class="post-tags"><li>', "</li>\n<li>", '</li></ul>' );
	$meta_text     = '';

	if ( $category_list ) {
		$meta_text .= wp_kses(__( '<i class="fa fa-file"></i> ', 'printshop' ), array('i' => array('class' => array()))) . '%1$s';
	}
	if ( $tag_list ) {
		$meta_text .= wp_kses(__( '<i class="fa fa-tag"></i> ', 'printshop' ), array('i' =>array('class' => array()))) . '%2$s';
	}
	printf(
		$meta_text,
		$category_list,
		$tag_list,
		get_permalink()
	);
	?>
	
	<?php if ( $blog_single_author ) { ?>
	<div class="entry-author clearfix">
		<div class="entry-author-avatar">
			<?php
			printf(
				'<a class="vcard" href="%1$s">%2$s</a>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_avatar( get_the_author_meta( 'ID' ) )
			);
			?>
		</div>
		<div class="entry-author-byline">
			<?php
			printf(
				_x( 'Written by %s', 'author byline', 'printshop' ),
				sprintf(
					'<a class="vcard" href="%1$s">%2$s</a>',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_html( get_the_author_meta( 'display_name' ) )
				)
			);
			?>
		</div>
		<?php if ( is_singular() && $author_bio = get_the_author_meta( 'description' ) ) : ?>
		<div class="entry-author-bio">
			<?php echo wpautop( ( $author_bio ) ); ?>
		</div>
		<?php endif; ?>
	</div>
	<?php } ?>

	<?php
}
endif;


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function printshop_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'wpnetbase_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'wpnetbase_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so printshop_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so printshop_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in printshop_categorized_blog.
 */
function printshop_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'wpnetbase_categories' );
}
add_action( 'edit_category', 'printshop_category_transient_flusher' );
add_action( 'save_post',     'printshop_category_transient_flusher' );

/**
 * Let the sidebar display by Theme Option and Page Settings.
 */
function printshop_get_sidebar() {
	global $post;
	global $woocommerce;
	$post_type              = get_post_type($post);
	$archive_layout_setting = printshop_get_option('archive_layout');
	$page_layout_admin      = printshop_get_option('page_layout');
	$blog_layout_admin      = printshop_get_option('blog_layout');
	$single_shop_layout     = printshop_get_option('single_shop_layout');
	$single_project_layout  = printshop_get_option('project_layout');

	// Pages
	if ( is_singular('page') ){
		$page_layout_meta       = esc_html( get_post_meta(  $post->ID, 'sidebar_option', true) );
		if ( $page_layout_meta == '' || $page_layout_meta == 'sidebar-default' ) {
			if ( $page_layout_admin == '' || $page_layout_admin == 'left-sidebar' || $page_layout_admin == 'right-sidebar' ) {
				get_sidebar();
			}
		} else {
			if ( $page_layout_meta == 'right-sidebar' || $page_layout_meta == 'left-sidebar' ) {
				get_sidebar();
			}
		}
	}

	// Single Post
	if ( is_single() && ($post_type != 'product') && ($post_type != 'portfolio') ) {
		if ( $blog_layout_admin == 'right-sidebar' || $blog_layout_admin == 'left-sidebar' ) {
			get_sidebar();
		}
	}

	
	// Archive
	if ( ( (is_archive() || is_author()) && $post_type == 'post' ) && !is_front_page() ) {
		
		if ( $archive_layout_setting == 'right-sidebar' || $archive_layout_setting == 'left-sidebar' ) {
			get_sidebar(); 
		}		
	}

	// Search
	if ( is_search() ) {
		if ( $archive_layout_setting == 'right-sidebar' || $archive_layout_setting == 'left-sidebar' ) {
			get_sidebar();
		}
	}

	// WooCommerce
	if ( $woocommerce ) {
		$shop_layout_meta       = esc_html( get_post_meta( wc_get_page_id('shop'), 'sidebar_option', true) );
		if ( is_shop() || is_product_category() || is_product_tag() ) {
			if ( $shop_layout_meta == 'right-sidebar' || $shop_layout_meta == 'left-sidebar' ) {
				get_sidebar();
			}
		}
		if( is_product() ) {
			if ( $single_shop_layout == 'right-sidebar' || $single_shop_layout == 'left-sidebar' ) {
				get_sidebar();
			} else {
				// No Sidebar
			}
		} 
		
		
	}
}
/**
 * Let the sidebar display on frontpage if Front page set as latest post
 */
function printshop_frontpage_sidebar() {
	$display_sidebar        = false;
	$archive_layout_setting = printshop_get_option('archive_layout');
	$blog_layout_setting    = printshop_get_option('blog_layout');

	if ( is_front_page() ) {
		if ( $archive_layout_setting == 'right-sidebar' || $archive_layout_setting == 'left-sidebar' ) {
			$display_sidebar = true;
		} else {
			$display_sidebar = false;
		}
	} 

	if ( !is_front_page() && is_home() ) {
		if ( $blog_layout_setting == 'right-sidebar' || $blog_layout_setting == 'left-sidebar' ) {
			$display_sidebar = true;
		} else {
			$display_sidebar = false;
		}
	}

	if ( $display_sidebar ) {
		get_sidebar();
	}
}


if ( ! function_exists('printshop_comment') ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own printshop_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @return void
 */
function printshop_comment($comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
        // Display trackbacks differently than normal comments.
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <p><?php esc_html_e( 'Pingback:', 'printshop' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'printshop' ), '<span class="edit-link">', '</span>' ); ?></p>
    <?php
            break;
        default :
        // Proceed with normal comments.
        global $post;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment clearfix">

            <?php echo get_avatar( $comment, 60 ); ?>

            <div class="comment-wrapper">
            
                <header class="comment-meta comment-author vcard">
                    <?php
                        printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
                            get_comment_author_link(),
                            // If current post author is also comment author, make it known visually.
                            ( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', 'printshop' ) . '</span>' : ''
                        );
                        printf( '<a class="comment-time" href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                            esc_url( get_comment_link( $comment->comment_ID ) ),
                            get_comment_time( 'c' ),
                            /* translators: 1: date, 2: time */
                            sprintf( esc_html__( '%1$s', 'printshop' ), get_comment_date() )
                        );
                        comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'printshop' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
                        edit_comment_link( esc_html__( 'Edit', 'printshop' ), '<span class="edit-link">', '</span>' );
                    ?>
                </header><!-- .comment-meta -->

                <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'printshop' ); ?></p>
                <?php endif; ?>

                <div class="comment-content entry-content">
                    <?php comment_text(); ?>
                    <?php  ?>
                </div><!-- .comment-content -->

            </div><!--/comment-wrapper-->

        </article><!-- #comment-## -->
    <?php
        break;
    endswitch; // end comment_type check
}
endif;

/**
 * Output html5 js file for ie9.
 */
function printshop_html5() {
	echo '<!--[if lt IE 9]>';
	echo '<script src="'. esc_url( get_template_directory_uri() ) .'/assets/js/html5.min.js"></script>';
	echo '<![endif]-->';
}
add_action( 'wp_head', 'printshop_html5' );

/**
 * Output site favicon to wp_head hook.
 */
function printshop_favicons() {
	$favicons = null;

	if ( printshop_get_option('site_favicon', '', 'url') ) $favicons .= '
	<link rel="shortcut icon" href="'. esc_url(printshop_get_option('site_favicon', '', 'url')) .'">';

	if ( printshop_get_option('site_iphone_icon', '', 'url') ) $favicons .= '
	<link rel="apple-touch-icon-precomposed" href="'. esc_url(printshop_get_option('site_iphone_icon', '', 'url')) .'">';

	if ( printshop_get_option('site_iphone_icon_retina', '', 'url') ) $favicons .= '
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'. esc_url(printshop_get_option('site_iphone_icon_retina', '', 'url')) .'">';

	if ( printshop_get_option('site_ipad_icon', '', 'url') ) $favicons .= '
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="'. esc_url(printshop_get_option('site_ipad_icon', '', 'url')) .'">';

	if ( printshop_get_option('site_ipad_icon_retina', '', 'url') ) $favicons .= '
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'. esc_url(printshop_get_option('site_ipad_icon_retina', '', 'url')) .'">';

	printf("%s", $favicons);
}
add_action( 'wp_head', 'printshop_favicons' );

/**
 * Modified Gallery Shortcode
 */

function printshop_post_gallery($output, $attr) {
    global $post;

    static $instance = 0;
    $instance++;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 3,
        'size'       => 'printshop-medium-thumb',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $size = 'printshop-medium-thumb';

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? (100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";


    // Custom Lightbox
    $gallery_lightbox = null;
    if ( isset( $attr['link'] ) && $attr['link'] == 'file' ) { 
    	wp_enqueue_style( 'wpnetbase-magnific-style' );
    	$gallery_lightbox = 'gallery-lightbox';
    }
    

    $gallery_style = $gallery_div = '';
    if ( apply_filters( 'use_default_gallery_style', true ) )
        $gallery_style = "
        <style type='text/css'>
            #{$selector} .gallery-item {
                float: {$float};
                text-align: center;
                width: {$itemwidth}%;
                margin-bottom:0;
            }
            #{$selector} img {
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
        </style>";

    if ( isset( $attr['link'] ) && $attr['link'] == 'file' ) { 
    	$gallery_style .= "
        <script type='text/javascript'>
        	jQuery(document).ready(function() {
				jQuery('.galleryid-{$id}').magnificPopup({
					delegate: '.gallery-item a',
					type: 'image',
					gallery:{
						enabled:true
					},
					zoom: {
						enabled:true
					}
				});
			});
        </script>";
    }

    $size_class = sanitize_html_class( $size );
    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} {$gallery_lightbox}'>";
    
    $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-icon'>
                $link
            </{$icontag}>";
        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <{$captiontag} class='wp-caption-text gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
        if ( $columns > 0 && ++$i % $columns == 0 )
            $output .= '<div class="clear"></div>';
    }

    $output .= '
            <div class="clear"></div>
        '."</div>\n";

    return $output;
}
add_filter("post_gallery", "printshop_post_gallery",10,2);


/**
 * Output BreadCrumb.
 */
function printshop_breadcrumb() {
	if( function_exists('bcn_display') ) {
		if ( is_front_page() && is_home() ) {
			// Default homepage
		} elseif ( is_front_page() ) {
			// static homepage
		} elseif ( is_home() ) {
			// blog page
			?>
			<div class="breadcrumbs">
				<div class="container">
					<?php bcn_display(); ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="breadcrumbs">			
				<span><?php echo esc_html__('You are here:', 'printshop');?></span>
				<?php bcn_display(); ?>				
			</div>
			<?php
		}
	}
}

/**
 * Display list child page
 */
function printshop_list_child_pages($pageID, $order, $orderby, $exclude, $layout, $column, $number, $readmore_text ) {

	if ( $readmore_text == '' ) {
		$readmore_text = esc_html__('Read More', 'printshop');
	}

	$col_class = $thumbnail = '';
	if ( $column == 2 ) {
		$col_class = "grid-sm-6";
	} elseif ( $column == 3 ){
		$col_class = "grid-sm-6 grid-md-4";
	} elseif ( $column == 4 ) {
		$col_class = "grid-sm-6 grid-md-3";
	} else {
		$col_class = "grid-sm-6 grid-md-4";
	}
	$output = '';
	$count  = 0;
	$args = array(
		'posts_per_page' => $number,
		'post__not_in'   => $exclude,
		'post_parent'    => $pageID,
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'order'          => $order,
		'orderby'        => $orderby
		
	);
	$page_childrens = new WP_Query( $args );

	$carousel_class = '';
	if ( $layout == 'carousel' ) {
		$carousel_class = 'carousel-wrapper-'.uniqid();
			$output .= '
			<script type="text/javascript">
				jQuery(document).ready(function(){
					"use strict";
					jQuery(".'. $carousel_class .'").slick({
						slidesToShow: '. $column .',
						slidesToScroll: 1,
						draggable: false,
						prevArrow: "<span class=\'carousel-prev\'><i class=\'fa fa-angle-left\'></i></span>",
        				nextArrow: "<span class=\'carousel-next\'><i class=\'fa fa-angle-right\'></i></span>",
        				responsive: [{
						    breakpoint: 1024,
						    settings: {
						    slidesToShow: '. $column .'
						    }
						},
						{
						    breakpoint: 600,
						    settings: {
						    slidesToShow: 2
						    }
						},
						{
						    breakpoint: 480,
						    settings: {
						    slidesToShow: 1
						    }
						}]
					});
				});
			</script>';
	}

	if ( $page_childrens->have_posts() ) :

		$output .= '
		<div class="grid-wrapper grid-'.$column.'-columns grid-row '. $carousel_class .'">';

		while ( $page_childrens->have_posts() ) : $page_childrens->the_post(); $count++;

			$output .= '
			<div class="grid-item '. $col_class .'">';

				if( has_post_thumbnail() ) {
				$output .= '
				<div class="grid-thumbnail">
					<a href="'. esc_url(get_the_permalink()) .'" title="'. esc_html(get_the_title()) .'">'. get_the_post_thumbnail( get_the_ID(), 'printshop-medium-thumb') .'</a>
				</div>';
				}
				
				$output .= '
				<h3 class="grid-title"><a href="'. esc_url(get_the_permalink()) .'" rel="bookmark">'. esc_html(get_the_title()) .'</a></h3>

				<p>'. esc_html(get_the_excerpt()) .'</p>

				<a class="grid-more" href="'. esc_url(get_the_permalink()) .'" title="'. esc_html(get_the_title()) .'">'. esc_attr($readmore_text) .'</a>

			</div>
			';
			if ( $layout == 'grid' ) {
				if ( $count % $column == 0 ) $output .= '
				<div class="clear"></div>';
			}

		endwhile;

		$output .= '
		</div>';

		else:
			$output .= esc_html__( 'Sorry, there is no child pages under your selected page.', 'printshop' );
	endif;

	wp_reset_postdata();

	return $output;

}

