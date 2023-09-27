<?php

/**
 * Filters checkbox list tree categories
 *
 * @since  1.0.0
 * @return WooPanel_Category_Checkbox_List_Tree
 */
class WooPanel_Category_Checkbox_List_Tree extends Walker_Category {

  /**
   * Walker_Category::start_el
   *
   * @since  1.0.0
   */
  public function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {

    extract($args);
    $cat_name = esc_attr( $category->name );
    $cat_name = apply_filters( 'list_terms', $cat_name, $category );

    if ( 'list' == $args['style'] ) {
      $output .= "\t<li";
      $class = "term-item term-item-". absint($category->term_id);
      $id = $taxonomy ."-". absint($category->term_id);
      $checked = in_array($category->term_id, $checked) ? "checked" : "";

      if ( !empty($current_category) ) {
        $_current_category = get_term( $current_category, $category->taxonomy );

        if ( $category->term_id == $current_category )
          $class .=  " current-term";
        elseif ( $category->term_id == $_current_category->parent )
          $class .=  " current-term-parent";
      }

      if ( count( get_term_children( $category->term_id, $category->taxonomy ) ) === 0 ) {
        $class .=  " term-item-has-children";
      }
      
      $output .=  " id='". esc_attr($id) ."' class='" . esc_attr($class) . "'";
      $output .= "><label class='m-checkbox m-checkbox--solid m-checkbox--brand'><input type='checkbox' name='".esc_attr($form_name)."[]' id='in-". esc_attr($id) ."' value='$category->term_id' $checked />&nbsp;$cat_name<span></span></label>";
    } else {
      $output .= "\t$cat_name\n";
    }
  }
}

/**
 * Filters checkbox list tree categories for Quick Edit post list table
 *
 * @since  1.0.0
 * @return WooPanel_Category_Checkbox_List_Tree
 */
class WooPanel_QuickEdit_Checkbox_List_Tree extends Walker_Category {

  /**
   * Walker_Category::start_el
   *
   * @since  1.0.0
   */
  public function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {

    extract($args);
    $cat_name = esc_attr( $category->name );
    $cat_name = apply_filters( 'list_terms', $cat_name, $category );

    if ( 'list' == $args['style'] ) {
      $output .= "\t<li";
      $class = "term-item term-item-". absint($category->term_id);
      $id = $taxonomy ."-". absint($category->term_id);
      $checked = in_array($category->term_id, $checked) ? "checked" : "";

      if ( !empty($current_category) ) {
        $_current_category = get_term( $current_category, $category->taxonomy );

        if ( $category->term_id == $current_category )
          $class .=  " current-term";
        elseif ( $category->term_id == $_current_category->parent )
          $class .=  " current-term-parent";
      }

      if ( count( get_term_children( $category->term_id, $category->taxonomy ) ) === 0 ) {
        $class .=  " term-item-has-children";
      }
      
      $output .=  " id='". esc_attr($id) ."' class='" . esc_attr($class) . "'";
      $output .= "><label class='m-checkbox'><input type='checkbox' name='".esc_attr($form_name)."[]' id='in-". esc_attr($id) ."' value='$category->term_id' $checked />&nbsp;$cat_name<span></span></label>";
    } else {
      $output .= "\t$cat_name\n";
    }
  }
}
