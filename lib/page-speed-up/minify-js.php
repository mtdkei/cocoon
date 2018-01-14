<?php //JavaScript縮小化用

///////////////////////////////////////
// JSファイルとインラインJSの縮小化
///////////////////////////////////////
if ( !function_exists( 'tag_code_to_minify_js' ) ):
function tag_code_to_minify_js($buffer) {


  if (is_js_minify_enable()) {
    //最終出力縮小化JSコード
    //$last_minfified_js = null;

    //JSファイルパターン
    $js_file_pattern = '<script[^>]+?javascript[^>]+?src=[\'"]([^\'"]+?)[\'"][^>]*?></script>';
    //JSインラインパターン
    $js_inline_pattern = '<script[^>]*?>(.*?)</script>';
    //JS正規表現パターン
    $pattern = '{'.$js_file_pattern.'|'.$js_inline_pattern.'}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m);
    $all = 0;  //scriptタグ全体にマッチ
    $flie = 1; //src内のファイルURLにマッチ
    $code = 2; //scriptタグ内のコードにマッチ
    if ($res && isset($m[$all], $m[$flie], $m[$code])) {
      $i = 0;
      foreach ($m[$all] as $match) {
        //scriptタグ全体
        $script_tag = $match;
        //_v($script_tag);
        //JSファイルURL
        $url = $m[$flie][$i];
        //JSコード
        $js_code = $m[$code][$i];
        //_v($script_link_tag);
        ++$i;


        //ファイルタイプのscriptタグだった場合
        if ($url) {
          //サイトのURLが含まれているものだけ処理
          if (strpos($url, site_url()) !== false) {
            //_v($url);
            //除外処理
            if (
              //jQueryは除外
              //(strpos($url, 'js/jquery/jquery.js') !== false) ||
              //アドミンバーのJSは除外
              (strpos($url, 'js/admin-bar.min.js') !== false)
              || (strpos($url, '/plugins/highlight-js/highlight.min.js') !== false)

              //jQueryマイグレートは除外
              //(strpos($url, 'js/jquery/jquery-migrate.min.js ') !== false)
            ) {
              continue;
            }

            //除外リストにマッチするCSS URLは縮小化しない
            if (is_url_matche_list($url, get_js_minify_exclude_list())) {
              continue;
            }

            //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
            $url = preg_replace('/\?.*$/m', '', $url);
            //_v($url);//JSコード変換するURL

            //JS URLからJSコードの取得
            $js = js_url_to_js_minify_code( $url );
            //縮小化可能ななJSだと時
            if ($js !== false) {
              //_v($js);//変換したJSコード

              //JSを縮小化したJSファイルURL linkタグをインラインにする
              $buffer = str_replace($script_tag, '<script type="text/javascript">'.$js.'</script>', $buffer);


              //$last_minfified_js .= $js;
            }//$js

          }//strpos($url, site_url()) !== false
        }//url


        //インラインタイプのJavaScriptコードだった場合
        if ($js_code) {
          //_v($js_code);
          $js = minify_js($js_code);
          //インラインタイプのscriptタグを縮小化して置換する
          $buffer = str_replace($script_tag, '<script type="text/javascript">'.$js.'</script>', $buffer);
        }

      }//foreach
    }//$res && isset($m[1])
  }//is_js_minify_enable()


  /*
  if (is_js_minify_enable()) {
    //最終出力縮小化JSコード
    $last_minfified_js = null;

    //JSファイル
    $pattern = '{<script.+?javascript.+?src=[\'"]([^\'"]+?)[\'"].*?>.*?</script>}i';//[^>]*?
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m);
    if ($res && isset($m[1])) {
      $i = 0;
      foreach ($m[1] as $match) {
        //JSファイルURL
        $url = $match;
        //JSファイル読み込みタグ（<script type='text/javascript' src='http://xxx/jquery-migrate.min.js?ver=1.4.1'></script>）
        $script_link_tag = $m[0][$i];
        //_v($script_link_tag);
        ++$i;

        //サイトのURLが含まれているものだけ処理
        if (strpos($url, site_url()) !== false) {
          //除外処理
          if (
            //jQueryは除外
            (strpos($url, 'js/jquery/jquery.js') !== false) ||
            (strpos($url, 'plugins/highlight-js/highlight.min.js') !== false) ||
            (strpos($url, 'plugins/baguettebox/dist/baguetteBox.min.js') !== false) ||
            (strpos($url, 'plugins/stickyfill/dist/stickyfill.min.js') !== false) ||
            (strpos($url, 'plugins/slick/slick.min.js') !== false) ||
            (strpos($url, 'js/jquery/jquery.js') !== false) ||
            //アドミンバーのJSは除外
            (strpos($url, 'js/admin-bar.min.js') !== false) //||
            //jQueryマイグレートは除外
            //(strpos($url, 'js/jquery/jquery-migrate.min.js ') !== false)
          ) {
            continue;
          }

          //?var=4.9のようなURLクエリを除去(remove_query_arg( 'ver', $url ))
          $url = preg_replace('/\?.*$/m', '', $url);
          _v($url);//JSコード変換するURL

          //JS URLからJSコードの取得
          $js = js_url_to_js_minify_code( $url );
          //縮小化可能ななJSだと時
          if ($js) {
            //_v($js);//変換したJSコード

            //JSを縮小化したJSファイルURL linkタグを削除する
            $buffer = str_replace($script_link_tag, '', $buffer);


            $last_minfified_js .= $js;
          }//$js

        }//strpos($url, site_url()) !== false

      }//foreach
    }//$res && isset($m[1])


    //JSインラインスタイル
    $pattern = '{<script[^>]*?>(.*?)</script>}is';
    $subject = $buffer;
    $res = preg_match_all($pattern, $subject, $m);
    //_v($m);
    if ($res && isset($m[1])) {
      $i = 0;
      foreach ($m[1] as $match) {
        //jsコード
        $js = $match;
        //_v($js);
        //JSタグ（<script type="text/javascript"></script>）
        $script_tag = $m[0][$i];
        //_v($script_tag);
        ++$i;
        //_v($match);
        if (empty($js)) {
          continue;
        }

        //最終出力縮小化JSコードに縮小化したJSコードを加える
        $last_minfified_js .= minify_css($js);
        //ヘッダー出力コードからscriptタグを削除
        $buffer = str_replace($script_tag, '', $buffer);
      }
    }

    //縮小化したJavaScriptをデータの最後に付け加える
    $buffer = $buffer.PHP_EOL.'<script type="text/javascript">'.$last_minfified_js.'</script>';
  }//is_js_minify_enable()
  */
  //_v($buffer);
  return $buffer;
}
endif;


//JavaScript URLからコードを取り出して縮小化コードを返す
if ( !function_exists( 'js_url_to_js_minify_code' ) ):
function js_url_to_js_minify_code( $url ) {
  $js = false;
  //URLファイルをローカルファイルパスに変更
  $local_file = url_to_local($url);
  //_v($local_file);

  if ( WP_Filesystem() && file_exists($local_file) ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    $js = $wp_filesystem->get_contents($local_file);

    //コメントの除去
    $js = remove_code_comments($js);
    // $js = preg_replace('{/\*.+?\*/}is', '', $js);
    // $js = preg_replace('{^\s+//.+$}im', '', $js);
    //CSS内容を縮小化して書式を統一化する
    $js = minify_js($js);
    //コード内scriptタグの処理
    $js = str_replace('"<script>"', '"</script"+">"', $js);
    $js = str_replace("'<script>'", "'</script'+'>'", $js);
    $js = str_replace('"</script>"', '"</script"+">"', $js);
    $js = str_replace("'</script>'", "'</script'+'>'", $js);
    $js = preg_replace('{"<([^>]+?)>"}i', '"<$1"+">"', $js);
    $js = preg_replace("{'<([^>]+?)>'}i", "'<$1'+'>'", $js);


  }//WP_Filesystem
  return $js;
}
endif;

if ( !function_exists( 'remove_code_comments' ) ):
function remove_code_comments($code){
  $code = preg_replace('{/\*.+?\*/}is', '', $code);
  $code = preg_replace('{^\s+//.+$}im', '', $code);
  return $code;
}
endif;

/*async defer*/
//add_filter( 'script_loader_tag', 'add_defer_async_scripts', 10, 3 );
if ( !function_exists( 'add_defer_async_scripts' ) ):
function add_defer_async_scripts( $tag, $handle, $src ) {
  $async_defer = array(
    'jquery',
    'jquery-migrate',
    'jquery-core',
  );
  $async_scripts = array(
    //'crayon_js',
  );
  $defer_scripts = array(
    //'code-highlight-js',
  );

  if ( in_array( $handle, $async_defer ) ) {
      return '<script type="text/javascript" src="' . $src . '" async defer></script>' . PHP_EOL;
  }
  if ( in_array( $handle, $async_scripts ) ) {
      return '<script type="text/javascript" src="' . $src . '" async></script>' . PHP_EOL;
  }
  if ( in_array( $handle, $defer_scripts ) ) {
      return '<script type="text/javascript" src="' . $src . '" defer></script>' . PHP_EOL;
  }

  return $tag;
}
endif;
