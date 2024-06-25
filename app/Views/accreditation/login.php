<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);
$returnTo = "?return_to=/accreditation/{$view->controller->type->value}";
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <main>
        <section>
            <div>
                <div>
                    <a style="display:flex; width:fit-content" href="/auth/login<?php echo $returnTo ?>/home"><img style="disply: block; width: 240px;" src="<?php echo fileUrl('assets/line_login_btn.png') ?>"></a>
                </div>
            </div>
            <hr>
            <p>
                <small>
                    オプチャ検定の問題投稿ページでは、ユーザー登録のためにLINEログインを利用しています。<br>
                    <br>
                    当サイトではLINEに登録している名前やプロフィール画像など、LINEに関する利用情報を取得しないため、安心してご利用いただけます。<br>
                    LINEアプリに当サイトから通知が来ることもありません。<br>
                </small>
            </p>
            <hr>
            <?php $view->term() ?>
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
    <?php $view->footer() ?>
</body>

</html>