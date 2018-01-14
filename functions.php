<?php
require_once 'lib/_defins.php'; //定数を定義
//require_once 'lib/admin.php'; //管理者機能（functions.phpで呼ばないと動作しないので）
//require_once 'lib/admin-tinymce-qtag.php'; //管理者用編集ボタン機能


//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content =  preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content =  strip_shortcodes($content);//ショートコード削除
  $content =  strip_tags($content);//タグの除去
  $content =  str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content =  preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content =  preg_replace(URL_REG, '', $content); //URLを取り除く
  // $content =  preg_replace('/\s/iu',"",$content); //余分な空白を削除
  $over    =  intval(mb_strlen($content)) > intval($length);
  $content =  mb_substr($content, 0, $length);//文字列を指定した長さで切り取る

  return $content;
}
endif;

//WP_Queryの引数を取得
if ( !function_exists( 'get_related_wp_query_args' ) ):
function get_related_wp_query_args(){
  global $post;
  if (!$post) {
    $post = get_random_posts(1);
  }
  //var_dump($post);
  if ( true ) {
  //if ( is_related_entry_association_category() ) {
    //カテゴリ情報から関連記事をランダムに呼び出す
    $categories = get_the_category($post->ID);
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category->cat_ID);
    endforeach ;
    if ( empty($category_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post->ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'category__in' => $category_IDs,
      'orderby' => 'rand',
    );
  } else {
    //タグ情報から関連記事をランダムに呼び出す
    $tags = wp_get_post_tags($post->ID);
    $tag_IDs = array();
    foreach($tags as $tag):
      array_push( $tag_IDs, $tag->term_id);
    endforeach ;
    if ( empty($tag_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post -> ID),
      'posts_per_page'=> intval(10),
      //'posts_per_page'=> intval(get_related_entry_count()),
      'tag__in' => $tag_IDs,
      'orderby' => 'rand',
    );
  }
}
endif;

//images/no-image.pngを使用するimgタグに出力するサイズ関係の属性
if ( !function_exists( 'get_noimage_sizes_attr' ) ):
function get_noimage_sizes_attr($image = null){
  if (!$image) {
    $image = get_template_directory_uri().'/images/no-image-160.png';
  }
  $sizes = ' srcset="'.$image.' 160w" width="160" height="90" sizes="(max-width: 160px) 160vw, 90px"';
  return $sizes;
}
endif;

//投稿ナビのサムネイルタグを取得する
if ( !function_exists( 'get_post_navi_thumbnail_tag' ) ):
function get_post_navi_thumbnail_tag($id, $width = 120, $height = 67){
  $thumb = get_the_post_thumbnail( $id, array($width, $height), array('alt' => '') );
  if ( !$thumb ) {
    $image = get_template_directory_uri().'/images/no-image-%s.png';
    //表示タイプ＝デフォルト
    if ($width == 120) {
      $w = '160';
      $image = sprintf($image, $w);
      $wh_attr = ' srcset="'.$image.' 120w" width="120" height="67" sizes="(max-width: 120px) 120vw, 67px"';
    } else {//表示タイプ＝スクエア
      $w = '150';
      $image = sprintf($image, $w);
      $wh_attr = ' srcset="'.$image.' 120w" width="120" height="120" sizes="(max-width: 120px) 120vw, 120px"';
    }
    $thumb = '<img src="'.$image.'" alt="NO IMAGE" class="no-image post-navi-no-image"'.$wh_attr.' />';
  }
  return $thumb;
}
endif;

///////////////////////////////////////
// グローバルナビに説明文を加えるウォーカークラス
///////////////////////////////////////
class menu_description_walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    global $wp_query;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    //$classes[] = 'fa';
    if ($item->description) {
      $classes[] = 'menu-item-has-description';
    }
    //var_dump($classes);

    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
    $class_names = ' class="'. esc_attr( $class_names ) . '"';
    $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

    $prepend = '<div class="item-label">';
    $append = '</div>';
    $description  = ! empty( $item->description ) ? '<div class="item-description sub-caption">'.esc_attr( $item->description ).'</div>' : '';

    // if($depth != 0) {
    //   $description = $append = $prepend = "";
    // }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= '<div class="caption-wrap">';
    $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
    $item_output .= $description.$args->link_after;
    $item_output .= '</div>';
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}

//アーカイブタイトルの取得
if ( !function_exists( 'get_archive_chapter_title' ) ):
function get_archive_chapter_title(){
  $chapter_title = null;
  if( is_category() ) {//カテゴリページの場合
    $cat_id = get_query_var('cat');
    $icon_font = '<span class="fa fa-folder-open"></span>';
    if ($cat_id && get_category_title($cat_id)) {
      $chapter_title .= $icon_font.get_category_title($cat_id);
    } else {
      $chapter_title .= single_cat_title( $icon_font, false );
    }
  } elseif( is_tag() ) {//タグページの場合
    $chapter_title .= single_tag_title( '<span class="fa fa-tags"></span>
', false );
  } elseif( is_tax() ) {//タクソノミページの場合
    $chapter_title .= single_term_title( '', false );
  } elseif (is_day()) {
    //年月日のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y-m-n');
  } elseif (is_month()) {
    //年と月のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y-m');
  } elseif (is_year()) {
    //年のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y');
  } elseif (is_author()) {//著書ページの場合
    $chapter_title .= esc_html(get_queried_object()->display_name);
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
    $chapter_title .= 'Archives';
  } else {
    $chapter_title .= 'Archives';
  }
  return $chapter_title;
}
endif;

//アーカイブ見出しの取得
if ( !function_exists( 'get_archive_chapter_text' ) ):
function get_archive_chapter_text(){
  $chapter_text = null;
  //アーカイブタイトル前
  //$chapter_text .= '<span class="archive-title-pb">'.__( '"', THEME_NAME ).'</span><span class="archive-title-text">';
  //アーカイブタイトルの取得
  $chapter_text .= get_archive_chapter_title();
  //アーカイブタイトル後
  //$chapter_text .= '</span><span class="archive-title-pa">'.__( '"', THEME_NAME );//.'</span><span class="archive-title-list-text">'.get_theme_text_list().'</span>';
  //返り値として返す
  return $chapter_text;
}
endif;

//'wp-color-picker'の呼び出し順操作（最初の方に読み込む）
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_custom');
if ( !function_exists( 'admin_enqueue_scripts_custom' ) ):
function admin_enqueue_scripts_custom($hook) {
    wp_enqueue_script('colorpicker-script', get_template_directory_uri() . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
}
endif;

//投稿管理画面のカテゴリリストの階層を保つ
add_filter('wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2);
if ( !function_exists( 'solecolor_wp_terms_checklist_args' ) ):
function solecolor_wp_terms_checklist_args( $args, $post_id ){
 if ( isset($args['checked_ontop']) && ($args['checked_ontop'] !== false )){
    $args['checked_ontop'] = false;
 }
 return $args;
}
endif;


//カスタムフィールドのショートコードをロケーションURIに置換
if ( !function_exists( 'replace_directory_uri' ) ):
function replace_directory_uri($code){
  $code = str_replace('[template_directory_uri]', get_template_directory_uri(), $code);
  $code = str_replace('[stylesheet_directory_uri]', get_stylesheet_directory_uri(), $code);
  $code = str_replace('<?php echo template_directory_uri(); ?>', get_template_directory_uri(), $code);
  $code = str_replace('<?php echo get_stylesheet_directory_uri(); ?>', get_stylesheet_directory_uri(), $code);
  return $code;
}
endif;

/*
add_action('comment_form','google_recaptcha_script');
function google_recaptcha_script(){
  echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}

add_action('comment_form','display_google_recaptcha');
function display_google_recaptcha() { ?>
  <div class="g-recaptcha" data-sitekey="6LehHTgUAAAAALR98L7a600Cgqlf2aV3zMFhBWzV"></div>
<?php }



add_action('pre_comment_on_post', 'verify_google_recaptcha');
function verify_google_recaptcha($comment_post_ID)
{
  if (isset($_POST['g-recaptcha-response'])) {
    $secret_key = '6LehHTgUAAAAAEkr_HmJ7WkkICDgEMb1Pt92g4H2';
    $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $secret_key ."&response=". $_POST['g-recaptcha-response']);
    $response = json_decode($response["body"], true);
    if ($response["success"] == true) {
      return;
    } else {
      // $errors->add("reCaptcha Invalid", __("Ошибка Регистрации: Похоже вы не человек.","textdomain"));
      wp_die(__( 'reCaptchaにより投稿が拒否されました。', THEME_NAME ));
    }
  } else {
    //$errors->add("reCaptcha Invalid", __("Ошибка Регистрации: Похоже вы бот. Если у вас отключен JavaScript","textdomain"));
    wp_die(__( 'reCaptchaにより投稿が拒否されました。', THEME_NAME ));
  }
  return;
}
*/

// remove_action('do_feed_rdf', 'do_feed_rdf');
// remove_action('do_feed_rss', 'do_feed_rss');
// remove_action('do_feed_rss2', 'do_feed_rss2');
// remove_action('do_feed_atom', 'do_feed_atom');

//wp 関数内でクエリが解析されて投稿が読み込まれ、テンプレートが実行されるまでの間に実行する。
//出力にテンプレートを必要しないデータにアクセスする場合に活用できる。
add_action( 'wp','do_access_counting', 1 );
if ( !function_exists( 'do_access_counting' ) ):
function do_access_counting() {
  // global $post;
  // _v($post);
  //_v(!is_admin());
    // _v(is_singular());
  //アクセス数をカウントする
  if (!is_admin() && is_singular()) {
    //アクセス数のカウント
    if (is_access_count_enable()) {

      count_this_page_access();
    }
  }
}
endif;

//リダイレクト
add_action( 'wp','wp_singular_page_redirect', 0 );
if ( !function_exists( 'wp_singular_page_redirect' ) ):
function wp_singular_page_redirect() {
  //リダイレクト
  if (is_singular() && $redirect_url = get_singular_redirect_url()) {
    //URL形式にマッチする場合
    if (preg_match(URL_REG, $redirect_url)) {
      redirect_to_url($redirect_url);
    }
  }
}
endif;
/*
///////////////////////////////////////
// 自前でプロフィール画像のアップロード
///////////////////////////////////////
//プロフィール画面で設定したプロフィール画像
if ( !function_exists( 'get_the_author_upladed_avatar_url' ) ):
function get_the_author_upladed_avatar_url($user_id){
  if (!$user_id) {
    $user_id = get_the_posts_author_id();
  }
  return esc_html(get_the_author_meta('upladed_avatar', $user_id));
}
endif;

//ユーザー情報追加
add_action('show_user_profile', 'add_avatar_to_user_profile');
add_action('edit_user_profile', 'add_avatar_to_user_profile');
if ( !function_exists( 'add_avatar_to_user_profile' ) ):
function add_avatar_to_user_profile($user) {
?>
  <h3><?php _e( 'プロフィール画像', THEME_NAME ) ?></h3>
  <table class="form-table">
    <tr>
      <th>
        <label for="avatar"><?php _e( 'プロフィール画像のアップロード', THEME_NAME ) ?></label>
      </th>
      <td>
      <?php
        generate_upload_image_tag('upladed_avatar', get_the_author_upladed_avatar_url($user->ID));
       ?>
       <p class="description"><?php _e( '自前でプロフィール画像をアップロードする場合は画像を選択してください。Gravatarよりこちらのプロフィール画像が優先されます。300×300以上の正方形の画像がお勧めです。', THEME_NAME ) ?><?php _e( 'ページサイズ縮小のため<a href="https://tinypng.com/" target="_blank">TinyPNG</a>等で登録前にで圧縮することをおすすめします。', THEME_NAME ) ?></p>
      </td>
    </tr>
  </table>
<?php
}
endif;

//入力した値を保存する
add_action('personal_options_update', 'update_avatar_to_user_profile');
if ( !function_exists( 'update_avatar_to_user_profile' ) ):
function update_avatar_to_user_profile($user_id) {
  if ( current_user_can('edit_user',$user_id) ){
    update_user_meta($user_id, 'upladed_avatar', $_POST['upladed_avatar']);
  }
}
endif;

//プロフィール画像を変更する
add_filter( 'get_avatar' , 'get_uploaded_user_profile_avatar' , 1 , 5 );
if ( !function_exists( 'get_uploaded_user_profile_avatar' ) ):
function get_uploaded_user_profile_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
  if ( is_numeric( $id_or_email ) )
    $user_id = (int) $id_or_email;
  elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) )
    $user_id = $user->ID;
  elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) )
    $user_id = (int) $id_or_email->user_id;

  if ( empty( $user_id ) )
    return $avatar;

  if (get_the_author_upladed_avatar_url($user_id)) {
    $alt = !empty($alt) ? $alt : get_the_author_meta( 'display_name', $user_id );;
    $author_class = is_author( $user_id ) ? ' current-author' : '' ;
    $avatar = "<img alt='" . esc_attr( $alt ) . "' src='" . esc_url( get_the_author_upladed_avatar_url($user_id) ) . "' class='avatar avatar-{$size}{$author_class} photo' height='{$size}' width='{$size}' />";
  }

  return $avatar;
}
endif;
*/

// add_action( 'wp_loaded','my_minify_html' );
// function my_minify_html() {
//   // Use html_compress($html) function to minify html codes.
//   ob_start('html_compress');
// }

// function html_compress( $html ) {
//   // Some minify codes here...
//   _v($html);
//   return $html;
// }




// add_filter( 'wpex_shortcodes_tinymce_json', function( $data ) {
//   _v($data);

//   // Add your custom shortcode
//   $data['shortcodes']['my_custom_shortcode'] = array(
//     'text' => __( 'Custom Shortcode', 'total' ),
//     'insert' => '[custom_shortcode parameter1="value"]',
//   );

//   // Return data
//   return $data;

// } );


/*
  add_action('admin_head', 'better_shortcodes_in_js');
  add_action('admin_head', 'better_tinymce');

  function better_shortcodes_in_js(){
    $listTitle = trim(esc_attr(get_option('btslb_listTitle')));
    if(!$listTitle){
      $listTitle = "タグ";
    }
    $shortcodes = esc_attr(get_option('btslb_shortcodes'));
    if($shortcodes==''){
      $shortcodes = '""';
    }
?>
  <script type="text/javascript">
  var listTitle = "<?php echo $listTitle; ?>";
  var btslb_shortcodes = <?php echo html_entity_decode($shortcodes); ?>;
  </script>
<?php
}

  function better_tinymce() {
    add_filter('mce_external_plugins', 'better_tinymce_plugin');
    add_filter('mce_buttons_2', 'better_tinymce_button');
  }

  function better_tinymce_plugin($plugin_array) {
    $plugin_array['shortcodedrop'] = get_template_directory_uri().'/js/betterDrop.js';
    return $plugin_array;
  }

  function better_tinymce_button($buttons) {
    array_push($buttons, 'shortcodedrop');
    return $buttons;
  }
*/











/*
add_action('admin_init', 'shortcode_button');
add_action('admin_head', 'get_shortcodes');


function shortcode_button()
{
    if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
    {
        add_filter( 'mce_external_plugins',  'add_buttons' );
        add_filter( 'mce_buttons',  'register_buttons' );
    }
}

function add_buttons( $plugin_array )
    {
       //$path=get_template_directory_uri() . '/lib/tinymce/rashortcodes.php';
       $path=get_template_directory_uri() . '/js/shortcode-tinymce-button.js';
        $plugin_array['rashortcodes'] = $path;
      //_v($plugin_array);

        return $plugin_array;
    }

function register_buttons( $buttons )
    {
        array_push( $buttons, 'separator', 'rashortcodes' );
        return $buttons;
    }

function get_shortcodes()
    {
        global $shortcode_tags;
        //_v($shortcode_tags);

        echo '<script type="text/javascript">
        var shortcodes_button = new Array();';

        $count = 0;

        foreach($shortcode_tags as $tag => $code)
        {?>
            var count = <?php echo $count; ?>;
            shortcodes_button[count] = new Array();
            shortcodes_button[count].code = '<?php echo $tag; ?>';

            <?php
            $count++;
        }

        echo '</script>';
    }

*/
