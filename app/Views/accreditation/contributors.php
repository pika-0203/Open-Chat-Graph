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
        <h2>投稿者の一覧<?php $view->examTitle() ?></h2>
        <p>
            <small><?php echo $view->examTypeName ?>の問題を投稿した人の一覧です。</small>
        </p>
        <hr>
        <?php $view->contributors() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>