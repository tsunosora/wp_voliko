<?php
/*
  Plugin Name: NBT Order Upload
  Plugin URI: http://abc.com/woocommerce-price-matrix
  Description: Change the default behavior of WooCommerce Cart page, making AJAX requests when quantity field changes
  Version: 1.2
  Author: Katori
  Author URI: https://cmsmart.net/wordpress-plugins/nbt-woocommerce-price-matrix
 */

define('NBT_OUP_PATH', plugin_dir_path(__FILE__));
define('NBT_OUP_URL', plugin_dir_url(__FILE__));

class NBT_Solutions_Order_Upload
{
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public static $types = array();

    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize()
    {
        // Do nothing if pluggable functions already initialized.
        if (self::$initialized) {
            return;
        }
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        if (is_plugin_active('woocommerce/woocommerce.php')) {

            if (is_admin()) {
                require_once 'inc/admin.php';
                self::create_table();
            }

            require_once 'inc/frontend.php';

            if ((defined('DOING_AJAX') && DOING_AJAX)) {
                require_once 'inc/ajax.php';
                NBT_Order_Upload_Ajax::initialize();
            }

            if (!self::is_osc()) {
                $create = array(
                    'post_title' => 'Order Upload',
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'post_content' => '[nbt_upload]',
                    'post_slug' => 'order-upload'
                );
                wp_insert_post($create);
            }

            // TODO check with register_plugin hook
            if (!file_exists(self::get_path())) {
                if (is_writable(self::get_path(true))) {
                    mkdir(self::get_path(), 0755, true);
                } else {
                    add_action('admin_notices', 'notice_writable_folder');
                }
            }

            if (!defined('PREFIX_NBT_SOL') && !class_exists('NBT_Plugins')) {
                require_once 'inc/plugins.php';
            }
        } else {
            add_action('admin_notices', 'install_woocommerce_admin_notice');
        }
        // Register actions to do something.
        //add_action( 'action_name'   , array( __CLASS__, 'method_name'    ) );
        // State that initialization completed.
        self::$initialized = true;
    }

    public static function get_path($path = false)
    {
        $upload_dir = wp_upload_dir();
        if ($path) {
            return $upload_dir['basedir'];
        }
        return $upload_dir['basedir'] . '/nbt-order-uploads/';
    }

    public static function get_url()
    {
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'] . '/nbt-order-uploads/';
    }

    public static function create_table()
    {
        global $wpdb;

        $collate = '';

        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $tables = "
        CREATE TABLE {$wpdb->prefix}nbt_order_upload (
          ou_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          ou_order_id BIGINT UNSIGNED NOT NULL,
          ou_key char(32) NOT NULL,
          ou_value longtext NOT NULL,
          ou_expiry BIGINT UNSIGNED NOT NULL,
          PRIMARY KEY  (ou_key),
          UNIQUE KEY ou_id (ou_id)
        ) $collate;";

        if (!$wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}nbt_order_upload';")) {
            $wpdb->hide_errors();

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($tables);
        }
    }

    public static function zip_and_download($file_names, $archive_file_name)
    {
        $upload_dir = wp_upload_dir();
        $destination_folder = $upload_dir['basedir'] . '/nbt-order-uploads/';
        if (class_exists('ZipArchive')) {

            //create the object
            $zip = new ZipArchive();

            //create the file and throw the error if unsuccessful
            if ($zip->open($destination_folder . $archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
                exit("cannot open <$archive_file_name>\n");
            }

            //add each files of $file_name array to archive
            foreach ($file_names as $path => $file) {
                $zip->addFile($path, $file);
            }
            $zip->close();

            //then send the headers to foce download the zip file
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=$archive_file_name");
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($destination_folder . "$archive_file_name");
            exit;

        }
    }


    public static function is_osc($return = false)
    {
        global $wpdb;
        $rs = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_content LIKE '%[nbt_upload]%' AND post_type = 'page' AND post_status = 'publish'");
        if ($rs) {
            if ($return) {
                return $rs;
            } else {
                return $rs->ID;
            }
        }

    }

    public static function format_size($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}


if (!function_exists('install_woocommerce_admin_notice')) {
    function install_woocommerce_admin_notice()
    {
        ?>
        <div class="error">
            <p><?php _e('WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>NBT Order Upload</strong>.', 'yith-woocommerce-wishlist'); ?></p>
        </div>
        <?php
    }
}

if (!function_exists('notice_writable_folder')) {
    function notice_writable_folder()
    {
        ?>
        <div class="error">
            <p><?php _e('Uploads folder not writable. Please create a new folder as name <strong>nbt-order-uploads</strong> in path wp-content/uploads', 'yith-woocommerce-wishlist'); ?></p>
        </div>
        <?php
    }
}

if (!defined('PREFIX_NBT_SOL')) {
    NBT_Solutions_Order_Upload::initialize();
}