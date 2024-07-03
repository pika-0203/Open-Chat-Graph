<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;
use Shadow\Kernel\Reception;

$view = new AccreditationAdminViewContent($controller);
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <main>
        <?php $view->mainTab() ?>
        <div style="margin-bottom: 1rem;">
            <p>
                <?php if (!Reception::has('all')) : ?>
                    <small><?php echo $view->examTypeName ?>の問題を投稿した人の一覧です。</small>
                <?php else : ?>
                    <small>問題を投稿した人の一覧です。</small>
                <?php endif ?>
            </p>
        </div>
        <small style="margin-right: auto; user-select: none; font-size: 15px; color: #000; font-weight: 700; margin-bottom: 8px; display:block;">投稿者 <?php echo count($view->controller->currentContributorsArray) ?> 人</small>
        <?php $view->contributors() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>