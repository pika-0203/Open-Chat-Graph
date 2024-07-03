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
        <div style="margin-top:0;">
            <?php if ($view->controller->currentId === $view->controller->myId) : ?>
                <?php $view->userTerm() ?>
                <div style="margin: 1rem 0;">
                    <?php $view->typeTab(true) ?>
                </div>
                <?php $view->questionList() ?>
            <?php else : ?>
                <div style="margin: 0 0 -1rem 0;">
                    <?php $view->profile(true) ?>
                </div>
                <div style="margin: 24px 0 1rem 0;">
                    <?php $view->typeTab(true) ?>
                </div>
                <?php $view->questionList() ?>
            <?php endif ?>
    </main>
    <?php $view->footer(false) ?>
</body>

</html>