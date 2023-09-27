# NETBASE SOLUTIONS #

This README would normally document whatever steps are necessary to get your application up and running.

### Cài đặt demo data của các modules vào trong THEME ###

* Sử dụng hook `add_filter( 'nbt_{module_name}_settings', 'function_name', 99, 1);`

* Chẳng hạn:
> add_filter( 'nbt_ajax-search_settings', 'setting_ajax_search_modules', 99, 1);
>
> function setting_ajax_search_modules( $settings ) {
>
>	$settings = array(
>
>		'wc_ajax-search_layout' => 'popup',
>
>		'wc_ajax-search_color_icon' => 'red'
>
>	);
>
>	return $settings;
>
> }

### Thêm localize script cho các modules trong Admin ###
* Sử dụng hook `add_filter( 'nbs_admin_localize_script', 'function_name', 99, 1);`

### Lấy dữ liệu từ setting modules ###
* Sử dụng function `NB_Solution::get_setting('ten_modules);`
* Hàm này sẽ có chức năng lấy dữ liệu từ settings của modules đó ra.
* Trường hợp 1: Nếu chưa có dữ liệu trong database (tức chưa save settings) thì lấy giá trị default từ file settings.php đã set.
* Trường hợp 2: Nếu đã có dữ liệu trong database thì lấy giá trị từ database.

* Lưu ý: Hàm này có sử dụng cache dữ liệu, để clear cache phải save settings của module đó.