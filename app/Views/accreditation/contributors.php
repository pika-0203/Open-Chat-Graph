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
        <?php $view->mainTab() ?>
        <div style="margin-top:2rem;">
            <h2>投稿者の一覧<?php $view->examTitle() ?></h2>
            <p>
                <small><?php echo $view->examTypeName ?>の問題を投稿した人の一覧です。</small>
            </p>
        </div>
        <hr>
        <small style="margin-right: auto; user-select: none; font-size: 15px; color: #000; font-weight: 700; margin-bottom: 8px; display:block;">投稿者 <?php echo count($view->controller->currentContributorsArray) ?> 人</small>
        <?php $view->contributors() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>