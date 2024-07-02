<?php

use App\Services\Accreditation\Enum\ExamType;
use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);

/**
 * @var \App\Models\Accreditation\AccreditationHomePageModel $model
 */
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <style>
        .graph-ifame {
            width: 100%;
            max-width: 512px;
            aspect-ratio: 2/1;
            overflow: hidden;
        }

        .graph-ifame iframe {
            border: 0;
            width: 100%;
            height: 200vh
        }

        .share-menu-item {
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
        }

        .share-menu-icon {
            width: 32px;
            height: 32px;
            margin: auto;
            background-repeat: no-repeat;
            background-size: contain;
            display: block;
        }

        .share-menu-icon.copy {
            width: 26px;
            height: 26px;
            margin: auto;
        }

        .share-menu-icon-twitter {
            background-image: url(/assets/twitter_x.svg);
            background-color: #000000;
            border-radius: 6px;
        }

        .share-menu-icon-line {
            background-image: url(/assets/line.svg);
            border-radius: 6px;
        }

        .copy-btn-icon {
            background-image: url(/assets/copy_icon_c.svg);
        }

        .list-wrapper {
            margin: 24px 0;
            display: block;
            text-decoration: none;
        }

        .question-link {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            word-break: break-all;
            font-size: 16px;
            line-height: 1.5;
            letter-spacing: -0.3px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial,
                sans-serif;
            text-decoration: unset;
            font-weight: bold;
            width: fit-content;
            max-width: 712px;
            margin-top: 6px;
        }

        .exam-title-chip {
            margin-right: 6px;
            display: inline-block;
            padding: 2px 7px;
            border-radius: 2rem;
            font-size: 11px;
            line-height: 1.3;
            color: #fff;
            font-weight: bold;
        }

        .gold-chip {
            background: linear-gradient(45deg, #B67B03 0%, #DAAF08 45%, #FEE9A0 70%, #DAAF08 85%, #B67B03 90% 100%);
        }

        .silver-chip {
            background: linear-gradient(45deg, #757575 0%, #9E9E9E 45%, #E8E8E8 70%, #9E9E9E 85%, #757575 90% 100%);
        }

        .bronze-chip {
            background: linear-gradient(45deg, #cd7f32 0%, #d88b42 30%, #e6b377 50%, #f0c490 70%, #e6b377 85%, #cd7f32 100%);
        }

        @media screen and (min-width: 512px) {
            .list-wrapper:hover .question-link {
                text-decoration: underline 2px;
            }
        }
    </style>
    <main>
        <?php $view->mainTab(false) ?>
        <hr>
        <div>
            <h2>最近の投稿</h2>
            <?php $list = $model->getQuestionList(5); ?>
            <?php foreach ($list as $q) : ?>
                <?php $type = ExamType::from($q->type) ?>
                <a class="list-wrapper" href="./../<?php echo $q->type . "/" . ($q->publishing ? 'published' : 'unpublished') ?>#id-<?php echo $q->id ?>">
                    <span class="exam-title-chip <?php echo $q->type ?>-chip"><?php echo $view->getExamTypeName($type) ?></span>
                    <small style="margin-right: 4px; color: #111;"><?php echo $q->user_name ?></small>
                    <small style="margin-right: 4px; color: #777;"><?php echo timeElapsedString($q->created_at) ?></small>
                    <small style="margin-right: 4px; color: <?php echo $q->publishing ? '#aaa' : '#4d73ff' ?>;"><?php echo $q->publishing ? '出題中' : '未公開' ?></small>
                    <span class="question-link"><?php echo $q->question ?></span>
                </a>
            <?php endforeach ?>
        </div>
        <hr>
        <div>
            <h2>投稿された問題</h2>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>問題数</th>
                        <th>出題中</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counts = $model->getQuestionCount(); ?>
                    <?php foreach (ExamType::cases() as $type) : ?>
                        <tr>
                            <td style="color: <?php echo $view->getTypeColor($type) ?>; font-weight: bold;">
                                <?php echo $view->getExamTypeName($type) ?>
                            </td>
                            <td>
                                <span style="font-weight: bold;"><?php echo $counts['total_count_' . $type->value] ?> 件</span>
                            </td>
                            <td>
                                <span><?php echo $counts['publishing_count_' . $type->value] ?> 件</span>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    <tr>
                        <td style="margin-top: 12px;">
                            全体
                        </td>
                        <td>
                            <span style="font-weight: bold;"><?php echo $counts['total_count_bronze'] + $counts['total_count_silver'] + $counts['total_count_gold'] ?> 件</span>
                        </td>
                        <td>
                            <span><?php echo $counts['publishing_count_bronze'] + $counts['publishing_count_silver'] + $counts['publishing_count_gold'] ?> 件</span>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <hr>
        <a href="/accreditation" target="_blank" style="font-size: 16px; font-weight: bold; color: rgb(29, 155, 240);">オプチャ検定｜練習問題</a>
        <div style="display: flex; gap: 24px; margin-top: 1rem; flex-wrap: wrap; align-items: center;">
            <small style="word-break: keep-all; text-wrap: nowrap; margin-right: -10px">シェア</small>

            <div class="share-menu-item unset" onclick="copyUrl('LINEオープンチャットを快適に利用できる知識を楽しく学ぼう！\nオプチャ検定｜練習問題\nhttps://openchat-review.me/accreditation')">
                <span class="copy-btn-icon share-menu-icon copy"></span>
            </div>
            <a class="share-menu-item unset" href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://openchat-review.me/accreditation') ?>&text=<?php echo urlencode("LINEオープンチャットを快適に利用できる知識を楽しく学ぼう！\nオプチャ検定｜練習問題\n") ?>" rel="nofollow noopener" target="_blank" title="ポスト">
                <span class="share-menu-icon-twitter share-menu-icon"></span>
            </a>

            <a class="share-menu-item unset" href="http://line.me/R/msg/text/?<?php echo urlencode("LINEオープンチャットを快適に利用できる知識を楽しく学ぼう！\nオプチャ検定｜練習問題\nhttps://openchat-review.me/accreditation") ?>" rel="nofollow noopener" target="_blank" title="LINEで送る">
                <span class="share-menu-icon-line share-menu-icon"></span>
            </a>
        </div>
        <br>

        <details>
            <summary style="width: fit-content; font-size: 13px;">練習問題のページ訪問回数を見る</summary>
            <br>
            <div class="graph-ifame">
                <iframe loading=lazy src="https://lookerstudio.google.com/embed/reporting/12373f52-b8f2-42d1-9935-d2ffd6bebfa2/page/usU4D" frameborder="0" allowfullscreen sandbox="allow-storage-access-by-user-activation allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe>
            </div>
            <br>
            <small>データの反映にラグがあるため、最新日の数字は最大2日後ぐらいに確定します</small>
        </details>

        <?php if ($view->controller->profileArray) : ?>
            <hr>
            <small style="font-size: 13.3px;">
                <span style="margin-right: 4px;">プロフィール</span>
                <a href="./profile">
                    <?php if ($view->controller->profileArray['is_admin']) : ?>
                        👑
                    <?php endif ?>
                    <span style="text-decoration: underline;"> <?php echo $view->controller->profileArray['name'] ?></span>
                </a>
            </small>
        <?php endif ?>
        <hr>
        <?php $view->termHome() ?>
    </main>
    <?php $view->footer() ?>
    <script>
        async function copyUrl(text) {
            try {
                await navigator.clipboard.writeText(text)
                alert("リンクをコピーしました");
            } catch {
                alert("コピーできませんでした\n(非対応ブラウザ)");
            }
        }
    </script>
</body>

</html>