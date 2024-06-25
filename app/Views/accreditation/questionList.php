<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);
$isPublished = $view->controller->pageType === 'published';
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <main>
        <?php $view->mainTab() ?>
        <?php if ($isPublished) : ?>
            <h2>出題中の問題<?php $view->examTitle() ?></h2>
            <p>
                <small>サイト管理者により出題中になった問題一覧です。<br>出題中の問題は実際の検定に出題されます。</small>
            </p>
        <?php else : ?>
            <h2>未公開の問題<?php $view->examTitle() ?></h2>
            <p>
                <small>ユーザーにより投稿され、サイトに保存されている問題一覧です。<br>サイト管理者により未公開・出題中が適宜変更されます。</small>
            </p>
        <?php endif ?>
        <?php $view->questionList() ?>
    </main>
    <?php $view->footer(false) ?>
</body>

</html>