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

        .recommend-desc2 {
            font-size: 14px;
            color: #555;
        }

        .list-aside {
            all: unset;
            display: block;
            margin-bottom: -6px;
        }

        .list-aside details {
            margin: 0;
            font-size: 11.5px;
            color: #aaa;
            font-weight: normal;
        }

        .list-aside-desc {
            font-size: 13px;
            color: #555;
        }

        .css-162gv95 {
            user-select: none;
            width: 1em;
            height: 1em;
            display: inline-block;
            fill: currentcolor;
            flex-shrink: 0;
            color: rgb(7, 181, 59);
            font-size: 11px;
            margin: -1px -4px;
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
    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="recommend-header">
                <div class="header-img">
                    <?php foreach ($recommend->getPreviewList(8) as $oc) : ?>
                        <img alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
                    <?php endforeach ?>
                </div>
                <h2>「<?php echo $tag ?>」関連のおすすめ人気オープンチャット<?php echo $count ?>選【最新】</h2>
                <p class="recommend-desc">
                    マッチ度が高い「<?php echo $tag ?>」に関するオープンチャットがここに集結！
                </p>
                <p class="recommend-desc2">
                    あなたがお探しのテーマにマッチする厳選LINEオープンチャット最新リストを、直近のメンバー増加が多い順でお届けします。
                </p>
                <p class="recommend-desc2">
                    気になるトークルームを見つけたらまずは気軽に参加してみましょう！
                </p>
            </header>
            <hr>
            <aside class="list-aside">
                <details>
                    <summary>メンバー数のアイコンについて</summary>
                    <div style="margin-top: 4px;">
                        <small class="list-aside-desc">🔥：直近1時間のメンバー数が急上昇</small>
                        <br>
                        <small class="list-aside-desc">🚀：直近24時間のメンバー数が急上昇</small>
                        <br>
                        <small class="list-aside-desc">
                            <span style="margin: 0 4px;">
                                <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                    <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                </svg>
                            </span>：直近1週間のメンバー数が急上昇
                        </small>
                        <br>
                        <small class="list-aside-desc">🏆：リスト内で最もメンバー数が多いトークルーム</small>
                    </div>
                </details>
            </aside>
            <?php viewComponent('open_chat_list_recommend', compact('recommend')) ?>
            <p class="recommend-desc">
                オープンチャットには生活の役に立つ・楽しいトークルームがいっぱい！
            </p>
            <p class="recommend-desc2">
                気になるトークルームを見つけたらまずは気軽に参加してみましょう！
            </p>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>