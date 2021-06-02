<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
} //endif
?>
<div class="sidebar-myplugin">
  <div class="author-myplugin">
    <h3 class="ttl-sub-myplugin"><i class="icon-deau_api-logo"></i><?php echo DEAU_API_PLUGIN_NAME; ?></h3><!-- /.ttl-sub-myplugin -->
    <p><?php _e('開発者', DEAU_API_TEXT_DOMAIN); ?>: <?php _e('株式会社カロニマ', DEAU_API_TEXT_DOMAIN); ?></p>
    <ul>
      <li><a href="https://www.youtube.com/channel/UCte2Fu9FT6fSNQ6JhcWPPHA" target="_blank"><i aria-hidden="true" class="dashicons dashicons-video-alt3"></i><?php _e('WebアプリのYouTube', DEAU_API_TEXT_DOMAIN); ?></a></li>
      <li><a href="https://www.facebook.com/deau.jp" target="_blank"><i aria-hidden="true" class="dashicons dashicons-facebook"></i><?php _e('WebアプリのFacebook', DEAU_API_TEXT_DOMAIN); ?></a></li>
      <li><a href="https://twitter.com/deau_jp" target="_blank"><i aria-hidden="true" class="dashicons dashicons-twitter"></i><?php _e('WebアプリのTwitter', DEAU_API_TEXT_DOMAIN); ?></a></li>
      <li><a href="https://deau.app" target="_blank"><i aria-hidden="true" class="dashicons dashicons-external"></i><?php _e('Webアプリ', DEAU_API_TEXT_DOMAIN); ?></a></li>
      <li><a href="https://deau.app/legal/terms/" target="_blank"><i aria-hidden="true" class="dashicons dashicons-external"></i><?php _e('Webアプリの利用規約', DEAU_API_TEXT_DOMAIN); ?></a></li>
      <li><a href="https://deau.app/legal/privacy/" target="_blank"><i aria-hidden="true" class="dashicons dashicons-external"></i><?php _e('Webアプリのプライバシーポリシー', DEAU_API_TEXT_DOMAIN); ?></a></li>
      <li><a href="https://caronima.com" target="_blank"><i aria-hidden="true" class="dashicons dashicons-external"></i><?php _e('開発者のWebサイト', DEAU_API_TEXT_DOMAIN); ?></a></li>
    </ul>
  </div><!-- /.author-myplugin -->
  <dl class="info-myplugin">
    <dt><?php _e('プラグイン名', DEAU_API_TEXT_DOMAIN); ?>:</dt>
    <dd><?php echo DEAU_API_PLUGIN_NAME; ?></dd>
    <dt><?php _e('プラグインバージョン', DEAU_API_TEXT_DOMAIN); ?>:</dt>
    <dd><?php echo DEAU_API_PLUGIN_VERSION; ?></dd>
    <dt><?php _e('Webアプリ名', DEAU_API_TEXT_DOMAIN); ?>:</dt>
    <dd>deAU クラウド法人情報</dd>
  </dl><!-- /.info-myplugin -->
</div><!-- /.sidebar-myplugin -->
