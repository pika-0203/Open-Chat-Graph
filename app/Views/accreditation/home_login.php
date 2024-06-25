<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$returnTo = "?return_to=/accreditation/bronze/home";
?>

<!DOCTYPE html>
<html lang="ja">
<?php AccreditationAdminViewContent::headComponent() ?>

<body>
    <style>
        hr {
            border-bottom: 1px #efefef solid;
            margin: 2rem 0;
        }

        section aside {
            width: 100%;
            margin: 1rem 0;
        }

        body,
        footer {
            padding-top: 0;
        }

        header {
            padding-bottom: 0;
            word-break: keep-all;
            overflow-wrap: anywhere
        }

        main {
            padding-top: 2rem;
        }

        small {
            font-size: 15px;
        }
    </style>
    <header>
        <img style="padding: 0 2rem; max-width: 300px;" src="<?php echo fileUrl('assets/accreditation-log.svg') ?>" />
        <div style="margin-top: 1rem;">
            <span>問題投稿ページ</span>
        </div>
        <hr>
        <p>
            オプチャ検定は、LINEオープンチャットの<wbr>利用に関連する知識を深める場所です。<br>
            ガイドライン、ルール、管理方法などに<wbr>ついて楽しく学ぶことができます。
        </p>
        <p>
            問題投稿ページでは誰でも問題を投稿でき、<wbr>他の人が投稿した問題を閲覧できます。<br>
            自分の知識を共有して、<wbr>オープンチャットコミュニティに<wbr>貢献できます！
        </p>
    </header>
    <main>
        <section>
            <div>
                <div>
                    <a style="display:flex; width:fit-content" href="/auth/login<?php echo $returnTo ?>"><img style="disply: block; width: 240px;" src="<?php echo fileUrl('assets/line_login_btn.png') ?>"></a>
                </div>
            </div>
            <hr>
            <p>
                <a href="/accreditation" target="_blank">オプチャ検定｜練習問題</a>
                <br>
                <small>(現在オプチャ検定の問題が出題されている場所です)</small>
            </p>
            <p>
                <small>このサイトではオプチャ検定の問題集を管理しています。<br>LINEログインでメンバー登録を行い、誰でも問題文を投稿して作成に協力することができます。</small>
            </p>
            <p>
                <small>問題が検定に出題された際は、出題者としてニックネームとオプチャ名・オプチャリンクが検定サイト上に掲載されます。</small>
            </p>
            <p>
                <small>投稿された問題は、検定に合わせるためにサイト管理者が編集する場合があります。<br>問題数が限られているので、実際に出題されるのは一部の範囲の問題となります。</small>
            </p>
            <hr>
            <p>
                <small>
                    オプチャ検定の問題投稿ページでは、メンバー登録のためにLINEログインを利用しています。<br>
                </small>
            </p>
            <p>
                <small>
                    LINEに登録している名前やプロフィール画像などが表示されることはなく、お好きなニックネームを設定できます。<br>
                    また、料金が発生することはなく、無料でご利用いただけます。<br>
                </small>
            </p>
            <p>
                <small>
                    当サイトではLINEに登録している個人情報などを取得しないため、安心してご利用いただけます。<br>
                    LINEアプリに当サイトから通知が来ることもありません。<br>
                </small>
            </p>
        </section>
    </main>
    <footer>
        <hr style="margin-top: 0rem;">
        <p>
            <small>「オプチャ検定」はLINEオープンチャット非公式の検定です。LINEヤフー社はこの内容に関与していません。<br>監修しているのは一部のLINEオープンチャット公認メンターです。</small>
        </p>
        <small><a href="/accreditation/privacy" style="color: #b7b7b7; text-decoration: none;">プライバシーポリシー</a></small>
    </footer>
</body>

</html>