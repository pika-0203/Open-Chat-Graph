<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;

/** @var array{ hour:?int,hour24:?int,week:?int } $diffMember */
viewComponent('head', compact('_css', '_schema', 'canonical') + ['_meta' => $_meta->generateTags(true)]) ?>

<body class="body">
    <style>

    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="recommend-header">
                <div class="hearder-img-outer">
                    <div class="header-img">
                        <?php if (isset($recommend)) : ?>
                            <?php foreach ($recommend->getPreviewList(8) as $oc) : ?>
                                <img alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>
                    <div class="header-img-title">
                        <?php if ($count) : ?>
                            <h2>【<?php echo $tag ?>】オープンチャット人気ランキングTOP<?php echo $count ?>【毎時更新】</h2>
                        <?php else : ?>
                            <h2>【<?php echo $tag ?>】オープンチャット人気ランキング【毎時更新】</h2>
                        <?php endif ?>
                    </div>
                </div>
            </header>
            <time datetime="<?php echo $_updatedAt->format(\DateTime::ATOM) ?>"><span aria-hidden="true" style="user-select: none;">🕛 </span><?php echo $_updatedAt->format('Y年m月d日 H:i') ?></time>
            <nav class="share-nav unset">
                <div class="share-nav-inner">
                    <div class="share-menu-outer">
                        <?php $url = urlencode(url(path())) ?>
                        <a class="share-menu-item unset" href="https://twitter.com/intent/tweet?url=<?php echo $url ?>&text=<?php echo urlencode($_meta->title) ?>" rel="nofollow noopener" target="_blank" title="Twitterでシェア">
                            <span class="share-menu-icon-twitter share-menu-icon"></span>
                        </a>
                        <a class="share-menu-item unset" href="https://b.hatena.ne.jp/entry/s/<?php echo getHostAndUri() ?>" rel="nofollow noopener" target="_blank" title="はてなブックマークでブックマーク">
                            <span class="share-menu-icon-hatena share-menu-icon"></span>
                        </a>
                        <a class="share-menu-item unset" href="https://social-plugins.line.me/lineit/share?url=<?php echo $url ?>" rel="nofollow noopener" target="_blank" title="LINEでシェア">
                            <span class="share-menu-icon-line share-menu-icon"></span>
                        </a>
                        <a class="share-menu-item unset" href="https://www.facebook.com/share.php?u=<?php echo $url ?>" rel="nofollow noopener" target="_blank" title="Facebookでシェア">
                            <span class="share-menu-icon-facebook share-menu-icon"></span>
                        </a>
                        <div class="copy-btn-outer" id="copy-btn-outer">
                            <button class="share-menu-item unset" id="copy-btn" title="このページのタイトルとURLをコピーする">
                                <span class="copy-btn-icon link-icon"></span>
                            </button>
                            <div class="description1" id="copy-description">
                                <div class="copy-btn-inner">
                                    <span class="copy-btn-icon copy-icon"></span>
                                    <span>コピーしました</span>
                                </div>
                                <hr style="margin: 0.25rem 0 0.45rem 0;">
                                <div class="copy-btn-text" id="copy-btn-title"></div>
                                <div class="copy-btn-text" id="copy-btn-url"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="recommend-p">
                <p class="recommend-desc">
                    2019年のサービス開始以来、累計2200万人以上のユーザーに利用されているLINEオープンチャット。<?php echo $tag ?>に関するルームは多くのユーザーによって開設されています。
                </p>
                <p class="recommend-desc">
                    そこでオプチャグラフでは「<?php echo \App\Services\Recommend\RecommendUtility::extractTag($tag) ?>に関するテーマで人数増加が多いルーム」のランキングを作成しました。1時間ごとの更新で新しいルームが随時追加されます。
                </p>
            </div>
            <?php if (isset($tags) && $tags) : ?>
                <aside class="list-aside">
                    <h3 class="list-title">
                        <span>関連のキーワード</span>
                    </h3>
                    <section class="tag-list-section">
                        <p class="recommend-desc">
                            <span class="small">各ルームは特定のランキングにのみ表示されます。例えば、ポケモンGOのルームは「ポケモンGO」のランキングに表示され、「ポケモン」のランキングには表示されません。探しているルームが見つからない場合は関連のキーワードをチェック！</span>
                        </p>
                        <ul class="tag-list">
                            <?php foreach (array_slice($tags, 0, 12) as $key => $word) : ?>
                                <li>
                                    <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                        <?php echo \App\Services\Recommend\RecommendUtility::extractTag($word) ?>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </section>
                </aside>
            <?php endif ?>
            <section style="all:unset; display:block;">
                <?php if ($count) : ?>
                    <h2 class="list-title oc-list">「<?php echo $tag ?>」のランキング <?php echo $count ?>件</h2>
                    <aside class="list-aside">
                        <details class="icon-desc">
                            <summary>メンバー数のアイコンについて</summary>
                            <div class="list-aside-details">
                                <small class="list-aside-desc">🔥：過去1時間で<?php echo AppConfig::MIN_MEMBER_DIFF_HOUR ?>人以上増加<?php if (count($recommend->hour) >= AppConfig::RECOMMEND_LIST_LIMIT) : ?> (<?php echo AppConfig::RECOMMEND_LIST_LIMIT ?>件まで)<?php endif ?></small>
                                <small class="list-aside-desc">🚀：過去24時間で<?php echo AppConfig::MIN_MEMBER_DIFF_H24 ?>人以上増加<?php if (count($recommend->day) >= AppConfig::RECOMMEND_LIST_LIMIT) : ?> (<?php echo AppConfig::RECOMMEND_LIST_LIMIT ?>件まで)<?php endif ?></small>
                                <small class="list-aside-desc">
                                    <span style="margin: 0 4px;">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                            <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                        </svg>
                                    </span>：過去1週間で<?php echo AppConfig::MIN_MEMBER_DIFF_WEEK ?>人以上増加<?php if (count($recommend->week) >= AppConfig::RECOMMEND_LIST_LIMIT) : ?> (上位<?php echo AppConfig::RECOMMEND_LIST_LIMIT ?>件まで)<?php endif ?>
                                </small>
                                <small class="list-aside-desc">🏆：リスト内で最も人数が多いトークルーム</small>
                            </div>
                        </details>
                    </aside>
                <?php else : ?>
                    <h2 class="list-title oc-list">只今サーバー内でリスト更新中です…</h2>
                <?php endif ?>
                <?php if (isset($diffMember)) : ?>
                    <aside class="list-aside">
                        <span>全体の増減</span>
                        <section class="diff-member">
                            <div>
                                <span>1時間</span>
                                <span><?php echo signedNumF($diffMember['hour']) ?>人</span>
                            </div>
                            <div>
                                <span>24時間</span>
                                <span><?php echo signedNumF($diffMember['hour24']) ?>人</span>
                            </div>
                            <div>
                                <span>1週間</span>
                                <span><?php echo signedNumF($diffMember['week']) ?>人</span>
                            </div>
                        </section>
                    </aside>
                <?php endif ?>
                <?php if (isset($recommend)) : ?>
                    <?php viewComponent('open_chat_list_recommend', compact('recommend')) ?>
                <?php endif ?>
            </section>
            <?php if (isset($tags) && $tags) : ?>
                <aside class="list-aside">
                    <h3 class="list-title">
                        <span>関連のキーワード</span>
                    </h3>
                    <section class="tag-list-section" style="margin-bottom: 1rem;">
                        <ul class="tag-list">
                            <?php foreach (array_slice($tags, 0, 12) as $key => $word) : ?>
                                <li>
                                    <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                        <?php echo \App\Services\Recommend\RecommendUtility::extractTag($word) ?>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </section>
                </aside>
            <?php endif ?>
            <aside style="all: unset; display:block; margin: 20px 0 0 0;">
                <p class="recommend-desc2">
                    オープンチャットはLINEに登録している名前とプロフィールが同期されず、高い匿名性で安全に利用できるのが特徴です。
                    気になるルームを見つけたら気軽に参加してみましょう！
                </p>
                <div class="app_link">
                    <a href="https://openchat-jp.line.me/other/beginners_guide">
                        <span class="text">はじめてのLINEオープンチャットガイド（LINE公式）</span>
                    </a>
                </div>
            </aside>
        </article>
    </main>
    <footer style="border-top: 1px solid #efefef; padding-top: 1rem;">
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>