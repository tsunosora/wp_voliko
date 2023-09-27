# Hướng dẫn tích hợp Merlin vào themes.

1. Download plugin nb-fw bản mới nhất về tại https://gitlab.com/netbase-wp/nb-fw
2. Giải nén .zip merlin themes cho vào netbase-core/import/merlin
3. Mở file netbase-core/core.php, tìm dòng `require_once get_template_directory() . '/netbase-core/vendor/tgmpa/class-tgm-plugin-activation.php';` thêm phía dưới đoạn code sau:
> // Add Merlin
>
> require_once get_parent_theme_file_path( '/netbase-core/import/merlin/vendor/autoload.php' );
>
> require_once get_parent_theme_file_path( '/netbase-core/import/merlin/class-merlin.php' );
>
> require_once get_parent_theme_file_path( '/netbase-core/import/merlin-config.php' );

