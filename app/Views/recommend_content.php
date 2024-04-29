<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;

/** @var array{ hour:?int,hour24:?int,week:?int } $diffMember */
viewComponent('head', compact('_css', '_schema', 'canonical') + ['_meta' => $_meta->generateTags(true)]) ?>

<body class="body">
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header', compact('_updatedAt')) ?>
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
            <section class="unset close" style="display: block; position:relative;" id="recommed-desc-box">
                <p class="recommend-desc recommend-p" id="recommed-desc">
                    <span class="recommend-desc-child">2019年のサービス開始以来、累計2200万人以上のユーザーに利用されているLINEオープンチャットでは、「<?php echo $tag ?>」をテーマにしたルームが数多く開設されています。</span>
                    <br>
                    <br>
                    <span class="recommend-desc-child">そこで、オプチャグラフでは、「<?php echo \App\Services\Recommend\RecommendUtility::extractTag($tag) ?>」をテーマにした中で、最近人数が急増しているルームのランキングを作成しました。このランキングは1時間ごとに更新され、新しいルームが継続的に追加されます。</span>
                    <br>
                    <br>
                    <span class="recommend-desc-child">オープンチャットの情報を掲載する仕組みにつきましては、<a href="<?php echo url('policy') ?>">オプチャグラフについて</a>をご覧ください。</span>
                </p>
                <button id="recommed-desc-close-btn" class="close-btn" title="一部を表示">一部を表示</button>
                <div class="more">
                    <div class="more-separater">&nbsp;</div>
                    <span class="unset more-text" id="read_more_btn" title="もっと見る">…もっと見る</span>
                </div>
            </section>
            <?php if (isset($tags) && $tags) : ?>
                <aside class="list-aside">
                    <h3 class="list-title">
                        <span>関連のタグ</span>
                    </h3>
                    <?php viewComponent('recommend_tag_desc') ?>
                    <section class="tag-list-section">
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
                        <span>関連のタグ</span>
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
                    オープンチャットは、LINEに登録している名前やプロフィールとは同期されないため、匿名性が高く安全に利用できることが特徴です。気になるルームを見つけたら、気軽に参加してみましょう！
                </p>
                <div class="app_link">
                    <a href="https://openchat-jp.line.me/other/beginners_guide">
                        <span class="text">はじめてのLINEオープンチャットガイド（LINE公式）</span>
                    </a>
                </div>
            </aside>
        </article>
    </main>
    <footer style="padding-top: 1rem;">
        <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
    <script>
        // 説明文の続きを読むボタン
        const talkroomDesc = document.getElementById('recommed-desc')
        const talkroomDescBox = document.getElementById('recommed-desc-box')
        const closeId = 'recommed-desc-close-btn'
        const closeBtn = document.getElementById('recommed-desc-close-btn')

        talkroomDescBox.addEventListener('click', (e) => e.target.id !== closeId && talkroomDescBox.classList.remove('close'))

        closeBtn.addEventListener('click', () => {
            talkroomDescBox.classList.add('close')
            window.scrollTo({
                top: 0,
            });
        })
    </script>
</body>

</html>