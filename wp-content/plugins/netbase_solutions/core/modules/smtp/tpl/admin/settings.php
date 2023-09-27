<?php
$settings = get_option('_nbtmcs_currency_lists' );
$settings_mcs = get_option('settings_mcs' );
    // $url = "https://www.google.com/finance/converter?a=1&from=USD&to=VND";
    // $ch = curl_init();
    // $timeout = 0;

    // curl_setopt ($ch, CURLOPT_URL, $url);
    // curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // $rawdata = curl_exec($ch);

    // curl_close($ch);

    // preg_match("/<span class=bld>(.*)<\/span>/", $rawdata,  $converted);
    // $converted = preg_replace("/[^0-9.]/", "", $converted);

    // echo round($converted[0], 3);


?>

<div id="table-mcs" style="width: 50%; margin-top: 15px">
	<table class="pm_repeater" data-option="pa_color,size,">
	   <thead>
	      <tr>
	         <th class="pm-row-zero"></th>
	         <th class="pm-th">Currency</th>
	         <th class="pm-th">Position</th>
	         <th class="pm-th">Decimals</th>
	         <th class="pm-th">Rate</th>
	         <th class="pm-th">Flag</th>
	         <th class="pm-row-zero"></th>
	      </tr>
	   </thead>
	   <tbody>
	   	<?php
	   	if($settings_mcs){
	   		$i = 1;
	   		foreach ($settings_mcs as $k_mcs => $mcs) {?>
	      <tr class="pm-row">
	         <td class="pm-row-zero order">
	            <span><?php echo $i;?></span>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <select class="pm-attributes-field mcs_currency" name="mcs_currency[]" data-value="pa_color" data-option="pa_color">
	                     <option value="0">(Select an Currency)</option>
	                     <?php 
	                     foreach ($settings as $key => $value) {
	                     	?>
	                     	<option value="<?php echo $key;?>"<?php if($key == $k_mcs){ echo ' selected';}?>><?php echo $key;?></option>
	                     	<?php
	                     }?>
	                  </select>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <select class="pm-direction-field" name="mcs_position[]" data-value="vertical">
	                  	<?php foreach (NBT_Solutions_Currency_Switcher::symbol_position() as $key_pos => $value_pos) {
	                  		?>
	                  		<option value="<?php echo $key_pos;?>"<?php if($key_pos == $mcs['position']){ echo ' selected';}?>><?php echo $value_pos;?></option>
	                  		<?php
	                  	}?>
	                  </select>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <select class="pm-direction-field" name="mcs_decimals[]" data-value="vertical">
	                  	<?php for ($etalon = 0; $etalon <= 8; $etalon++) {?>
	                  		<option value="<?php echo $etalon;?>"<?php if($etalon == $mcs['decimals']){ echo ' selected';}?>><?php echo $etalon;?></option>
	                  		<?php
	                  	}?>
	                  </select>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <input type="text" style="width: 100px;" value="<?php echo $mcs['rates'];?>" name="mcs_rates[]" class="nbtmcs-text" placeholder="exchange rate">
	                  <button type="button" class="nbt-load-rate button">Load</button>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <?php echo NBT_Solutions_Currency_Switcher::show_image($mcs['image']);?>
	               </div>
	            </div>
	         </td>
	         <td class="pm-row-zero">
	            <a class="pm-icon -plus small" href="#" data-event="add-row" title="Add row"></a>
	            <a class="pm-icon -minus small" href="#" data-event="remove-row" title="Remove row"></a>
	         </td>
	      </tr>
	    <?php $i++;
			}
	    }else{?>
	      <tr class="pm-row">
	         <td class="pm-row-zero order">
	            <span>1</span>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <select class="pm-attributes-field mcs_currency" name="mcs_currency[]" data-value="pa_color" data-option="pa_color">
	                     <option value="0" selected="">(Select an Currency)</option>
	                     <?php 
	                     foreach ($settings as $key => $value) {
	                     	?>
	                     	<option value="<?php echo $key;?>"><?php echo $key;?></option>
	                     	<?php
	                     }?>
	                  </select>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <select class="pm-direction-field" name="mcs_position[]" data-value="vertical">
	                  	<?php foreach (NBT_Solutions_Currency_Switcher::symbol_position() as $key_pos => $value_pos) {
	                  		?>
	                  		<option value="<?php echo $key_pos;?>"><?php echo $value_pos;?></option>
	                  		<?php
	                  	}?>
	                  </select>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <select class="pm-direction-field" name="mcs_decimals[]" data-value="vertical">
	                  	<?php for ($etalon = 0; $etalon <= 8; $etalon++) {?>
	                  		<option value="<?php echo $etalon;?>"><?php echo $etalon;?></option>
	                  		<?php
	                  	}?>
	                  </select>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <input type="text" style="width: 100px;" value="0" name="mcs_rates[]" class="nbtmcs-text" placeholder="exchange rate">
	                  <button type="button" class="nbt-load-rate button">Load</button>
	               </div>
	            </div>
	         </td>
	         <td class="pm-field">
	            <div class="pm-input">
	               <div class="pm-input-wrap">
	                  <?php echo NBT_Solutions_Currency_Switcher::show_image($mcs['image']);?>
	               </div>
	            </div>
	         </td>
	         <td class="pm-row-zero">
	            <a class="pm-icon -plus small" href="#" data-event="add-row" title="Add row"></a>
	            <a class="pm-icon -minus small" href="#" data-event="remove-row" title="Remove row"></a>
	         </td>
	      </tr>
	    <?php }?>
	   </tbody>
	</table>
</div>

<button type="button" class="button save-mcs button-primary" style="margin-top: 15px;">Save</button>