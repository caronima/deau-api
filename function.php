<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
} //endif

class deAU_API {

  /*** Initial execution ***/
  public function __construct() {
    if ( is_admin() ){
      add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'jquery_check' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
      add_action( 'admin_menu', array( $this, 'add_menu' ) );
      add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
    add_shortcode('deau', array( $this, 'deau_api_shortcodes_func') );
    add_shortcode('deau_history', array( $this, 'deau_api_shortcode_history_func') );
    add_shortcode('deau_seo_schema', array( $this, 'deau_api_seo_schema_func') );
  } //endfunction


  public function register_styles() {
    wp_enqueue_style( 'deau_api-style', DEAU_API_PLUGIN_URL.'/assets/deau-api.css', array(), DEAU_API_PLUGIN_VERSION, 'all' );
  } //endfunction

  public function jquery_check() {
    wp_enqueue_script('jquery');
  } //endfunction

  public function register_scripts() {
    wp_enqueue_script( 'deau_api-script', DEAU_API_PLUGIN_URL.'/assets/deau-api.js', array( 'jquery' ), DEAU_API_PLUGIN_VERSION, true );
  } //endfunction


  public function add_menu() {
    add_menu_page( DEAU_API_PLUGIN_NAME, 'deAU API', 'edit_others_posts', DEAU_API_PLUGIN_SLUG, array( $this, 'settings_page' ) , false );
  } //endfunction


  public function deau_api_remote_get( $url=false, $password=false, $update=false ) {
    if( $url ) {
      $args = array(
        'headers' => array(
          'x-deau-api-key' => $password
        )
      );
      $response = wp_remote_get( $url, $args );
      $result['http_code'] = wp_remote_retrieve_response_code( $response );
      $body = wp_remote_retrieve_body( $response );
      $body_array = json_decode($body, true);
      if( $result['http_code'] == 200 and isset($body_array['corporation']) ) {
        $result['body'] = $body;
        $result['body_array'] = $body_array;
        if( $update === 'update' ) {
          update_option( 'deau_api_localhost', $body );
        } //endif
      } else {
        $result['body'] = get_option('deau_api_localhost'); //ローカルホストのデータを使用
        $result['body_array'] = json_decode( get_option('deau_api_localhost'), true ); //ローカルホストのデータを使用
      } //endif
    } else {
      $result['http_code'] = 404;
      $result['body'] = get_option('deau_api_localhost'); //ローカルホストのデータを使用
      $result['body_array'] = json_decode( get_option('deau_api_localhost'), true ); //ローカルホストのデータを使用
    } //endif
    return $result;
  } //endfunction


  public function register_settings() {
    register_setting( 'deau_api-option-group', 'deau_api' );
    register_setting( 'deau_api-option-group', 'deau_api_shortcodes' );
    register_setting( 'deau_api-option-group', 'deau_api_shortcode_history' );
    register_setting( 'deau_api_localhost-option-group', 'deau_api_localhost' );
  } //endfunction


  public function settings_page() {
    $deau_option = get_option('deau_api'); //deAUのオプションデータを取得
    $deau_option_hojinbango = isset($deau_option['hojinbango']) ? sanitize_text_field( $deau_option['hojinbango'] ) : null;
    $deau_option_app_password = isset($deau_option['app_password']) ? sanitize_text_field( $deau_option['app_password'] ) : null;
    $deau_option_shortcodes = get_option('deau_api_shortcodes'); //deAUのオプションデータを取得
    $deau_option_shortcode_history = get_option('deau_api_shortcode_history'); //deAUのオプションデータを取得
?>
<div id="deau-api">
  <h2 class="ttl-myplugin"><i class="icon-deau_api-logo"></i><?php echo DEAU_API_PLUGIN_NAME; ?></h2><!-- /.ttl-myplugin -->
  <div class="inner-myplugin">
    <div class="main-myplugin">
      <form method="post" action="options.php" id="deau-api-form">
        <?php
    settings_fields( 'deau_api-option-group' );
    do_settings_sections( 'deau_api-option-group' );
        ?>
        <div id="deau_api-setting">
          <dl class="deau_api-setting-field">
            <dt class="required"><?php _e('法人番号', DEAU_API_TEXT_DOMAIN); ?></dt>
            <dd><input type="text" name="deau_api[hojinbango]" id="deau_api-hojinbango" value="<?php echo $deau_option_hojinbango; ?>" required pattern="^[0-9]+$"></dd>
          </dl><!-- /.deau_api-setting-field -->
          <dl class="deau_api-setting-field">
            <dt><?php _e('deAU Appパスワード', DEAU_API_TEXT_DOMAIN); ?></dt>
            <dd><input type="text" name="deau_api[app_password]" id="deau_api-app_password" value="<?php echo $deau_option_app_password; ?>"></dd>
          </dl><!-- /.deau_api-setting-field -->
        </div><!-- /#deau_api-setting -->
        <div class="deau_api-corp_data">
          <h2 class="deau_api-corp_data-title"><?php _e('法人情報', DEAU_API_TEXT_DOMAIN); ?></h2><!-- /.deau_api-corp_data-title -->
          <?php
    $result = $this->deau_api_remote_get( DEAU_APP_URL_WEBAPI.'/?hojinbango='.$deau_option_hojinbango, $deau_option_app_password, 'update' );
    $corp_data = $result['body_array'];

    if( $result['http_code'] !== 200 ) {
      echo '<p class="deau_api-error">'.__('ERROR! CODE 402: WebアプリのAPIが停止中です。', DEAU_API_TEXT_DOMAIN).'</p>';
    } else {
      if( isset($corp_data['code']) ) {
        if( $corp_data['code'] !== 200 ) {
          echo '<p class="deau_api-error">ERROR, CODE '.$corp_data['code'].': '.$corp_data['error'].'</p>';
        } else {
          if( isset( $corp_data['corporation']['基本情報'] ) and is_array( $corp_data['corporation']['基本情報'] ) ) {
            foreach( $corp_data['corporation']['基本情報'] as $metaName => $metaData ) {
              if( $metaName === '男女比率' ) {
                $metaData = json_decode($metaData, true);
          ?>
          <dl>
            <dt class="deau_api-key-copy">['<?php echo $metaName;?>']</dt>
            <dd>男性: <?php echo $metaData['male']; ?>人　女性: <?php echo $metaData['female']; ?>人</dd>
          </dl>
          <?php
              } else if( $metaName === '業種' ) {
          ?>
          <dl>
            <dt class="deau_api-key-copy">['<?php echo $metaName;?>']</dt>
            <dd><?php echo implode(", ", $metaData); ?></dd>
          </dl>
          <?php } else { ?>
          <dl>
            <dt class="deau_api-key-copy">['<?php echo $metaName;?>']</dt>
            <dd><?php echo $metaData;?></dd>
          </dl>
          <?php
                       } // endif
            }  // endforeach
            if( isset( $corp_data['corporation']['支店・営業所'] ) and is_array( $corp_data['corporation']['支店・営業所'] ) ) {
              foreach( $corp_data['corporation']['支店・営業所'] as $branch_name => $branch_data ) {
          ?>
          <dl class="deau_api-corp_data-branch">
            <dt><?php echo $branch_name; ?></dt>
            <dd>
              <ul>
                <?php
                if( isset($branch_data) and is_array( $branch_data ) ) {
                  foreach( $branch_data as $data_name => $data ) {
                    if($data) {
                ?>
                <li><span class="deau_api-key-copy">['<?php echo $branch_name.':'.$data_name;?>']</span><span class="deau_api-corp_data-branch-value"><?php echo $data;?></span></li>
                <?php
                    } // endif
                  } // endforeach
                } // endif
                ?>
              </ul>
            </dd>
          </dl><!-- /.deau_api-corp_data-branch -->
          <?php
              } // endforeach
            } // endif
          } // endif
          ?>
          <p class="deau_api-external-web_app"><a class="button" href="<?php echo DEAU_APP_URL_CORP_SINGLE; ?>/?hojinbango=<?php echo $deau_option_hojinbango; ?>" target="_blank"><?php _e('deAUのWebアプリでデータの編集・確認', DEAU_API_TEXT_DOMAIN); ?><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>
          <?php
        } // endif
      } else {
        if( isset($deau_option_hojinbango) ) {
          echo '<p class="deau_api-error">'.__('ERROR! CODE 404: WebアプリのAPIのエンドポイントが見つかりません。', DEAU_API_TEXT_DOMAIN).'</p>';
        } else {
          echo '<p>'.__('法人番号を入力してください。', DEAU_API_TEXT_DOMAIN).'</p>';
        } // endif
      } // endif
    } // endif
          ?>
        </div><!-- /.deau_api-corp_data -->
        <div id="deau_api-shortcodes">
          <p class="deau_api-shortcodes-title"><?php _e('ショートコード: ', DEAU_API_TEXT_DOMAIN); ?><span class="deau_api-key-copy">[deau slug="XXX"]</span></p><!-- /.deau_api-shortcodes-title -->
          <?php
    if( isset($deau_option_shortcodes) and is_array( $deau_option_shortcodes ) ) {
      $i=0;
      foreach( $deau_option_shortcodes as $deau_shortcode ) {
          ?>
          <div class="deau_api-shortcode-one">
            <input type="text" name="deau_api_shortcodes[<?php echo $i; ?>][slug]" id="deau_api-shortcode-slug-<?php echo $i; ?>" value="<?php echo sanitize_text_field( $deau_shortcode['slug'] ); ?>">
            <textarea name="deau_api_shortcodes[<?php echo $i; ?>][code]" id="deau_api-shortcode-code-<?php echo $i; ?>" cols="100" rows="10"><?php echo wp_kses_post($deau_shortcode['code']); ?></textarea>
            <span class="deau_api-shortcode-delete button"><?php _e('削除', DEAU_API_TEXT_DOMAIN); ?></span><!-- /.deau_api-shortcode-delete -->
          </div><!-- /.deau_api-shortcode-one -->
          <?php
        $i++;
      } // endforeach
    } // endif
          ?>
        </div><!-- /#deau_api-shortcodes -->
        <div id="new-deau_api_shortcode" data-deau_api_shortcodes-count="<?php echo isset($i) ? $i : null; ?>" data-deau_api_shortcode-delete_button_text="<?php _e('削除', DEAU_API_TEXT_DOMAIN); ?>" data-deau_api_shortcode-delete_confirm="<?php _e('このショートコードを削除しますか？この操作は取り消せません。', DEAU_API_TEXT_DOMAIN); ?>">
          <span class="button"><?php _e('ショートコードを作成', DEAU_API_TEXT_DOMAIN); ?></span>
        </div><!-- /#new-deau_api_shortcode -->

        <?php if( isset( $corp_data['corporation']['法人沿革'] ) and is_array( $corp_data['corporation']['法人沿革'] ) ) { ?>
        <div id="deau_api-shortcode-history">
          <h2 class="deau_api-shortcode-history-title"><?php _e('法人沿革', DEAU_API_TEXT_DOMAIN); ?></h2><!-- /.deau_api-shortcode-history-title -->
          <p class="deau_api-shortcode-history-explanation"><?php _e('沿革表示ショートコード: ', DEAU_API_TEXT_DOMAIN); ?><span class="deau_api-key-copy">[deau_history]</span></p><!-- /.deau_api-shortcode-history-explanation -->
          <ul class="deau_api-shortcode-history-codes">
            <li><textarea name="deau_api_shortcode_history[header]" id="deau_api-shortcode-history-header" cols="100" rows="1" placeholder="<dl>"><?php if( $deau_option_shortcode_history ) { echo wp_kses_post( $deau_option_shortcode_history['header'] ); } ?></textarea></li>
            <li><textarea name="deau_api_shortcode_history[body]" id="deau_api-shortcode-history-body" cols="100" rows="10" placeholder="<dt>['year']年（['year_jp']）['month']月</dt><dd>['event']</dd>"><?php if( $deau_option_shortcode_history ) { echo wp_kses_post( $deau_option_shortcode_history['body'] ); } ?></textarea></li>
            <li><textarea name="deau_api_shortcode_history[footer]" id="deau_api-shortcode-history-footer" cols="100" rows="1" placeholder="</dl>"><?php if( $deau_option_shortcode_history ) { echo wp_kses_post( $deau_option_shortcode_history['footer'] ); } ?></textarea></li>
          </ul><!-- /.deau_api-shortcode-history-codes -->
        </div><!-- /#deau_api-shortcode-history -->
        <p class="short-code-css"><?php _e('ショートコードで書き出されたHTML及び法人沿革のスタイルは、左メニューの  外観 -> カスタマイズ -> 追加 CSS にて編集できます。(Wordpress ver.4.7以降)', DEAU_API_TEXT_DOMAIN); ?></p><!-- /.short-code-css -->
        <?php } // endif
    submit_button(); //WPの標準関数
        ?>
      </form><!-- /#deau-api-form -->
    </div><!-- /.main-myplugin -->
    <?php require( DEAU_API_PLUGIN_PATH .'/assets/sidebar.php' ); ?>
  </div><!-- /.inner-myplugin -->
</div><!-- /#deau-api -->
<?php
  } //endfunction

  public function deau_api_shortcodes_func($atts) {
    $atts = shortcode_atts(array(
      'slug' => '',
      'content_type' => 'html',
      'data_type' => 'local',
      'split' => ', ',
    ),$atts);

    if( $atts['data_type'] === 'remote' ) {
      $deau_option = get_option('deau_api'); //deAUのオプションデータを取得
      $result = $this->deau_api_remote_get( DEAU_APP_URL_WEBAPI.'/?hojinbango='.$deau_option['hojinbango'], $deau_option['app_password'] );
      $corp_data = $result['body_array'];
    } else {
      $corp_data = json_decode( get_option('deau_api_localhost'), true ); //ローカルホストのデータを使用
    } // endif

    if( $atts['slug'] and $atts['content_type'] === 'html' ) {
      $slug_check = false;
      $deau_option_shortcodes = get_option('deau_api_shortcodes'); //deAUのオプションデータを取得
      if( isset( $deau_option_shortcodes ) and is_array( $deau_option_shortcodes ) ) {
        foreach( $deau_option_shortcodes as $deau_shortcode ) {
          if( $deau_shortcode['slug'] === $atts['slug'] ) {
            $code = $deau_shortcode['code'];
            $slug_check = true;
          } //enif
        } //endforeach
      } //endif
      if( $slug_check ) {
        while($slug_check) {
          if( mb_strpos($code, "['") !== false ) {
            $word_prev = mb_strpos($code,"['") + 2;
            $word_count = mb_strpos($code,"']")  - $word_prev;
            $word = mb_substr( $code, $word_prev, $word_count );
            $corp_data_word = $corp_data['corporation']['基本情報'][$word];
            if( isset( $corp_data_word ) ) {
              if( is_array( $corp_data_word ) ) {
                $code = str_replace("['".$word."']", implode( $atts['split'], $corp_data_word ), $code);
              } else if( $word === '男女比率' ) {
                $gender_ratio = json_decode($corp_data_word, true);
                $code = str_replace("['".$word."']",'男性: '.$gender_ratio['male'].'人　女性: '.$gender_ratio['female'].'人'  , $code);
              } else {
                $code = str_replace("['".$word."']", $corp_data_word, $code);
              }
            } else if(strpos($word,':') !== false) {
              $branch = explode(':', $word);
              $corp_data_branch = $corp_data['corporation']['支店・営業所'][$branch[0]][$branch[1]];
              if( isset( $corp_data_branch ) ) {
                $code = str_replace("['".$word."']", $corp_data_branch, $code);
              } else {
                $code = str_replace("['".$word."']", "[ERROR]", $code);
              }
            } else {
              $code = str_replace("['".$word."']", "[ERROR]", $code);
            }
          } else {
            $slug_check = false;
          } //endif
        } //endwhile
        return $code;
      } else {
        return __('ERROR: deAU APIのショートコードのslugの値が空か又はその値が存在しません。', DEAU_API_TEXT_DOMAIN);
      } //endif
    } else if( ! $atts['slug'] and $atts['content_type'] === 'json' ) {
      if( $atts['data_type'] === 'remote' ) {
        return $result['body']; //JSON書き出し
      } else {
        return get_option('deau_api_localhost'); //ローカルホストのデータからJSON書き出し
      } // endif
    } else {
      return __('ERROR: deAU APIのショートコードのslugの値を指定してください。', DEAU_API_TEXT_DOMAIN);
    } //endif
  } //endfunction


  public function deau_api_shortcode_history_func($atts) {
    $atts = shortcode_atts(array(
      'data_type' => 'local',
    ),$atts);

    if( $atts['data_type'] === 'remote' ) {
      $deau_option = get_option('deau_api'); //deAUのオプションデータを取得
      $deau_option_hojinbango = isset($deau_option['hojinbango']) ? sanitize_text_field( $deau_option['hojinbango'] ) : null;
      $deau_option_app_password = isset($deau_option['app_password']) ? sanitize_text_field( $deau_option['app_password'] ) : null;
      $result = $this->deau_api_remote_get( DEAU_APP_URL_WEBAPI.'/?hojinbango='.$deau_option_hojinbango, $deau_option_app_password );
      $corp_data = $result['body_array'];
    } else {
      $corp_data = json_decode( get_option('deau_api_localhost'), true ); //ローカルホストのデータを使用
    } // endif

    $deau_option_shortcode_history = get_option('deau_api_shortcode_history'); //deAUのオプションデータを取得
    if( isset( $deau_option_shortcode_history ) and is_array( $deau_option_shortcode_history ) ) {
      $history_code = $deau_option_shortcode_history['header'];
      $corp_data_histories = $corp_data['corporation']['法人沿革'];
      if( isset( $corp_data_histories ) and is_array( $corp_data_histories ) ) {
        foreach( $corp_data_histories  as $corp_history ) {
          $history_check = true;
          $history_code_body = $deau_option_shortcode_history['body'];
          while($history_check){
            if( mb_strpos($history_code_body, "['") !== false){
              $word_prev = mb_strpos($history_code_body, "['") + 2;
              $word_count = mb_strpos($history_code_body, "']") - $word_prev;
              $word = mb_substr( $history_code_body, $word_prev, $word_count );
              if( $word === 'year_jp' ) {
                $history_code_body = str_replace("['".$word."']", $this->get_jp_year( $corp_history['year'], $corp_history['month'] ), $history_code_body );
              } else {
                $history_code_body = str_replace("['".$word."']", $corp_history[$word], $history_code_body );
              } // endif
            } else {
              $history_check = false;
            } // endif
          } // endwhile
          $history_code .= $history_code_body;
        } // endforeach
      } //endif
      $history_code .= $deau_option_shortcode_history['footer'];
    } //endif
    return $history_code;
  } // endfunction


  public function get_jp_year($year, $month) {
    if( $year > 1868 and $year <= 1911  or $year == 1912 and $month < 8 ) {
      $j_year_num = $year - 1867;
      if( $j_year_num == 1 ) {
        $j_year = '明治元年';
      } else {
        $j_year = '明治'.$j_year_num.'年';
      } // endif
    } else if(( $year > 1912 and $year < 1927) or( $year == 1912 and $month > 7 ) ) {
      $j_year_num = $year - 1911;
      if( $j_year_num == 1 ) {
        $j_year = '大正元年';
      } else {
        $j_year = '大正'.$j_year_num.'年';
      } // endif
    } else if( $year > 1926 and $year < 1989 ){
      $j_year_num=$year - 1925;
      if( $j_year_num == 1 ) {
        $j_year = '昭和元年';
      } else {
        $j_year = '昭和'.$j_year_num.'年';
      } // endif
    } else if( ($year > 1988 and $year < 2019) or ($year == 2019 and $month < 5) ) {
      $j_year_num = $year - 1987;
      if($j_year_num == 1) {
        $j_year = '平成元年';
      } else {
        $j_year = '平成'.$j_year_num.'年';
      } // endif
    } else {
      $j_year_num=$year - 2018;
      if( $j_year_num == 1 ) {
        $j_year = '令和元年';
      } else {
        $j_year = '令和'.$j_year_num.'年';
      } // endif
    } // endif
    return $j_year;
  } // endfunction


  public function deau_api_seo_schema_func($atts) {
    if( isset($atts['data_type']) ) {
      $data_type = $atts['data_type'];
    } else {
      $data_type = 'local';
    } //endif

    if( $data_type === 'remote' ) {
      $deau_option = get_option('deau_api'); //deAUのオプションデータを取得
      $deau_option_hojinbango = isset($deau_option['hojinbango']) ? sanitize_text_field( $deau_option['hojinbango'] ) : null;
      $deau_option_app_password = isset($deau_option['app_password']) ? sanitize_text_field( $deau_option['app_password'] ) : null;
      $result = $this->deau_api_remote_get( DEAU_APP_URL_WEBAPI.'/?hojinbango='.$deau_option_hojinbango, $deau_option_app_password );
      $corp_data = $result['body_array'];
    } else {
      $corp_data = json_decode( get_option('deau_api_localhost'), true ); //ローカルホストのデータを使用
    } // endif

    $hojin = isset($corp_data['corporation']['基本情報']) ? $corp_data['corporation']['基本情報'] : null;
    if( isset( $hojin ) and is_array( $hojin ) ) {
      foreach( $hojin as $key => $value ) {
        if( $key === '法人種別' ) {
          if( $value === '株式会社' ) {
            $schema_type = 'Corporation';
          } else if( $value === '国の機関' or $value === '地方公共団体' ) {
            $type = 'GovernmentOrganization';
          } else {
            $schema_type = 'Organization';
          }
        } else if( $key === '法人名' ) {
          $schema_name = $value;
        } else if( $key === '法人名英語' ) {
          $schema_alternatename = $value;
        } else if( $key === '郵便番号' ) {
          $schema_address_postalcode = $value;
        } else if( $key === '都道府県' ) {
          $schema_address_region = $value;
        } else if( $key === '市区町村' ) {
          $schema_address_locality = $value;
        } else if( $key === '番地' ) {
          $schema_address_street = $value;
        } else if( $key === '事業内容' ) {
          $schema_description = $value;
        } else if( $key === '代表者' ) {
          $schema_founder = $value;
        } else if( $key === '創設者' or $key === '設立者' ) {
          $schema_founder = $value;
        } else if( $key === '設立日' ) {
          $schema_foundingdate = $value;
        } else if( $key === '電話番号' ) {
          $schema_contactpoint_telephone = $value;
        } else if( $key === 'Eメール' ) {
          $schema_contactpoint_email = $value;
        } else if( $key === 'DUNS' or $key === 'DUNSナンバー' ) {
          $schema_duns = $value;
        } else if( $key === 'ロゴ' ) {
          $schema_logo = $value;
        } else if( $key === 'RSSフィード' ) {
          $no_output = $value;
        } else if( $key === 'Webサイト' ) {
          $schema_url = $value;
        } else if( filter_var( $value, FILTER_VALIDATE_URL ) ) {
          $needle = $schema_url;
          if( strpos($value, $needle) == false ) {
            //$valueのなかに$needleが含まれていない場合
            $schema_sameas_url[] = $value;
          } //endif
        } //endif
      } //endforeach
    } //endif

    $atts = shortcode_atts(array(
      'context' => 'https://schema.org/',
      'type' => isset($schema_type) ? $schema_type : null,
      'name' => isset($schema_name) ? $schema_name : null,
      'alternate_name' => isset($schema_alternatename) ? $schema_alternatename : null,
      'description' => isset($schema_description) ? wp_strip_all_tags($schema_description, true) : null,
      'duns' => isset($schema_duns) ? $schema_duns : null,
      'founder' => isset($schema_founder) ? $schema_founder : null,
      'founding_date' => isset($schema_foundingdate) ? $schema_foundingdate : null,
      'logo' => isset($schema_logo) ? $schema_logo : null,
      'url' => isset($schema_url) ? $schema_url : null,
      'address_locality' => isset($schema_address_locality) ? $schema_address_locality : null,
      'address_region' => isset($schema_address_region) ? $schema_address_region : null,
      'address_postalcode' => isset($schema_address_postalcode) ? $schema_address_postalcode : null,
      'address_street' => isset($schema_address_street) ? $schema_address_street : null,
      'address_country' => 'JP',
      'sameas_url' => isset($schema_sameas_url) ? $schema_sameas_url : null,
      'contact_telephone' => isset($schema_contactpoint_telephone) ? '+81 '.$schema_contactpoint_telephone : null,
      'contact_email' => isset($schema_contactpoint_email) ? $schema_contactpoint_email : null,
      'contact_type' => 'sales',
    ),$atts);

    $data['@context'] = $atts['context'];
    $data['@type'] = $atts['type'];
    $data['name'] = $atts['name'];
    $data['alternateName'] = $atts['alternate_name'];
    $data['description'] = $atts['description'];
    $data['duns'] = $atts['duns'];
    $data['founder'] = array(
      '@type' => 'Person',
      'name' => $atts['founder'],
    );
    $data['foundingDate'] = $atts['founding_date'];
    $data['logo'] = array(
      '@type' => 'ImageObject',
      'url' => $atts['logo']
    );
    $data['url'] = $atts['url'];
    $data['address'] = array(
      '@type' => 'PostalAddress',
      'addressLocality' => $atts['address_locality'],
      'addressRegion' => $atts['address_region'],
      'postalCode' => $atts['address_postalcode'],
      'streetAddress' => $atts['address_street'],
      'addressCountry' => $atts['address_country']
    );
    $data['contactPoint'] = array(
      array(
        '@type' => 'ContactPoint',
        'telephone' => $atts['contact_telephone'],
        'email' => $atts['contact_email'],
        'contactType' => $atts['contact_type']
      )
    );
    $data['sameAs'] = isset($schema_sameas_url) ? $schema_sameas_url : null;

    $data = array_filter($data); //配列の値が空の要素は除去
    return json_encode( $data, JSON_UNESCAPED_UNICODE );
  } //endfunction

} //endclass
