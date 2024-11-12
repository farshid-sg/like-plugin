<?php
/*
PLugin Name: پلاگین لایک
description:  پلاگین لایک
*/
define ('LIKE_URL',plugin_dir_url(__FILE__));
define ('LIKE_ASSETS',plugin_dir_url(__FILE__) . 'assets/');
define ('LIKE_PATH',plugin_dir_path(__FILE__));

include LIKE_PATH . 'inc/fucntion.php';

register_activation_hook(__FILE__,function(){
    global $wpdb;
    $tblPrefix = $wpdb->prefix;
    $tblName = $tblPrefix.'liked';
    $sql = "
    CREATE TABLE `$tblName` (
        `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `post_id` bigint(20) unsigned NOT NULL,
        `user_id` bigint(20) unsigned NOT NULL,
        `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
        `like_post` tinyint(1) NOT NULL DEFAULT '1',
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`ID`),
        KEY `post_id` (`post_id`),
        KEY `user_id` (`user_id`)
       ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});



   