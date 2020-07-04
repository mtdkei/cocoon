<?php //ブロックエディタースタイル関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////////
// アイコンボックス
///////////////////////////////////////////
register_block_style(
  'core/paragraph',
  array(
    'name'  => 'information-box',
    'label' => __( '補足情報(i)', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'question-box',
    'label' => __( '補足情報(?)', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'alert-box',
    'label' => __( '補足情報(!)', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'memo-box',
    'label' => __( 'メモ', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'comment-box',
    'label' => __( 'コメント', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'ok-box',
    'label' => __( 'OK', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'ng-box',
    'label' => __( 'NG', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'good-box',
    'label' => __( 'GOOD', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'bad-box',
    'label' => __( 'BAD', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'profile-box',
    'label' => __( 'プロフィール', THEME_NAME ),
  )
);

///////////////////////////////////////////
// 案内ボックス
///////////////////////////////////////////
register_block_style(
  'core/paragraph',
  array(
    'name'  => 'primary-box',
    'label' => __( 'プライマリー（濃い水色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'secondary-box',
    'label' => __( 'セカンダリー（濃い灰色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'info-box',
    'label' => __( 'インフォ（薄い青）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'success-box',
    'label' => __( 'サクセス（薄い緑）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'warning-box',
    'label' => __( 'ワーニング（薄い黄色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'danger-box',
    'label' => __( 'デンジャー（薄い赤色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'light-box',
    'label' => __( 'ライト（白色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'dark-box',
    'label' => __( 'ダーク（暗い灰色）', THEME_NAME ),
  )
);

///////////////////////////////////////////
// 付箋風ボックス
///////////////////////////////////////////
register_block_style(
  'core/paragraph',
  array(
    'name'  => 'sticky-gray',
    'label' => __( '付箋風（灰色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'sticky-yellow',
    'label' => __( '付箋風（黄色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'sticky-red',
    'label' => __( '付箋風（赤色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'sticky-blue',
    'label' => __( '付箋風（青色）', THEME_NAME ),
  )
);

register_block_style(
  'core/paragraph',
  array(
    'name'  => 'sticky-green',
    'label' => __( '付箋風（緑色）', THEME_NAME ),
  )
);

///////////////////////////////////////////
// 案内ボックス
///////////////////////////////////////////
