<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
} //endif
if( ! class_exists( 'deAU_API_Uninstall' ) ) {
  class deAU_API_Uninstall {
    public static function delete_options() {
      delete_option('deau_api');
      delete_option('deau_api_shortcodes');
      delete_option('deau_api_shortcode_history');
      delete_option('deau_api_localhost');
    } //endfunction
  } //endclass
} //endif
