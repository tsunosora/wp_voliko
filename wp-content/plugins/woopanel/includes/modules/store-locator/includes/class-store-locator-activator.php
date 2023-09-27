<?php

/**
 * Fired during plugin activation
 *
 * @link       http://agilelogix.com
 * @since      1.0.0
 *
 * @package    wpl_store_locator
 * @subpackage wpl_store_locator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    wpl_store_locator
 * @subpackage wpl_store_locator/includes
 * @author     Your Name <email@agilelogix.com>
 */
class WooPanel_Store_Locator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		WooPanel_Store_Locator_Activator::add_basic_tables();
	}


	public static function add_basic_tables() {

		//ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		global $wpdb;
		$charset_collate = 'utf8';
		$prefix 	 	 = $wpdb->prefix."wplsl_";

		

		/*Categories*/
		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}categories` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `category_name` varchar(255) DEFAULT NULL,
			  `slug` varchar(255) DEFAULT NULL,
			  `user_id` int(4) NOT NULL,
			  `is_active` tinyint(4) NOT NULL,
			  `icon` varchar(100) NOT NULL,
			  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;";
		dbDelta( $sql );

		//Alter Character SET
		$sql = "ALTER TABLE {$prefix}categories CHARACTER SET utf8;";
		$wpdb->query( $sql );

		###############################################################################


	
		/*Config*/
		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}configs` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `key` varchar(50) DEFAULT NULL,
			  `value` varchar(100) DEFAULT NULL,
			  `type` varchar(50) DEFAULT NULL,
			  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;";
		dbDelta( $sql );
		

		
		/*Countries*/
		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}countries` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `country` varchar(255) NOT NULL,
				  `iso_code_2` char(2) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `IDX_COUNTRIES_NAME` (`country`)
				) AUTO_INCREMENT=240 DEFAULT CHARSET=utf8;";
		dbDelta( $sql );





		/*CREATE Store Logos*/
		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}storelogos` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `path` varchar(300) NOT NULL,
				  `name` varchar(50) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;";
		dbDelta( $sql );



		###########################################################################

		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}stores` (
			    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`title` varchar(255) DEFAULT NULL,
				`name` varchar(255) DEFAULT NULL,
				`description` text,
				`street` text,
				`city` varchar(100) DEFAULT NULL,
				`state` varchar(100) DEFAULT NULL,
				`postal_code` varchar(50) DEFAULT NULL,
				`country` int(11) DEFAULT NULL,
				`lat` varchar(50) DEFAULT NULL,
				`lng` varchar(50) DEFAULT NULL,
				`phone` varchar(50) DEFAULT NULL,
				`fax` varchar(50) DEFAULT NULL,
				`email` varchar(100) DEFAULT NULL,
				`website` varchar(255) DEFAULT NULL,
				`description_2` text,
				`logo_id` int(11) DEFAULT NULL,
				`banner_id` int(11) DEFAULT NULL,
				`user_id` int(11) DEFAULT NULL,
				`marker_id` int(11) DEFAULT NULL,
				`intro` text,
				`tos` text,
				`is_disabled` tinyint(4) DEFAULT NULL,
				`open_hours` text,
				`ordr` int(11) DEFAULT '0',
				`featured` tinyint(4) DEFAULT NULL,
				`created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				`updated_on` datetime DEFAULT NULL,
				INDEX (`logo_id`),
				INDEX (`country`),
			  PRIMARY KEY (`id`)
			)  DEFAULT CHARSET=utf8;";
		dbDelta( $sql );

		//Alter Character SET
		$sql = "ALTER TABLE {$prefix}stores CHARACTER SET utf8;";
		$wpdb->query( $sql );


		/*CREATE Stores Categories*/
		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}stores_categories` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `category_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			  INDEX (`store_id`),
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=261 DEFAULT CHARSET=utf8;";
		dbDelta( $sql );


		/*Stores Markers*/
		$sql = "CREATE TABLE IF NOT EXISTS `{$prefix}markers` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `marker_name` varchar(255) DEFAULT NULL,
			  `is_active` tinyint(4) NOT NULL,
			  `icon` varchar(100) NOT NULL,
			  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;";
		dbDelta( $sql );

		//markers
		$c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}markers");
		if($c[0]->count <= 0) {
			
			$sql =  "UPDATE `{$prefix}markers` SET `icon` = 'default.png' WHERE id > 150;"; //150 by default
			dbDelta( $sql );
		}



		//logos
		$c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}storelogos");
		
		if($c[0]->count <= 0) {

			$sql =  "INSERT INTO `{$prefix}storelogos`(`path`,`name`) VALUES ('default.png','Default')";
			dbDelta( $sql );
		}
		



		//categories;
		$c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}categories");
		if($c[0]->count <= 0) {
			
			$sql =  "INSERT INTO `{$prefix}categories`(`id`,`category_name`,`is_active`,`icon`,`created_on`) VALUES (1,'Arts & Entertainment',1,'arts_entertainment.png','2016-05-07 20:31:04'),(2,'For The Home',1,'for_the_home.png','2016-05-07 20:31:09'),(4,'Appliances',1,'appliances.png','2016-05-07 20:31:16'),(5,'Medical / Dental / Vision Care',1,'medical.png','2016-05-07 20:31:20'),(6,'Jewelry',1,'jewelry.png','2016-05-07 20:31:23'),(7,'Fitness',1,'fitness.png','2016-05-07 20:31:26'),(8,'Electronics',1,'electronics.png','2016-05-07 20:31:32'),(9,'Pets',1,'pets.png','2016-05-07 20:31:33'),(10,'Auto',1,'auto.png','2016-05-07 20:31:36'),(11,'Local Services',1,'local_services.png','2016-05-07 20:31:39'),(13,'Beauty and Spas',1,'beauty_spas.png','2016-05-07 20:31:50'),(14,'Nightlife',1,'nightlife.png','2016-05-07 20:31:55'),(16,'Restaurants',1,'restaurant.png','2016-05-07 20:32:03'),(17,'Travel',1,'travel.png','2016-05-07 20:32:55');";
			dbDelta( $sql );
		}


		///////////////////////////////////
		///////// ADD INDEXES /////////////
		///////////////////////////////////
		$stores_index = $wpdb->query("SHOW INDEX FROM `{$prefix}stores`");
		if($stores_index < 2) {

			$wpdb->query("ALTER TABLE `{$prefix}stores` ADD INDEX(`logo_id`);");			
			$wpdb->query("ALTER TABLE `{$prefix}stores_categories` ADD INDEX(`store_id`);");
		}

		if($stores_index < 3) {

			$wpdb->query("ALTER TABLE `{$prefix}stores` ADD INDEX(`country`);");
		}

		
		//countries
		$c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}countries WHERE country = 'Macedonia'");

		//	Check if new countries are not there, save the list again
		if($c[0]->count <= 0) {

			$wpdb->query("TRUNCATE TABLE `{$prefix}countries`");

			$sql =  "INSERT INTO `{$prefix}countries`(`id`,`country`,`iso_code_2`) VALUES (1,'Afghanistan','AF'),(2,'Albania','AL'),(3,'Algeria','DZ'),(4,'American Samoa','AS'),(5,'Andorra','AD'),(6,'Angola','AO'),(7,'Anguilla','AI'),(8,'Antarctica','AQ'),(9,'Antigua and Barbuda','AG'),(10,'Argentina','AR'),(11,'Armenia','AM'),(12,'Aruba','AW'),(13,'Australia','AU'),(14,'Austria','AT'),(15,'Azerbaijan','AZ'),(16,'Bahamas','BS'),(17,'Bahrain','BH'),(18,'Bangladesh','BD'),(19,'Barbados','BB'),(20,'Belarus','BY'),(21,'Belgium','BE'),(22,'Belize','BZ'),(23,'Benin','BJ'),(24,'Bermuda','BM'),(25,'Bhutan','BT'),(26,'Bolivia','BO'),(27,'Bosnia and Herzegovina','BA'),(28,'Botswana','BW'),(29,'Bouvet Island','BV'),(30,'Brazil','BR'),(31,'British Indian Ocean Territory','IO'),(32,'Brunei Darussalam','BN'),(33,'Bulgaria','BG'),(34,'Burkina Faso','BF'),(35,'Burundi','BI'),(36,'Cambodia','KH'),(37,'Cameroon','CM'),(38,'Canada','CA'),(39,'Cape Verde','CV'),(40,'Cayman Islands','KY'),(41,'Central African Republic','CF'),(42,'Chad','TD'),(43,'Chile','CL'),(44,'China','CN'),(45,'Christmas Island','CX'),(46,'Cocos (Keeling) Islands','CC'),(47,'Colombia','CO'),(48,'Comoros','KM'),(49,'Congo','CG'),(50,'Cook Islands','CK'),(51,'Costa Rica','CR'),(52,'Cote D\'Ivoire','CI'),(53,'Croatia','HR'),(54,'Cuba','CU'),(55,'Cyprus','CY'),(56,'Czech Republic','CZ'),(57,'Denmark','DK'),(58,'Djibouti','DJ'),(59,'Dominica','DM'),(60,'Dominican Republic','DO'),(61,'East Timor','TP'),(62,'Ecuador','EC'),(63,'Egypt','EG'),(64,'El Salvador','SV'),(65,'Equatorial Guinea','GQ'),(66,'Eritrea','ER'),(67,'Estonia','EE'),(68,'Ethiopia','ET'),(69,'Falkland Islands (Malvinas)','FK'),(70,'Faroe Islands','FO'),(71,'Fiji','FJ'),(72,'Finland','FI'),(73,'France','FR'),(74,'France, Metropolitan','FX'),(75,'French Guiana','GF'),(76,'French Polynesia','PF'),(77,'French Southern Territories','TF'),(78,'Gabon','GA'),(79,'Gambia','GM'),(80,'Georgia','GE'),(81,'Germany','DE'),(82,'Ghana','GH'),(83,'Gibraltar','GI'),(84,'Greece','GR'),(85,'Greenland','GL'),(86,'Grenada','GD'),(87,'Guadeloupe','GP'),(88,'Guam','GU'),(89,'Guatemala','GT'),(90,'Guinea','GN'),(91,'Guinea-bissau','GW'),(92,'Guyana','GY'),(93,'Haiti','HT'),(94,'Heard and Mc Donald Islands','HM'),(95,'Honduras','HN'),(96,'Hong Kong','HK'),(97,'Hungary','HU'),(98,'Iceland','IS'),(99,'India','IN'),(100,'Indonesia','ID'),(101,'Iran (Islamic Republic of)','IR'),(102,'Iraq','IQ'),(103,'Ireland','IE'),(104,'Israel','IL'),(105,'Italy','IT'),(106,'Jamaica','JM'),(107,'Japan','JP'),(108,'Jordan','JO'),(109,'Kazakhstan','KZ'),(110,'Kenya','KE'),(111,'Kiribati','KI'),(112,'South Korea','SK'),(113,'North Korea','NK'),(114,'Kuwait','KW'),(115,'Kyrgyzstan','KG'),(116,'Lao People\'s Democratic Republic','LA'),(117,'Latvia','LV'),(118,'Lebanon','LB'),(119,'Lesotho','LS'),(120,'Liberia','LR'),(121,'Libyan Arab Jamahiriya','LY'),(122,'Liechtenstein','LI'),(123,'Lithuania','LT'),(124,'Luxembourg','LU'),(125,'Macau','MO'),(126,'Macedonia, The Former Yugoslav Republic of','MK'),(127,'Madagascar','MG'),(128,'Malawi','MW'),(129,'Malaysia','MY'),(130,'Maldives','MV'),(131,'Mali','ML'),(132,'Malta','MT'),(133,'Marshall Islands','MH'),(134,'Martinique','MQ'),(135,'Mauritania','MR'),(136,'Mauritius','MU'),(137,'Mayotte','YT'),(138,'Mexico','MX'),(139,'Micronesia, Federated States of','FM'),(140,'Moldova, Republic of','MD'),(141,'Monaco','MC'),(142,'Mongolia','MN'),(143,'Montserrat','MS'),(144,'Morocco','MA'),(145,'Mozambique','MZ'),(146,'Myanmar','MM'),(147,'Namibia','NA'),(148,'Nauru','NR'),(149,'Nepal','NP'),(150,'Netherlands','NL'),(151,'Netherlands Antilles','AN'),(152,'New Caledonia','NC'),(153,'New Zealand','NZ'),(154,'Nicaragua','NI'),(155,'Niger','NE'),(156,'Nigeria','NG'),(157,'Niue','NU'),(158,'Norfolk Island','NF'),(159,'Northern Mariana Islands','MP'),(160,'Norway','NO'),(161,'Oman','OM'),(162,'Pakistan','PK'),(163,'Palau','PW'),(164,'Panama','PA'),(165,'Papua New Guinea','PG'),(166,'Paraguay','PY'),(167,'Peru','PE'),(168,'Philippines','PH'),(169,'Pitcairn','PN'),(170,'Poland','PL'),(171,'Portugal','PT'),(172,'Puerto Rico','PR'),(173,'Qatar','QA'),(174,'Reunion','RE'),(175,'Romania','RO'),(176,'Russian Federation','RU'),(177,'Rwanda','RW'),(178,'Saint Kitts and Nevis','KN'),(179,'Saint Lucia','LC'),(180,'Saint Vincent and the Grenadines','VC'),(181,'Samoa','WS'),(182,'San Marino','SM'),(183,'Sao Tome and Principe','ST'),(184,'Saudi Arabia','SA'),(185,'Senegal','SN'),(186,'Seychelles','SC'),(187,'Sierra Leone','SL'),(188,'Singapore','SG'),(189,'Slovakia (Slovak Republic)','SK'),(190,'Slovenia','SI'),(191,'Solomon Islands','SB'),(192,'Somalia','SO'),(193,'South Africa','ZA'),(194,'South Georgia and the South Sandwich Islands','GS'),(195,'Spain','ES'),(196,'Sri Lanka','LK'),(197,'St. Helena','SH'),(198,'St. Pierre and Miquelon','PM'),(199,'Sudan','SD'),(200,'Suriname','SR'),(201,'Svalbard and Jan Mayen Islands','SJ'),(202,'Swaziland','SZ'),(203,'Sweden','SE'),(204,'Switzerland','CH'),(205,'Syrian Arab Republic','SY'),(206,'Taiwan','TW'),(207,'Tajikistan','TJ'),(208,'Tanzania, United Republic of','TZ'),(209,'Thailand','TH'),(210,'Togo','TG'),(211,'Tokelau','TK'),(212,'Tonga','TO'),(213,'Trinidad and Tobago','TT'),(214,'Tunisia','TN'),(215,'Turkey','TR'),(216,'Turkmenistan','TM'),(217,'Turks and Caicos Islands','TC'),(218,'Tuvalu','TV'),(219,'Uganda','UG'),(220,'Ukraine','UA'),(221,'United Arab Emirates','AE'),(222,'United Kingdom','GB'),(223,'United States','US'),(224,'United States Minor Outlying Islands','UM'),(225,'Uruguay','UY'),(226,'Uzbekistan','UZ'),(227,'Vanuatu','VU'),(228,'Vatican City State (Holy See)','VA'),(229,'Venezuela','VE'),(230,'Viet Nam','VN'),(231,'Virgin Islands (British)','VG'),(232,'Virgin Islands (U.S.)','VI'),(233,'Wallis and Futuna Islands','WF'),(234,'Western Sahara','EH'),(235,'Yemen','YE'),(236,'Serbia','SR'),(237,'Zaire','ZR'),(238,'Zambia','ZM'),(239,'Zimbabwe','ZW'),(240,'Montenegro','ME'),(241,'Macedonia','MK'),(242,'Curaçao','CW');";
			dbDelta( $sql );
		}

		//stores
		$c = $wpdb->get_results("SELECT count(*) AS 'count' FROM {$prefix}stores");
		
		if($c[0]->count <= 0) {
			$sql = "INSERT INTO `{$prefix}stores` (`id`, `title`, `name`, `description`, `street`, `city`, `state`, `postal_code`, `country`, `lat`, `lng`, `phone`, `fax`, `email`, `website`, `description_2`, `logo_id`, `banner_id`, `user_id`, `marker_id`, `is_disabled`, `open_hours`, `ordr`, `featured`, `created_on`, `updated_on`) VALUES(1, 'Amanda Food Court', 'amanda-food-court', '', '45 North Street', 'Uitenhage', 'Eastern Cape', NULL, 193, '-33.749771', '25.405823', '041 111 3964', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(2, 'Aqua Food Store', 'aqua-food-store', '', '26 Northriding, Lorraine Manor', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.97506', '25.513332', '213 882 8888', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(3, 'Astro Club X', 'astro-club-x', '', '15 Heartly Road, Parsons Hill', 'Port Elizabeth', 'Eastern Cape', '', 193, '-33.947128', '25.591169', '123 226 2222', '', '', 'https://agilestorelocator.com', 'there is no description available for this store', 150, 139, 6, NULL, 0, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', '2020-04-14 02:57:56'),(4, 'Barry Mason Pool Services', 'barry-mason-pool-services', '', '34 Saddlewood, Louis Michael Drive, Lovemore Heights', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.99213', '25.531601', '123 888 5555', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(5, 'Bid-Bon Development', 'bid-bon-development', '', '274 Kragga Kamma Road, Lorraine', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.96524', '25.50242', '041 888 3534', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(6, 'Deon\'S Pool Maintenance Servics', 'deons-pool-maintenance-servics', '', '1 Demurville, Lorraine', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.968891', '25.52002', '072 888 1607', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(7, 'East Coast Pools', 'east-coast-pools', '', '17 Reith Street, Sidwell', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.92194', '25.595169', '041 888 4916', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(8, 'Mica Food Court', 'mica-food-court', '', '48 Church Street', 'Graaf Reinet', 'Eastern Cape', NULL, 193, '-32.253212', '24.536243', '049 888 2640', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(9, 'Haggie\'s Swimpool Services & Supplies ', 'haggies-swimpool-services-supplies', '', '50 Mosel Road', 'Uitenhage', 'Eastern Cape', NULL, 193, '-33.752365', '25.404612', '041 888 6343', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:05', NULL),(10, 'Heritage Rock Hotel', 'heritage-rock-hotel', '', 'Idle  Wydle, Sardinia Bay Road, Lovemore Park', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-34.014511', '25.523298', '082 888 9282', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(11, 'Norman\'s Hotel', 'normans-hotel', '', '15 Vincent Road', 'East London', 'Eastern Cape', NULL, 193, '-32.978962', '27.901928', '043 888 8445', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(12, 'Pelican Hotel', 'pelican-hotel', '', '143 Heugh Road, Walmer', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.980839', '25.59514', '041 888 6453', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(13, 'Pool and Spa Centre', 'pool-and-spa-centre', '', '6 Boshof Street, Westering', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.944561', '25.522751', '041 888 8005', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(14, 'Pool Maintenance Services', 'pool-maintenance-services', '', '100 Dijon Road, Lorraine', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.976929', '25.529341', '041 888 4927', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(15, 'Sandstone Pools', 'sandstone-pools', '', '5 Yale Road, Blue Water Bay', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.857689', '25.62956', '072 888 0505', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(16, 'Royal Autos', 'royal-autos', '', '61 Heugh Road, Walmer', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.976898', '25.605591', '041 888 8117', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(17, 'Grahamstown Restaurant', 'grahamstown-restaurant', '', '4 Hill Street', 'Grahamstown', 'Eastern Cape', NULL, 193, '-33.308353', '26.525585', '046 888 4320', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(18, 'Port Alfred Restaurant', 'port-alfred-restaurant', '', '88 Albany Road', 'Port Alfred', 'Eastern Cape', NULL, 193, '-33.586407', '26.907701', '046 888 8618', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(19, 'JIM Beauty Saloon', 'jim-beauty-saloon', '', 'Jacaranda Street', 'Jeffreys Bay', 'Eastern Cape', NULL, 193, '-34.033333', '24.916668', '042 888 0813', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(20, 'Sliming Center', 'sliming-center', '', '28 6th Avenue, Walmer', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.978668', '25.594669', '041 888 6568', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:06', NULL),(21, 'Artcraft Centre', 'artcraft-centre', '', '43 3rd Avenue, Newton Park', 'Port Elizabeth', 'Eastern Cape', NULL, 193, '-33.947609', '25.568867', '041 888 1257', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:07', NULL),(22, 'Tams Hyperstore', 'tams-hyperstore', '', '5 High Street', 'Cradock', 'Eastern Cape', NULL, 193, '-32.175652', '25.621719', '048 888 3022', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:07', NULL),(23, 'The Pool Lab', 'the-pool-lab', '', '17 Young Road, Mill Park', 'Port Elizabeth', 'Eastern Cape', '', 193, '-33.965435', '25.59059', '083 888 1181', '', '', 'https://agilestorelocator.com', 'there is no description available for this store', NULL, 0, 0, NULL, 0, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2016-06-08 04:44:07', '2020-04-13 10:32:08'),(216, 'Amanzi Club', 'amanzi-club', '', '46 Longfellow Street, Ridgeway', 'Johannesburg', 'Gauteng', NULL, 193, '-26.253469', '27.996401', '011 888 4569', NULL, 'support@agilelogix.com', 'https://agilestorelocator.com', 'there is no description available for this store', 1, 0, 0, 1, NULL, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2020-04-06 06:23:26', NULL),(217, 'Amanzi Club', 'amanzi-club', '', '46 Longfellow Street, Ridgeway', 'Johannesburg', 'Gauteng', '', 193, '-26.253469', '27.996401', '011 888 4569', '', '', 'https://agilestorelocator.com', 'there is no description available for this store', NULL, 0, 0, NULL, 0, '{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}', 0, NULL, '2020-04-06 06:24:45', '2020-04-13 10:33:24'),(218, 'NetbaseJSC', 'netbasejsc', 'NetbaseJSC Company', '91 Nguyen Chi Thanh', 'Ha Noi', 'Dong Da', '70000', 230, '21.020667', '105.8094866', '0364886455', '', 'anh.lt@netbasejsc.com', 'https://netbasejsc.com', '', 141, 145, 4, NULL, 0, '{\"mon\":[\"09:30 AM - 06:30 PM\"],\"tue\":[\"09:30 AM - 06:30 PM\"],\"wed\":[\"09:30 AM - 06:30 PM\"],\"thu\":[\"09:30 AM - 06:30 PM\"],\"fri\":[\"09:30 AM - 06:30 PM\"],\"sat\":[\"09:30 AM - 06:30 PM\"],\"sun\":[\"09:30 AM - 06:30 PM\"]}', 0, NULL, '2020-04-14 07:14:51', '2020-04-14 07:20:40');";
			// $sql =  "INSERT INTO `{$prefix}stores`(`id`,`title`,`description`,`street`,`city`,`state`,`postal_code`,`country`,`lat`,`lng`,`phone`,`fax`,`email`,`website`,`description_2`,`logo_id`,`marker_id`,`is_disabled`,`open_hours`,`created_on`,`updated_on`) values (1,'Amanda Food Court','','45 North Street','Uitenhage','Eastern Cape',NULL,193,'-33.749771','25.405823','041 111 3964',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(2,'Aqua Food Store','','26 Northriding, Lorraine Manor','Port Elizabeth','Eastern Cape',NULL,193,'-33.97506','25.513332','213 882 8888',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(3,'Astro Club','','15 Heartly Road, Parsons Hill','Port Elizabeth','Eastern Cape',NULL,193,'-33.947128','25.591169','123 226 2222',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(4,'Barry Mason Pool Services','','34 Saddlewood, Louis Michael Drive, Lovemore Heights','Port Elizabeth','Eastern Cape',NULL,193,'-33.99213','25.531601','123 888 5555',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(5,'Bid-Bon Development','','274 Kragga Kamma Road, Lorraine','Port Elizabeth','Eastern Cape',NULL,193,'-33.96524','25.50242','041 888 3534',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(6,'Deon\'S Pool Maintenance Servics','','1 Demurville, Lorraine','Port Elizabeth','Eastern Cape',NULL,193,'-33.968891','25.52002','072 888 1607',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(7,'East Coast Pools','','17 Reith Street, Sidwell','Port Elizabeth','Eastern Cape',NULL,193,'-33.92194','25.595169','041 888 4916',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(8,'Mica Food Court','','48 Church Street','Graaf Reinet','Eastern Cape',NULL,193,'-32.253212','24.536243','049 888 2640',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(9,'Haggie\'s Swimpool Services & Supplies ','','50 Mosel Road','Uitenhage','Eastern Cape',NULL,193,'-33.752365','25.404612','041 888 6343',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:05',NULL),(10,'Heritage Rock Hotel','','Idle  Wydle, Sardinia Bay Road, Lovemore Park','Port Elizabeth','Eastern Cape',NULL,193,'-34.014511','25.523298','082 888 9282',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(11,'Norman\'s Hotel','','15 Vincent Road','East London','Eastern Cape',NULL,193,'-32.978962','27.901928','043 888 8445',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(12,'Pelican Hotel','','143 Heugh Road, Walmer','Port Elizabeth','Eastern Cape',NULL,193,'-33.980839','25.59514','041 888 6453',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(13,'Pool and Spa Centre','','6 Boshof Street, Westering','Port Elizabeth','Eastern Cape',NULL,193,'-33.944561','25.522751','041 888 8005',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(14,'Pool Maintenance Services','','100 Dijon Road, Lorraine','Port Elizabeth','Eastern Cape',NULL,193,'-33.976929','25.529341','041 888 4927',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(15,'Sandstone Pools','','5 Yale Road, Blue Water Bay','Port Elizabeth','Eastern Cape',NULL,193,'-33.857689','25.62956','072 888 0505',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(16,'Royal Autos','','61 Heugh Road, Walmer','Port Elizabeth','Eastern Cape',NULL,193,'-33.976898','25.605591','041 888 8117',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(17,'Grahamstown Restaurant','','4 Hill Street','Grahamstown','Eastern Cape',NULL,193,'-33.308353','26.525585','046 888 4320',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(18,'Port Alfred Restaurant','','88 Albany Road','Port Alfred','Eastern Cape',NULL,193,'-33.586407','26.907701','046 888 8618',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(19,'JIM Beauty Saloon','','Jacaranda Street','Jeffreys Bay','Eastern Cape',NULL,193,'-34.033333','24.916668','042 888 0813',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(20,'Sliming Center','','28 6th Avenue, Walmer','Port Elizabeth','Eastern Cape',NULL,193,'-33.978668','25.594669','041 888 6568',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:06',NULL),(21,'Artcraft Centre','','43 3rd Avenue, Newton Park','Port Elizabeth','Eastern Cape',NULL,193,'-33.947609','25.568867','041 888 1257',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:07',NULL),(22,'Tams Hyperstore','','5 High Street','Cradock','Eastern Cape',NULL,193,'-32.175652','25.621719','048 888 3022',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:07',NULL),(23,'The Pool Lab','','17 Young Road, Mill Park','Port Elizabeth','Eastern Cape',NULL,193,'-33.965435','25.59059','083 888 1181',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:07',NULL),(24,'Brandons Club','','Leaping Frog Shopping Centre, Cnr William Nicol & Mulbarton Road, Fourways','Johannesburg','Gauteng',NULL,193,'-26.005112','28.020357','011 888 1224',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:07',NULL),(25,'Amanzi Club','','46 Longfellow Street, Ridgeway','Johannesburg','Gauteng',NULL,193,'-26.253469','27.996401','011 888 4569',NULL,'support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-08 11:44:08',NULL),(215,'White Shop','50% Clothes available','Central Park','Denver','CO','60204',223,'37.3990371','-105.6721263','333-3333-333','222-2222-22','support@agilelogix.com','https://netbaseteam.com','there is no description available for this store',1,1,NULL,'{\"mon\":\"1\",\"tue\":\"1\",\"wed\":\"1\",\"thu\":\"1\",\"fri\":\"1\",\"sat\":\"1\",\"sun\":\"1\"}','2016-06-20 15:18:19',NULL)";
			dbDelta( $sql );

			//store category relation
			$sql =  "INSERT INTO `{$prefix}stores_categories`(`id`,`category_id`,`store_id`,`created_on`) VALUES (1,16,1,'2016-06-08 11:59:49'),(2,16,2,'2016-06-08 12:00:00'),(3,7,3,'2016-06-08 12:00:39'),(4,14,3,'2016-06-08 12:00:52'),(5,11,4,'2016-06-08 12:01:29'),(6,11,5,'2016-06-08 12:01:30'),(7,11,6,'2016-06-08 12:01:31'),(8,11,7,'2016-06-08 12:01:31'),(9,16,8,'2016-06-08 12:10:09'),(10,11,9,'2016-06-08 12:10:10'),(11,17,1,'2016-06-08 12:10:12'),(12,17,11,'2016-06-08 12:10:12'),(13,17,12,'2016-06-08 12:10:13'),(14,7,13,'2016-06-08 12:10:14'),(15,11,14,'2016-06-08 12:10:15'),(16,7,15,'2016-06-08 12:10:16'),(17,1,16,'2016-06-08 12:10:17'),(18,16,17,'2016-06-08 12:10:18'),(19,16,18,'2016-06-08 12:10:19'),(20,13,19,'2016-06-08 12:10:20'),(21,7,20,'2016-06-08 12:10:21'),(22,1,21,'2016-06-08 12:10:22'),(23,4,22,'2016-06-08 12:10:23'),(24,11,23,'2016-06-08 12:10:24'),(25,14,24,'2016-06-08 12:10:25'),(26,14,25,'2016-06-08 12:10:26'),(261,2,215,'2016-06-20 15:18:19');";
			dbDelta( $sql );
		}


		//Add Ordering -> ordr and Feature Bit
		$sql 	= "SELECT count(*) as c FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$prefix}stores' AND COLUMN_NAME = 'ordr'";
		$result = $wpdb->get_results($sql);
		if($result[0]->c == 0) {

			$wpdb->query("ALTER TABLE {$prefix}stores ADD COLUMN `ordr` int(11) DEFAULT '0', ADD COLUMN `featured` tinyint(4) DEFAULT NULL;");
		}

		//Add Configs
		self::add_configs();

		//Store Timing
		require_once WOODASHBOARD_INC_DIR . 'modules/store-locator/includes/class-store-locator-helper.php';
		WooPanel_Store_Locator_Helper::fix_backward_compatible();
	
	}

	/**
	 * [upgrade_method Method which is invoked on Upgrade]
	 * @return [type] [description]
	 */
	public static function upgrade_method() {

		ini_set('max_execution_time', 0);

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
		global $wpdb;
		$charset_collate = 'utf8';
		$prefix 	 	 = WOOPANEL_STORE_LOCATOR_PREFIX;

		//Add Ordering -> ordr and Feature Bit
		$sql 	= "SELECT count(*) as c FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$prefix}stores' AND COLUMN_NAME = 'ordr'";
		$result = $wpdb->get_results($sql);
		if($result[0]->c == 0) {

			$wpdb->query("ALTER TABLE {$prefix}stores ADD COLUMN `ordr` int(11) DEFAULT '0', ADD COLUMN `featured` tinyint(4) DEFAULT NULL;");
		}

		//Add Configs
		self::add_configs();
	}


	private static function add_configs() {

		global $wpdb;
		$charset_collate = 'utf8';
		$prefix 	 	 = WOOPANEL_STORE_LOCATOR_PREFIX;


		$asl_configs = array(
			array('api_key','',''),
			array('default_lat','-33.947128',''),
			array('default_lng','25.591169',''),
			array('zoom','9',''),
			array('head_title','Stores',''),
			array('category_title','Category',''),
			array('no_item_text','No Store Found',''),
			array('map_type','roadmap',''),
			array('distance_unit','Miles',''),
			array('time_format','0',''),
			array('cluster','1',''),
			//NEW
			array('map_language','',''),
			array('map_region','',''),
			array('sort_by','',''),
		);

		foreach($asl_configs as $_config) {

			$key  = $_config[0];	
			$val  = $_config[1];	
			$type = $_config[2];

			//validate if not exist, else enter it
			$c = $wpdb->get_results("SELECT count(*) AS 'c' FROM `{$prefix}configs` WHERE `key` = '{$key}'");

			if($c[0]->c == 0) {

				$sql =  "INSERT INTO `{$prefix}configs`(`key`,`value`,`type`) VALUES ('{$key}','{$val}','{$type}');";
				dbDelta( $sql );
			}
		}
	}

}
