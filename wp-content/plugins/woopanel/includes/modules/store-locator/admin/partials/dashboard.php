<!-- Container -->
<div class="asl-p-cont asl-new-bg">
  <div class="hide">
    <svg xmlns="http://www.w3.org/2000/svg">
      <symbol id="i-cart" viewBox="0 0 32 32" width="40" height="40" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
          <path d="M6 6 L30 6 27 19 9 19 M27 23 L10 23 5 2 2 2" />
          <circle cx="25" cy="27" r="2" />
          <circle cx="12" cy="27" r="2" />
      </symbol>
      <symbol id="i-tag" viewBox="0 0 32 32" width="40" height="40" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <circle cx="24" cy="8" r="2" />
        <path d="M2 18 L18 2 30 2 30 14 14 30 Z" />
      </symbol>
      <symbol id="i-location" viewBox="0 0 32 32" width="40" height="40" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
          <circle cx="16" cy="11" r="4" />
          <path d="M24 15 C21 22 16 30 16 30 16 30 11 22 8 15 5 8 10 2 16 2 22 2 27 8 24 15 Z" />
      </symbol>
      <symbol id="i-search" viewBox="0 0 32 32" width="40" height="40" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
          <circle cx="14" cy="14" r="12" />
          <path d="M23 23 L30 30"  />
      </symbol>
    </svg>
  </div>
  <div class="container">
    <div class="row asl-inner-cont">
      <div class="col-md-12">
        <div class="card p-0 mb-4">
          <h3 class="card-title"><?php echo esc_html__('Agile Store Locator Dashboard','asl_admin') ?></h3>
          <div class="card-body">
        
            <div class="alert alert-info" role="alert">
              <a target="_blank" href="https://netbaseteam.com/wiki/"><?php echo esc_html__('Please visit the documentation page for help, for support please ','asl_admin') ?></a><a target="_blank" href="https://wordpress.org/support/plugin/agile-store-locator"><?php echo esc_html__('create a ticket','asl_admin') ?></a>  <?php echo esc_html__(', or email us at support@agilelogix.com','asl_admin') ?> 
            </div>
            <?php if(!$all_configs['api_key']): ?>
                <h3  class="alert alert-danger" style="font-size: 14px"><?php echo esc_html__('Google API KEY is missing, the Map Search and Direction will not work without it, Please add Google API KEY.','asl_admin') ?> <a href="https://netbaseteam.com/blog/enable-google-maps-api-agile-store-locator-plugin/" target="_blank">How to Add API Key?</a></h3>
            <?php endif; ?>
            <h3 class="alert alert-warning" style="width:100%;font-size: 14px"><span class="mr-2 mb-2 d-block"><?php echo esc_html__('Backup My Logo, Custom Markers, and Category Icons.','asl_admin') ?> </span>
            <a class="mr-2 btn disabled btn-sm btn-dark" style="color: #FFF" id="btn-assets-backup"><?php echo esc_html__('Backup Assets','asl_admin') ?></a>
            <a class="mr-2 btn disabled btn-primary btn-sm hide" id="lnk-assets-download" target="_blank"><?php echo esc_html__('Download Link','asl_admin') ?></a>
            <button type="button" class="btn disabled btn-sm btn-success pull-right" data-toggle="smodal" data-target="#import_assets_model"><?php echo esc_html__('Import Assets Zip','asl_admin') ?></button>
            </h3>
            <div class="dashboard-area">
              <div class="row">
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-3 stats-store">
                        <div class="stats">
                            <div class="stats-a"><svg width="40" height="40"><use xlink:href="#i-cart"></use></svg></div>
                            <div class="stats-b" title="<?php echo esc_html__('Stores','asl_admin') ?>"><?php echo $all_stats['stores'] ?></div>
                        </div>
                      </div>
                      <div class="col-md-3 stats-category">
                        <div class="stats">
                            <div class="stats-a"><svg width="40" height="40"><use xlink:href="#i-tag"></use></svg></div>
                            <div class="stats-b" title="<?php echo esc_html__('Categories','asl_admin') ?>"><?php echo $all_stats['categories'] ?></div>
                        </div>
                      </div>
                      <div class="col-md-3 stats-marker">
                        <div class="stats">
                            <div class="stats-a"><svg width="40" height="40"><use xlink:href="#i-location"></use></svg></div>
                            <div class="stats-b" title="<?php echo esc_html__('Markers','asl_admin') ?>"><?php echo $all_stats['markers'] ?></div>
                        </div>
                      </div>
                      <div class="col-md-3 stats-searches">
                        <div class="stats">
                            <div class="stats-a"><svg width="40" height="40"><use xlink:href="#i-search"></use></svg></div>
                            <div class="stats-b" title="<?php echo esc_html__('Searches','asl_admin') ?>">N/A</div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="row"></div>
              <ul class="nav nav-tabs" style="margin-top:30px">
                <li role="presentation" class="nav-item active"><a class="nav-link" href="#asl-analytics">Analytics</a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#asl-views"><?php echo esc_html__('Top Views','asl_admin') ?></a></li>
              </ul>
              <div class="tab-content" id="asl-tabs">
                
                <div class="tab-pane active" role="tabpanel" id="asl-analytics" aria-labelledby="asl-analytics">
                  <div class="row">
                    <div class="col-md-4 form-group mb-3">
                      <div class="form-group">
                        <label class="mr-2" for="asl-search-month"><?php echo esc_html__('Period','asl_admin') ?></label>
                        <select id="asl-search-month" class="custom-select" style="width:70%">
                          <?php 
                          for ($i=0; $i<=12; $i++) { 
                            echo '<option value="'.date('m-Y', strtotime("-$i month")).'">'.date('m/Y', strtotime("-$i month")).'</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="canvas-holder" style="width:100%">
                          <img src="<?php echo WOOPANEL_STORE_LOCATOR_URL.'admin/images/analytics.png' ?>" style="max-width:100%;margin-top: 0px">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane" role="tabpanel" id="asl-views" aria-labelledby="asl-views">
                  
                  <div class="col-md-12"> 
                    <ul class="list-group">
                      <li class="list-group-item active"><span class="store-id">
                        <div class="row">
                          <div class="col-2"><?php echo esc_html__('Store ID','asl_admin') ?></div>
                          <div class="col-8"><?php echo esc_html__('Most Views Stores List','asl_admin') ?></div>
                          <div class="col-2"><?php echo esc_html__('Views','asl_admin') ?></div>
                        </div>
                      </li>
                      
                      <li class="list-group-item">
                        <div class="row">
                          <div class="col-6">Analytics in Pro Version</div>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <br clear="both">
                  <div class="col-md-12"> 
                    <ul class="list-group">
                      <li class="list-group-item active">
                        <div class="row">
                          <div class="col-8"><?php echo esc_html__('Most Search Locations','asl_admin') ?></div>
                          <div class="col-4"><?php echo esc_html__('Views','asl_admin') ?></div>
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="row">
                          <div class="col-6">Analytics in Pro Version</div>
                        </div>
                    </ul>
                  </div>
                </div>

              </div>  
            </div>
            <div class="dump-message asl-dumper"></div>
          </div>
        </div>
      </div>  
    </div>
  </div>


</div>
<!-- asl-cont end-->


<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo WOOPANEL_STORE_LOCATOR_URL ?>'
};

asl_engine.pages.dashboard();
</script>