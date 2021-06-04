<?php
/**
 * Plugin Name: deAU クラウド法人情報 API
 * Plugin URI: https://deau.app/
 * Description: deAU クラウド法人情報のAPIで法人データを取得。
 * Version: 1.0.1
 * Requires at least: 4.8
 * Requires PHP: 5.4.0
 * Author: Caronima Inc.
 * Author URI: https://caronima.com
 * Text Domain: deau-api
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
} //endif

$this_plugin_info = get_file_data( __FILE__, array(
  'name' => 'Plugin Name',
  'version' => 'Version',
  'text_domain' => 'Text Domain',
  'minimum_php' => 'Requires PHP',
));

define( 'DEAU_API_PLUGIN_PATH', rtrim( plugin_dir_path( __FILE__ ), '/') );
define( 'DEAU_API_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/') );
define( 'DEAU_API_PLUGIN_SLUG', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
define( 'DEAU_API_PLUGIN_NAME', $this_plugin_info['name'] );
define( 'DEAU_API_PLUGIN_VERSION', $this_plugin_info['version'] );
define( 'DEAU_API_TEXT_DOMAIN', $this_plugin_info['text_domain'] );

load_plugin_textdomain( DEAU_API_TEXT_DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

/*** Require PHP Version Check ***/
if ( version_compare(phpversion(), $this_plugin_info['minimum_php'], '<') ) {
  $plugin_notice = sprintf( __('このプラグインは、PHP %s 以上が必要になります。', DEAU_API_TEXT_DOMAIN), $this_plugin_info['minimum_php'] );
  register_activation_hook(__FILE__, create_function('', "deactivate_plugins('".plugin_basename( __FILE__ )."'); wp_die('{$plugin_notice}');"));
} //endif

if( ! class_exists( 'deAU_API' ) ) {
  define( 'DEAU_APP_URL', rtrim( 'https://deau.app', '/') );
  define( 'DEAU_APP_URL_WEBAPI', rtrim( DEAU_APP_URL.'/web-api/json/v1', '/') );
  define( 'DEAU_APP_URL_CORP_SINGLE', rtrim( DEAU_APP_URL.'/corporations/single', '/') );
  require( DEAU_API_PLUGIN_PATH.'/function.php' );
  /****** deAU_API Initialize ******/
  function deau_api_initialize() {
    global $deau_api;
    /* Instantiate only once. */
    if( ! isset($deau_api) ) {
      $deau_api = new deAU_API();
    }
    return $deau_api;
  } //endfunction
  /*** Instantiate ****/
  deau_api_initialize();

  /*** How to use this Shortcode ***/
  /*
  * [deau slug="string"]
  * [deau_history]
  * [deau_seo_schema]
  */

  /****** Uninstall ******/
  require( DEAU_API_PLUGIN_PATH .'/assets/uninstall.php' );
  register_uninstall_hook( __FILE__, array('deAU_API_Uninstall', 'delete_options') ); // plugin uninstallation
  //register_deactivation_hook( __FILE__, array('deAU_API_Uninstall', 'delete_options') ); // plugin deactivation
} else {
  $plugin_notice = __('PHPのクラス名が競合しています。', DEAU_API_TEXT_DOMAIN);
  wp_die($plugin_notice);
} //endif
