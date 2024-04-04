<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body class="body">
    <style>
        hr {
            border-bottom: solid 1px var(--border-color);
            margin: 12px 0;
        }

        .ranking-page-main {
            padding-top: 0;
        }

        .header-img {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            margin: 1rem 0 1rem 0;
        }

        .header-img img {
            display: block;
            width: calc(100% / 4);
            object-fit: cover;
            display: flex;
            border-radius: 50%;
            aspect-ratio: 1;
            padding: 2px;
        }

        .recommend-header {
            text-align: left;
        }

        .recommend-header h2 {
            margin: 1rem 0;
            color: #111;
            font-size: 23px;
        }

        .recommend-desc {
            line-height: normal;
            font-size: 1rem;
            color: #111;
        }

        @media screen and (min-width: 512px) {
            .header-img {
                margin: 2rem 0;
            }

            .recommend-header h2 {
                color: #111;
                font-size: 1.8rem;
            }
        }

        .recommend-desc2 {
            font-size: 13px;
            color: #777;
        }
    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="recommend-header">
                <div class="header-img">
                    <?php foreach (array_slice($openChatList, 0, 8) as $oc) : ?>
                        <img alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
                    <?php endforeach ?>
                </div>
                <h2>【最新】「なりきり」関連のおすすめ人気オープンチャット50件</h2>
                <p class="recommend-desc">
                    「なりきり」に関連するオープンチャットがここに集結！
                </p>
                <p class="recommend-desc2">
                    オプチャグラフ独自の参加人数統計をもとに、勢いのあるLINEオープンチャットの最新リストをお届けします。
                </p>
                <p class="recommend-desc2">
                    オープンチャットには生活の役に立つ・楽しいトークルームが数多くあります。
                    <br>
                    気になるトークルームを見つけたらまずは気軽に参加してみましょう！
                </p>
            </header>
            <hr>
            <?php viewComponent('open_chat_list', compact('openChatList')) ?>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>