<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);
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
    </style>
    <main>
        <?php $view->mainTab() ?>
        <section style="gap: 2rem; padding: 2rem 0 0 0;">
            <section style="align-items: center; flex-direction: column;">
                <span>投稿された問題数</span><b style="font-size: 56px; line-height: 1;"><?php echo $total_count ?? 0 ?></b>
            </section>
            <section style="align-items: center; flex-direction: column;">
                <span style="font-size: 14px;">出題中の問題数</span><b style="font-size: 40px; line-height: 1;"><?php echo $publishing_count ?? 0 ?></b>
            </section>
        </section>
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