<?php //スタイリング用の追加クラス関数

//bodyクラスの追加関数
add_filter('body_class', 'body_class_additional');
if ( !function_exists( 'body_class_additional' ) ):
function body_class_additional($classes) {
  global $post;
  //カテゴリ入りクラスの追加
  if ( is_single() ) {
    foreach((get_the_category($post->ID)) as $category)
      $classes[] = 'categoryid-'.$category->cat_ID;
  }

  //サイドバーにウィジェットが入っていない場合
  if (!is_active_sidebar( 'sidebar' )) {
    $classes[] = 'no-sidebar';
  }

  //サイドバー追従領域にウィジェットが入っていない場合
  if (!is_scrollable_sidebar_enable()) {
    $classes[] = 'no-scrollable-sidebar';
  }

  return $classes;
}
endif;


//メインカラムの追加関数
if ( !function_exists( 'get_additional_main_classes' ) ):
function get_additional_main_classes($option = null){
  $classes = null;
  // //サイドバーにウィジェットが入っていない場合
  // if (!is_active_sidebar( 'sidebar' )) {
  //   $classes .= ' no-sidebar';
  // }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;}
endif;

//メインカラムの追加関数
if ( !function_exists( 'get_additional_entry_content_classes' ) ):
function get_additional_entry_content_classes($option = null){
  $classes = null;
  //画像の枠線エフェクトが設定されている場合
  switch (get_image_wrap_effect()) {
    case 'border':
      $classes .= ' iwe-border';
      break;
    case 'border_bold':
      $classes .= ' iwe-border-bold';
      break;
    case 'shadow':
      $classes .= ' iwe-shadow';
      break;
    default:

      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//エントリーカードの追加関数
if ( !function_exists( 'get_additional_new_entriy_cards_classes' ) ):
function get_additional_new_entriy_cards_classes($option = null){
  global $g_entry_type;
  $classes = null;
  if ($g_entry_type != ET_DEFAULT) {
    $classes .= ' not-default';
    if ($g_entry_type == ET_LARGE_THUMB) {
      $classes .= ' large-thumb';
    } else if ($g_entry_type == ET_LARGE_THUMB_ON) {
      $classes .= ' large-thumb-on';
    }
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//SNSシェアボタンの追加関数
if ( !function_exists( 'get_additional_sns_share_button_classes' ) ):
function get_additional_sns_share_button_classes($option = null){
  $classes = null;
  //カラム数
  if ($option == SS_TOP) {
    $value = get_sns_top_share_column_count();
  } else {
    $value = get_sns_share_column_count();
  }
  switch ($value) {
    case 1:
      $classes .= ' ss-col-1';
      break;
    case 2:
      $classes .= ' ss-col-2';
      break;
    case 3:
      $classes .= ' ss-col-3';
      break;
    case 4:
      $classes .= ' ss-col-4';
      break;
    case 5:
      $classes .= ' ss-col-5';
      break;
    default:
      $classes .= ' ss-col-6';
      break;
  }

  //ロゴとキャプションの配置
  if ($option == SS_TOP) {
    $value = get_sns_top_share_logo_caption_position();
  } else {
    $value = get_sns_share_logo_caption_position();
  }
  switch ($value) {
    case 'high_and_low_lc':
      $classes .= ' ss-high-and-low-lc';
      break;
    case 'high_and_low_cl':
      $classes .= ' ss-high-and-low-cl';
      break;
    default:

      break;
  }

  //ボタンカラー
  if ($option == SS_TOP) {
    $value = get_sns_top_share_button_color();
  } else {
    $value = get_sns_share_button_color();
  }
  switch ($value) {
    case 'brand_color':
      $classes .= ' bc-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' bc-brand-color-white';
      break;
    default:

      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//SNSフォローボタンのclass追加関数
if ( !function_exists( 'get_additional_sns_follow_button_classes' ) ):
function get_additional_sns_follow_button_classes($option = null){
  $classes = null;

  //ボタンカラー
  switch (get_sns_follow_button_color()) {
    case 'brand_color':
      $classes .= ' bc-brand-color';
      break;
    case 'brand_color_white':
      $classes .= ' bc-brand-color-white';
      break;
    default:

      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//内部ブログカードのclass追加関数
if ( !function_exists( 'get_additional_internal_blogcard_classes' ) ):
function get_additional_internal_blogcard_classes($option = null){
  $classes = null;

  //ボタンカラー
  switch (get_internal_blogcard_thumbnail_style()) {
    case 'right':
      $classes .= ' ib-right';
      break;
    default: //left
      $classes .= ' ib-left';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;

//外部ブログカードのclass追加関数
if ( !function_exists( 'get_additional_external_blogcard_classes' ) ):
function get_additional_external_blogcard_classes($option = null){
  $classes = null;

  //ボタンカラー
  switch (get_external_blogcard_thumbnail_style()) {
    case 'right':
      $classes .= ' eb-right';
      break;
    default: //left
      $classes .= ' eb-left';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//ヘッダーのclass追加関数
if ( !function_exists( 'get_additional_header_classes' ) ):
function get_additional_header_classes($option = null){
  $classes = null;

  //ヘッダーを固定にする場合
  if (is_header_background_attachment_fixed()) {
    $classes .= ' hba-fixed';
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//ヘッダーコンテナのclass追加関数
if ( !function_exists( 'get_additional_header_container_classes' ) ):
function get_additional_header_container_classes($option = null){
  $classes = null;

  switch (get_header_layout_type()) {
    case 'top_menu':
      $classes .= ' hlt-top-menu wrap';
      break;
    case 'top_menu_small':
      $classes .= ' hlt-top-menu hlt-tm-small wrap';
      break;
    default://'center_logo'デフォルト
      $classes .= ' hlt-center-logo';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


//フッターボトムのclass追加関数
if ( !function_exists( 'get_additional_footer_bottom_classes' ) ):
function get_additional_footer_bottom_classes($option = null){
  $classes = null;

  switch (get_footer_display_type()) {
    case 'left_and_right':
      $classes .= ' fdt-left-and-right';
      break;
    case 'up_and_down':
      $classes .= ' fdt-up-and-down';
      break;
    default://デフォルト
      $classes .= ' fdt-logo';
      break;
  }

  if ($option) {
    $classes .= ' '.trim($option);
  }
  return $classes;
}
endif;


