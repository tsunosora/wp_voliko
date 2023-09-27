    <?php if( $vacant_attributes ) { ?>
    <table class="pm-vacant-attributes">
        <tbody>
            <?php foreach( $vacant_attributes as $k_vacant => $val_vacant) {?>
            <tr>
                <td><label for="<?php echo esc_attr($k_vacant);?>"><?php echo esc_attr($val_vacant['attribute_label']);?></label></td>
                <td>
                    <select id="<?php echo esc_attr($k_vacant);?>" name="vacant_attrbute[<?php echo esc_attr($k_vacant);?>]" class="form-control m-input select-vacant-attribute">
                        <?php foreach( $val_vacant['terms'] as $k_term => $v_term ) {
                            ?>
                            <option value="<?php echo esc_attr($v_term['slug']);?>"><?php echo esc_attr($v_term['name']);?></option>
                            <?php
                        }?>
                    </select>
                </td>
            </tr>
            <?php }?>
        </tbody>
    </table>
    <?php }?>