<script id="tmpl_list_item" type="text/x-jsrender">
  <?php echo woopanel_store_list_template1();?>
</script>



<script id="asl_too_tip" type="text/x-jsrender">
  <h3>{{:title}}</h3>
  <div class="infowindowContent">
    <div class="info-addr">
      {{if description}}
      <div class="address" style="margin-bottom: 10px">{{:description}}</div>
      {{/if}}
      <div class="address"><span class="glyphicon icon-location"></span>{{:address}}</div>
      <div class="phone"><span class="glyphicon icon-phone-outline"></span><b>Phone: </b><a href="tel:{{:phone}}">{{:phone}}</a></div>
      {{if email}}
      <div class="phone"><span class="glyphicon icon-at"></span><a href="mailto:{{:email}}" style="text-transform: lowercase">{{:email}}</a></div>
      {{/if}}
      {{if c_names}}
      <div class="p-category"><span class="glyphicon icon-tag"></span> {{:c_names}}</div>
      {{/if}}
      {{if open_hours}}
      <div class="p-time"><span class="glyphicon icon-clock-1"></span> {{:open_hours}}</div>
      {{/if}}
      {{if dist_str}}
        <div class="row">
          <div class="col-xs-12">
            <a class="s-distance pull-right" style="margin-right: 15px"><?php echo esc_html__( 'Distance','asl_locator') ?>: {{:dist_str}}</a>
          </div>
        </div>
      {{/if}}
    </div>
  <div class="asl-buttons"></div>
</div><div class="arrow-down"></div>
</script>

