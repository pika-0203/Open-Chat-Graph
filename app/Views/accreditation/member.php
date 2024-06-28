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
        <h2>メンバー</h2>
        <p>
            <small>ラインでログインしてプロフィールを作成したメンバーを全て表示しています。<br>メンバーは、ブロンズ・シルバー・ゴールドで共通です。</small>
        </p>
        <hr>
        <small style="margin-right: auto; user-select: none; font-size: 15px; color: #000; font-weight: 700; margin-bottom: 8px; display:block;">メンバー数 <?php echo count($view->controller->currentContributorsArray) - count(AccreditationAdminViewContent::HIDDEN_USER_ID) ?> 人</small>
        <?php $view->contributors() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>