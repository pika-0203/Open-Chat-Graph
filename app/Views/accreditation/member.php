<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <main>
        <h2>ユーザー管理 <small style="font-weight: normal; font-size:13px">(サイト管理者機能)</small></h2>
        <p>
            <small>ラインでログインしてプロフィールを作成したユーザーを全て表示しています。<br>ユーザーは、ブロンズ・シルバー・ゴールドで共通です。</small>
        </p>
        <hr>
        <small style="margin-right: auto; user-select: none; font-size: 14px;">ユーザー数 <?php echo count($view->controller->currentContributorsArray) ?> 人</small>
        <?php $view->contributors() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>