<?php //barba.js処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp()): ?>
  <?php if (is_highspeed_mode_enable()): ?>
  <script>
  (function($){


    //barba.js遷移を無効化する
    function barbaPrevent() {
      //管理パネルはbarba.js動作から除外する
      let wpadminbar = document.getElementById("wpadminbar");
      if (wpadminbar) {
        wpadminbar.setAttribute("data-barba-prevent", "all");
      }
    }
    barbaPrevent();


    //barba.js
    //barba.use(barbaPrefetch);

    //head内タグのの移し替え
    function replaceHeadTags(target) {
      let head = document.head;
      let targetHead = target.html.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0];
      //console.log(targetHead);
      let newPageHead = document.createElement('head');
      newPageHead.innerHTML = targetHead;
      // console.log(newPageHead);
      //SEOに関係ありそうなタグ
      let removeHeadTags = [
        "style",
        "meta[name='keywords']",
        "meta[name='description']",
        "meta[property^='fb']",
        "meta[property^='og']",
        "meta[property^='article']",
        "meta[name^='twitter']",
        "meta[property^='twitter']",
        "meta[name='robots']",
        'meta[itemprop]',
        "meta[name='thumbnail']",
        'link[itemprop]',
        "link[rel='alternate']",
        "link[rel='prev']",
        "link[rel='next']",
        "link[rel='canonical']",
        "link[rel='amphtml']",
        "link[rel='shortlink']",
        "script",
        // "script[type='application/ld+json']",
      ].join(',');
      //前のページの古いタグを削除
      let headTags = [...head.querySelectorAll(removeHeadTags)];
      //console.log(headTags)
      headTags.forEach(item => {
        head.removeChild(item);
      });
      //新しいページの新しいタグを追加
      let newHeadTags = [...newPageHead.querySelectorAll(removeHeadTags)];
      //console.log(newHeadTags)
      newHeadTags.forEach(item => {
        head.appendChild(item);
      });
    }

    //アンカーリンクを考慮したスクロール
    //参考：https://leap-in.com/ja/notes-when-you-use-barba-js-2/
    function pageScroll(){
      let headerFixed = <?php echo is_header_fixed() ? 'true' : 'false'; ?>;
      // check if 「#」 exists
      if(location.hash){
        let anchor = document.querySelector( location.hash );
        if(anchor){
          let rect = anchor.getBoundingClientRect();
          //console.log(rect);
          let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
          //let scrollElem = document.scrollingElement || document.documentElement;

          console.log(scrollTop);
          let top = rect.top + scrollTop;
          // let top = $(location.hash).offset().top;
          console.log($(location.hash).offset().top);
          // if(headerFixed){
          //   let header = document.getElementById('header-container');
          //   if(header){
          //     top = top - header.clientHeight;
          //   }
          // }
          //scrollElem.scrollTop = top;
          window.scrollTo(0, top);
        }else{
          // no anchor, go to top position
          window.scrollTo(0, 0);
        }
      }else{
        // no anchor, go to top position
        window.scrollTo(0, 0);
      }
    }

    function footerTagsLoad(target) {
      let footerHtml = target.html.match(/<div id="go-to-top" class="go-to-top">([\s\S.]*)$/i)[0];
      //console.log(footerHtml);
      let footerScripts = footerHtml.match(/<script[^>]*>([\s\S.]*?)<\/script>/ig);
      //console.log(footerScripts);
      //$('script').delete();
      footerScripts.forEach(script => {
        if (!script.match(/barba/)) {
          //console.log(script);
          //$(script).delete();
          //script属性にsrcがある場合
          let res = script.match(/ src="(.+?)"/);
          let scriptTag = document.createElement("script");

          if (res) {
            //console.log(res[1]);
            scriptTag.async = true;
            // scriptTag.defer = true;
            scriptTag.src = res[1];
          } else {
            //script内にコードがある場合
            let code = script.match(/<script[^>]*>([\s\S.]+?)<\/script>/i);
            //console.log(script);
            if (code) {
              //console.log(code[1]);
              // scriptTag.async = true;
              scriptTag.innerHTML = code[1];
            }
          }
          document.getElementById("container").appendChild(scriptTag);
          // let url = script.match(/ src="(.+?)"/)[1];
          // console.log(url);
          // if (url) {

          // } else {

          // }
          // console.log($(script));
          // $('#container').append(script);
          //document.getElementById("container").appendChild($(script));
        }
        //
      });
      //console.log(footerScripts);
    }
    <?php
    $analytics_tracking_id = get_google_analytics_tracking_id();
    if ($analytics_tracking_id && is_analytics()): ?>
    //Google Analytics
    function gaPush(pagename) {
      //古いAnalyticsコード
      if (typeof ga === 'function') {
        ga('send', 'pageview', pagename);
      }
      //gtag.js（公式）
      if (typeof gtag === 'function') {
        //console.log('gtag');
        gtag('config', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
      }
      //ga-lite.min.js（高速化）
      if (typeof galite === 'function') {
        galite('create', '<?php echo $analytics_tracking_id; ?>', {'page_path': pagename});
        galite('send', 'pageview');
      }
    }
    <?php endif; ?>

    //Twitterスクリプトの呼び出し
    function tweetLoad() {
      let tweet = document.getElementsByClassName('twitter-tweet');
      if (tweet) {
        if (typeof twttr === 'undefined') {
          let twitterjs = document.createElement("script");
          twitterjs.async = true;
          twitterjs.src = '//platform.twitter.com/widgets.js';
          document.getElementById("container").appendChild(twitterjs);
        } else {
          twttr.widgets.load();
        }
      }
    }
    //Instagramスクリプトの呼び出し
    function instagramLoad() {
      let im = document.getElementsByClassName('instagram-media');
      if (im) {
        if (typeof window.instgrm === 'undefined') {
          let instagramjs = document.createElement("script");
          instagramjs.async = true;
          instagramjs.src = '//www.instagram.com/embed.js';
          document.getElementById("container").appendChild(instagramjs);
        } else {
          window.instgrm.Embeds.process();
        }
      }
    }

    //barba.jsの実行
    barba.init({
      prevent: function (e) {
        //console.log(e);
        return false;
      },
      transitions: [
        {
          before({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_before'); ?>
          },
          beforeLeave({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_before_leave'); ?>
          },
          leave({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_leave'); ?>
          },
          afterLeave({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_after_leave'); ?>
          },
          beforeEnter({ current, next, trigger }) {
            //console.log('beforeEnter');
            //console.log(next);

            //headタグ変換
            replaceHeadTags(next);

            // //ページトップに移動
            // const scrollElem = document.scrollingElement || document.documentElement;
            // scrollElem.scrollTop = 0;
            //コメントエリアを開く動作の登録
            //register_comment_area_open();

            // // 外部ファイルの実行(任意の場所に追加)
            // let script = document.createElement('script');
            // script.src = 'https://cocoon.local/plugins/slick/slick.min.js';
            // document.body.appendChild(script);

            //    // 外部ファイルの実行(任意の場所に追加)
            // let script = document.createElement('script');
            // script.innerHTML = '$(".carousel-content").slick();';
            // document.body.appendChild(script);

            //URLのアンカー（?以降の部分）を取得、加工してアンカーに移動する
            //let urlSearch = location.search;
            //urlSearch = getGET(); //「?」を除去
            // console.log(current);

            // const scrollElem = document.scrollingElement || document.documentElement;
            // //console.log(tgt);
            // let hash = location.hash;
            // // console.log(hash);
            // // console.log(current);
            // //ハッシュ値がある場合
            // if (hash) {
            //   // let anchor = document.getElementById(hash);
            //   // console.log(anchor);
            //   const target = $(hash).offset().top; //アンカーの位置情報を取得
            //   //console.log(target);
            //   scrollElem.scrollTop = Math.floor(target);
            // } else {
            //   scrollElem.scrollTop = 0;
            // }

            //window.history.pushState(null, null, pagelinkHref);

            // // ブラウザがpushStateに対応しているかチェック
            // if (window.history && window.history.pushState){
            //   $(window).on("popstate",function(event){
            //     console.log(event);
            //     // if (!event.originalEvent.state) return; // 初回アクセス時対策
            //     // let state = event.originalEvent.state; // stateオブジェクト
            //   });
            // }

            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_before_enter'); ?>
          },
          enter({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_enter'); ?>
          },
          afterEnter({ current, next, trigger }) {
            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_after_enter'); ?>
          },
          after({ current, next, trigger }) {
            //console.log(current);
            <?php if ($analytics_tracking_id && is_analytics()): ?>
              //Google Analytics
              gaPush(location.pathname);
            <?php endif; ?>
            //ツイート埋め込み
            tweetLoad();
            //instagram埋め込み
            instagramLoad();
            //footerTagsLoad(current);

            <?php //フッタースクリプトの読み込み
            //wp_footer()コードの再読み込み
            global $_WP_FOOTER;
            generate_baruba_js_scripts($_WP_FOOTER);
            //テンプレートのスクリプトも再読み込み
            ob_start();
            get_template_part('tmp/footer-scripts');
            $scripts = ob_get_clean();
            generate_baruba_js_scripts($scripts);
            ?>

            //アンカーリンク対応
            pageScroll();
            ?>

            // $(".entry-content pre").each(function(i,block){hljs.highlightBlock(block)});

            // $(".carousel-content").slick({
            //   dots: true,
            //   infinite: true,
            //   slidesToShow: 6,
            //   slidesToScroll: 6,
            //   responsive: [
            //       {
            //         breakpoint: 1240,
            //         settings: {
            //           slidesToShow: 5,
            //           slidesToScroll: 5
            //         }
            //       },
            //       {
            //         breakpoint: 1023,
            //         settings: {
            //           slidesToShow: 4,
            //           slidesToScroll: 4
            //         }
            //       },
            //       {
            //         breakpoint: 834,
            //         settings: {
            //           slidesToShow: 3,
            //           slidesToScroll: 3
            //         }
            //       },
            //       {
            //         breakpoint: 480,
            //         settings: {
            //           slidesToShow: 2,
            //           slidesToScroll: 2
            //         }
            //       }
            //     ]
            // });

            barbaPrevent();

            <?php //一応PHPからもスクリプトを挿入できるようにフック
            do_action('barba_init_transitions_after'); ?>
          }
        }
      ]
    });

    // const eventDelete = e => {
    //   if (e.currentTarget.href === window.location.href) {
    //     // console.log(e.currentTarget.href);
    //     // console.log(window.location.href);
    //     e.preventDefault();
    //     e.stopPropagation();
    //     return;
    //   }
    // };

    // const links = [...document.querySelectorAll('a[href]')];
    // //console.log(links);
    // links.forEach(link => {
    //   link.addEventListener('click', e => {
    //     //console.log('click');
    //     eventDelete(e);
    //   }, false);
    // });
  })($);
  </script>
  <?php endif; ?>
<?php endif; ?>
