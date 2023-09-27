<div class="taxonomy-lists taxonomy-<?php echo esc_attr($this->taxonomy);?>">
    <div class="row">
        <div class="col col-4">
            <div class="m-portlet">
                <?php
                $label = '';
                if( $this->taxonomy ) {
                    if( $this->edit ) {
                        $label = sprintf( esc_html__( 'Edit %s', 'woopanel' ), $term->name );
                    }else {
                        $label = sprintf( esc_html__( 'Add new %s', 'woopanel' ), $attribute->attribute_label );
                    }

                    include_once WOODASHBOARD_VIEWS_DIR . 'attribute-child.php';	
                }else {
                    if( $this->edit ) {
                        $label = esc_html__( 'Edit attribute', 'woopanel' );;
                    }else {
                        $label = esc_html__( 'Add new attribute', 'woopanel' );
                    }

                    include_once WOODASHBOARD_VIEWS_DIR . 'attribute-parent.php';	
                }?>
            </div>
        </div>

        <div class="col col-8">
            <?php $this->display();?>
        </div>
    </div>
</div>