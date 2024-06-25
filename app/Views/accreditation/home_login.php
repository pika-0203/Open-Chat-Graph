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
    </style>
    <main>
        <header>
            <img style="padding: 0 2rem; max-width: 300px;" src="<?php echo fileUrl('assets/accreditation-log.svg') ?>" />
            <div style="margin-top: 1rem;">
                <span>問題投稿ページ</span>
            </div>
        </header>
        <section>
            <div>
                <div>
                    <a style="display:flex; width:fit-content" href="/auth/login<?php echo $returnTo ?>"><img style="disply: block; width: 240px;" src="<?php echo fileUrl('assets/line_login_btn.png') ?>"></a>
                </div>
            </div>
            <hr>
            <p>
                <small>
                    当サイト内ではユーザー登録のためにLINEログインが必要です。<br>
                    <br>
                    当サイトではLINEの個人名や個人情報、LINEに関する利用者の情報を取得しないため、安心してご利用いただけます。<br>
                    LINEアプリに当サイトから通知が来ることもありません。<br>
                </small>
            </p>
            <hr>
            <p>
                <small>このサイトでは検定の問題集を管理しています。<br>LINEログインでメンバー登録を行い、誰でも問題文を投稿して作成に協力することができます。</small>
            </p>
            <p>
                <small>問題が検定に出題された際は、出題者としてニックネームとオプチャ名・オプチャリンクが検定サイト上に掲載されます。</small>
            </p>
            <p>
                <small>投稿された問題は、検定に合わせるためにサイト管理者が編集する場合があります。<br>問題数が限られているので、実際に出題されるのは一部の範囲の問題となります。</small>
            </p>
            <hr>
            <a href="/accreditation" target="_blank" style="font-size: 14px;">オプチャ検定｜練習問題</a>
            <hr>
            <aside>
                <small>
                    <b>iOS・Androidの推奨ブラウザ</b>
                </small>
                <ul style="margin: 0;">
                    <li>
                        <small>
                            デフォルトに設定されているブラウザ
                        </small>
                    </li>
                    <li>
                        <small>
                            LINEアプリ内ブラウザ
                        </small>
                    </li>
                </ul>
            </aside>
        </section>
    </main>
    <footer>
        <hr style="margin-top: 0rem;">
        <p>
            <small>「オプチャ検定」はLINEオープンチャット非公式の検定です。LINEヤフー社はこの内容に関与していません。<br>監修しているのは一部のLINEオープンチャット公認メンターです。</small>
        </p>
    </footer>
</body>

</html>