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
        <?php if ($view->controller->currentId === $view->controller->myId) : ?>
            <h2>投稿した問題<?php $view->examTitle() ?></h2>
            <?php $view->userTerm() ?>
            <hr>
            <?php $view->questionList() ?>
        <?php else : ?>
            <h2>投稿者<?php $view->examTitle() ?></h2>
            <?php $view->profile(true) ?>
            <?php $view->questionList() ?>
        <?php endif ?>
    </main>
    <?php $view->footer(false) ?>
</body>

</html>