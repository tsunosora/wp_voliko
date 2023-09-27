<div class="nas-wrapper nas-layout-input">
    <form role="search" method="get" class="nas-search-form" action="<?php echo home_url();?>">
        <label>
            <span class="screen-reader-text"><?php esc_attr_e( 'Search for', 'nbt-solution' );?>:</span>
            <input type="search" class="nas-field" placeholder="<?php esc_attr_e( 'Search', 'nbt-solution' );?> …" value="" name="s" autocomplete="off">
        </label>
        
        <input type="submit" class="nas-search-submit" value="Search">

        <div class="nas-results"></div>
    </form>
    
</div>