<?php
/**
 * Plugin Name:       Converselab
 * Plugin URI:        https://example.com
 * Description:       Starter plugin skeleton for Day 2.
 * Version:           1.0.0
 * Author:            Sumaya Islam
 * Text Domain:       converselab
 * Domain Path:       /languages
 */

if(!defined('ABSPATH')){
    exit;
}

/**
 * Constants
 */

define('CONVERSELAB_VERSION', '1.0.0');
define('CONVERSELAB_TEXT_DOMAIN', 'converselab');
define('CONVERSELAB_FILE', __FILE__ );
define('CONVERSELAB_PATH', plugin_dir_path(__FILE__));
define('CONVERSELAB_URL', plugin_dir_url(__FILE__));

/** 
 * Autoload + bootstrap
 */

require_once CONVERSELAB_PATH . 'includes/autoload.php';
require_once CONVERSELAB_PATH . 'includes/classes/class-noteposttype.php';
require_once CONVERSELAB_PATH . 'includes/classes/class-apiroutes.php';
/** 
 * Lifecycle hooks
 */

register_activation_hook(__FILE__, ['ConverseLab\Plugin', 'set_defaults']);
register_deactivation_hook(__FILE__, ['ConverseLab\Plugin', 'deactivate']);

ConverseLab\Plugin::init();
ConverseLab\NotePostType::init();
ConverseLab\ApiRoutes::init();