<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta', '_schema', 'canonical')) ?>

<body class="body">
    <style>

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
                <h2>「<?php echo $tag ?>」関連のおすすめ人気オプチャ<?php echo $count ?>選【最新】</h2>
                <time datetime="<?php echo $_updatedAt->format(\DateTime::ATOM) ?>"><span aria-hidden="true" style="user-select: none;">🕛 </span><?php echo $_updatedAt->format('Y年m月d日 H:i') ?></time>
                <p class="recommend-desc">
                    LINEオープンチャットにて特に人気のルームから、「<?php echo $tag ?>」にマッチするルームをご紹介！
                </p>
                <p class="recommend-desc2">
                    気になるルームを見つけたら気軽に参加してみましょう！
                </p>
            </header>
            <aside class="list-aside">
                <h3 class="list-title">
                    <span>関連性の高いタグ</span>
                    <!-- <button class="tag-btn close-btn"></button> -->
                </h3>
                <section class="tag-list-section">
                    <ul class="tag-list">
                        <?php foreach (array_slice($tags, 0, 10) as $key => $word) : ?>
                            <li>
                                <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode($word)) ?>">
                                    <?php echo $word ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </section>
            </aside>
            <section style="all:unset; display:block;">
                <h3 class="list-title oc-list">「<?php echo $tag ?>」関連のルーム <?php echo $count ?>件</h3>
                <aside class="list-aside">
                    <details>
                        <summary>メンバー数のアイコンについて</summary>
                        <div class="list-aside-details">
                            <small class="list-aside-desc">🔥：直近1時間のメンバー数が急上昇</small>
                            <small class="list-aside-desc">🚀：直近24時間のメンバー数が急上昇</small>
                            <small class="list-aside-desc">
                                <span style="margin: 0 4px;">
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                        <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                    </svg>
                                </span>：直近1週間のメンバー数が急上昇
                            </small>
                            <small class="list-aside-desc">🏆：リスト内で最もメンバー数が多いトークルーム</small>
                        </div>
                    </details>
                </aside>
                <?php viewComponent('open_chat_list_recommend', compact('recommend')) ?>
            </section>
            <hr>
            <aside style="all: unset; display:block; margin: 20px 0 8px 0;">
                <p class="recommend-desc">
                    オープンチャットには生活の役に立つ・楽しいルームがいっぱい！
                </p>
                <p class="recommend-desc2">
                    気になるルームを見つけたら気軽に参加してみましょう！
                </p>
            </aside>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>