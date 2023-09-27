<div class="nas-wrapper nas-layout-popup">
    <a href="javascript:;" class="nas-icon-click"><i class="nbt-icon-search" aria-hidden="true"></i></a>
    <div class="nas-layout-overlay">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1440 806" preserveAspectRatio="none">
            <path class="nas-overlay-path" d="M0,0C0,0,1439.999975,0,1439.999975,0C1439.999975,0,1439.999975,805.99999,1439.999975,805.99999C1439.999975,805.99999,0,805.99999,0,805.99999C0,805.99999,0,0,0,0C0,0,0,0,0,0"></path>
            <desc>Created with Snap</desc><defs></defs>
        </svg>
        <a href="javascript:void(0)" class="nas-overlay-close white title40"><i class="nbt-icon-cancel-1"></i></a>

        <div class="nas-block-element">
            <form role="search" method="get" class="nas-search-form" action="<?php echo home_url();?>">
                <label>
                    <span class="screen-reader-text"><?php esc_attr_e( 'Search for', 'nbt-solution' );?>:</span>
                    <input type="search" class="nas-field" placeholder="<?php esc_attr_e( 'Search', 'nbt-solution' );?> …" value="" name="s" autocomplete="off">
                </label>
                
                <input type="submit" class="nas-search-submit" value="Search">

                <div class="nas-results"></div>
            </form>
        </div>
    </div>
</div>