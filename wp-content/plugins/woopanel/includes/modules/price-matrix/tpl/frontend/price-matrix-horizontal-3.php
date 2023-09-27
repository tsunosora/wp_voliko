<?php
$order_attributes = get_post_meta($product->get_id(), '_pm_order_attributes', true);
$attribute_one = woopanel_price_matrix_attribute_label($_pm_attr['horizontal'][0], $product->get_id(), $order_attributes);
$attribute_two = woopanel_price_matrix_attribute_label($_pm_attr['horizontal'][1], $product->get_id(), $order_attributes);
$attribute_three = woopanel_price_matrix_attribute_label($_pm_attr['vertical'][0], $product->get_id(), $order_attributes);
$default_attributes = $product->get_default_attributes();
?>
<div class="table-responsive">
    <table class="pure-table price-matrix-table horizontal-3">
        <tbody>
            <tr>
                <td class="attr-name colspan" rowspan="2"></td>
                <?php foreach ($attribute_one as $kat => $attr_one) :?>
                <td class="attr-name heading-center" colspan="<?php echo count($attribute_two);?>"><?php echo esc_attr($attr_one->name);?></td>
                <?php endforeach;?>
            </tr>
            <?php
            end($attribute_one);
            $last_one = key($attribute_one);
            foreach ($attribute_one as $k_one => $attr_one) :
            if($k_one == 0):
                echo '<tr>';
            endif;?>
                <?php foreach ($attribute_two as $k_two => $attr_two) :?>
                <td class="attr-name text-center"><?php echo esc_attr($attr_two->name);?></td>
                <?php endforeach;?>
     
            <?php
            if($k_one == $last_one):
                echo '</tr>';
            endif;
            endforeach;?>

     
            <?php foreach ($attribute_three as $k_three => $attr_three) :?>
            <tr>
                <td class="attr-name"><?php echo esc_attr($attr_three->name);?></td>
                <?php
                foreach ($attribute_one as $k_one => $attr_one) :
                    $id = '';
                    foreach ($attribute_two as $k_two => $attr_two) :
                        $hover_detail = '';
                        $group_attr = array(
                            array(
                                'name' => $attr_one->taxonomy,
                                'value' => $attr_one->slug
                            ),
                            array(
                                'name' => $attr_two->taxonomy,
                                'value' => $attr_two->slug
                            ),
                            array(
                                'name' => $attr_three->taxonomy,
                                'value' => $attr_three->slug
                            )
                        );
                        $id = md5( esc_attr( $attr_one->taxonomy ) . esc_attr( $attr_one->slug ) . esc_attr( $attr_two->taxonomy ) . esc_attr( $attr_two->slug ). esc_attr( $attr_three->taxonomy ) . esc_attr( $attr_three->slug ) );
                        
                        if($deprived){
                            $group_attr = array_merge($group_attr, $deprived);
                        }

                        $count_default = 0;
                        foreach( $group_attr as $k => $v ) {
                            
                            if( isset($default_attributes[$v['name']]) ) {
                                if( $default_attributes[$v['name']] == $v['value'] ) {
                                    $count_default += 1;
                                }
                            }
                        }
                        
                        $selected = '';
                        if( count($group_attr) == $count_default && ! isset($_POST['add-to-cart']) ) {
                            $selected = ' selected';
                        }

                        $variations = woopanel_price_matrix_attribute_price($group_attr, $product);

                        $hover_detail .= '<tr><td>'.woopanel_price_matrix_attribute_tax($attr_one->taxonomy, $product->get_id()).'</td><td>'.esc_attr($attr_one->name).'</td></tr>';
                        $hover_detail .= '<tr><td>'.woopanel_price_matrix_attribute_tax($attr_two->taxonomy, $product->get_id()).'</td><td>'.esc_attr($attr_two->name).'</td></tr>';
                        $hover_detail .= '<tr><td>'.woopanel_price_matrix_attribute_tax($attr_three->taxonomy, $product->get_id()).'</td><td>'.esc_attr($attr_three->name).'</td></tr>';
                        $hover_detail .= '<tr><td>'. esc_html__('Total', 'woopanel' ) .'</td><td class=&quot;total_price&quot;>'.htmlspecialchars($variations['price']).'</td></tr>';
                    
                    ?>
                    <td id="<?php echo 'pm-price-' . esc_attr($variations['variation_id']);?>" class="pm-td-price tippy<?php echo esc_attr($selected);?>" title="<table><?php echo esc_attr($hover_detail);?></table>" data-attr="<?php echo htmlspecialchars(wp_json_encode( $group_attr ));?>" data-price="<?php echo isset($variations['final_price']) ? $variations['final_price'] : '';?>"><span class="pm-price-wrap<?php echo esc_attr($show_regular_price);?>"><?php echo wp_kses( $variations['price'],
                        array(
                            'span' => array(
                                'class' => array()
                            )
                        )
                    );?></span></td>
                    <?php endforeach;
                endforeach;?>

            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    <?php
    $_pm_show = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_show_on'];
    if($_pm_show != 'default'){
        echo '<div class="wc-price-matrix-amount price" style="margin-top: 15px;font-size: 1.41575em;"></div>';
    }?>
</div>