<?php

/*
Plugin Name: Image Map Pro v6
Plugin URI: http://www.imagemappro.com/
Version: 6.0.5
Author: Webcraft Plugins Ltd.
Description: The most advanced image map builder for WordPress
*/

require_once plugin_dir_path( __FILE__ ) . 'includes/admin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/storage.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/gutenberg.php';

if (!class_exists('ImageMapPro')) {
	class ImageMapPro_v6 {
		function __construct() {
			$this->version = '6.0.5';

			if (class_exists('ImageMapPro_v6_Admin')) {
				$this->admin = new ImageMapPro_v6_Admin($this->version);
			}

			if (class_exists('ImageMapPro_v6_Storage')) {
				$this->storage = new ImageMapPro_v6_Storage();
			}

			if (class_exists('ImageMapPro_v6_Shortcode')) {
				$this->shortcode = new ImageMapPro_v6_Shortcode($this->storage, $this->version);
			}

			if (class_exists('ImageMapPro_v6_Gutenberg')) {
				$this->gutenberg = new ImageMapPro_v6_Gutenberg($this->version);
			}
		}
	}
}

if (class_exists('ImageMapPro_v6')) {
	$instance = new ImageMapPro_v6();
}


