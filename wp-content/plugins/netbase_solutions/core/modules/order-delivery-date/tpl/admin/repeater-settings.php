<?php
$fields_data = get_option( NBTODD_SETTINGS ); 
?>
<div id="table-mcs" style="width: 50%; margin-top: 15px">
	<table class="nbtodd_pm_repeater" data-option="pa_color,size,">
	   <thead>
	      <tr>
	         <th class="pm-row-zero"></th>
	         <?php foreach ($fields as $k => $f) {

	         	?>
	         <th class="pm-th"><?php echo $f['name'];?></th>
	         <?php }?>
	         <th class="pm-row-zero"></th>
	      </tr>
	   </thead>
	   <tbody>
	   	<?php
	   	if($fields_data[$field_id]){
	   		foreach ($fields_data[$field_id] as $k => $data) {?>
	      <tr class="pm-row">
	         <td class="pm-row-zero order">
	            <span><?php echo ($k+1);?></span>
	         </td>
	         <?php foreach ($fields as $k2 => $f) {	         	
	         	$f_id = $f['id'];
	         	if(isset($f['class'])){
					$f['class'] = trim($f['class'].' pm-attributes-field '.$f['id']);
				}
	         	
	         	$f['id'] = $f_id.'[]';
	         	$f['fid'] = $f_id;
	         	$value = '';
	         	if(isset($data[$f_id])){
	         		$value = $data[$f_id];
	         	}
	         	//mang($value);
	         	?>
		         <td class="pm-field">
		            <div class="pm-input">
		               <div class="pm-input-wrap">
		                  <?php echo NBT_Solutions_Order_Delivery_Date::repeater_show_field($f, $value, false);?>
		               </div>
		            </div>
		         </td>
	         <?php }?>

	         <td class="pm-row-zero">
	            <a class="pm-icon -plus small" href="#" data-event="add-row" title="Add row"></a>
	            <a class="pm-icon -minus small" href="#" data-event="remove-row" title="Remove row"></a>
	         </td>
	      </tr>
	    <?php
			}
	    }else{?>
	      <tr class="pm-row">
	         <td class="pm-row-zero order">
	            <span>1</span>
	         </td>
	         <?php foreach ($fields as $k => $f) {

	         	if(isset($f['class'])){
					$f['class'] = trim($f['class'].' pm-attributes-field '.$f['id']);
				}
	         	$f['id'] = $f['id'].'[]';?>
		         <td class="pm-field">
		            <div class="pm-input">
		               <div class="pm-input-wrap">
		                  <?php echo NBT_Solutions_Order_Delivery_Date::repeater_show_field($f, '', false);?>
		               </div>
		            </div>
		         </td>
	         <?php }?> 
	         <td class="pm-row-zero">
	            <a class="pm-icon -plus small" href="#" data-event="add-row" title="Add row"></a>
	            <a class="pm-icon -minus small" href="#" data-event="remove-row" title="Remove row"></a>
	         </td>
	      </tr>
	    <?php }?>
	   </tbody>
	</table>
</div>