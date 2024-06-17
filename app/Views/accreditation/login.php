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
            <a style="display:flex; width:fit-content" href="/auth/login<?php echo $returnTo ?>/home"><img style="disply: block; width: 240px;" src="<?php echo fileUrl('assets/line_login_btn.png') ?>"></a>
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
            <?php $view->term() ?>
        </section>
    </main>
    <?php $view->footer() ?>
</body>

</html>