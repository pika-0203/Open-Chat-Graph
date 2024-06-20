<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);
$returnTo = "?return_to=/accreditation/{$view->controller->type->value}/editor?id={$view->controller->currentId}";
$deleteReturnTo = "?return_to=" . ($_SERVER['HTTP_REFERER'] ?? "/accreditation/{$view->controller->type->value}/home");
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <main>
        <h2>問題を編集<?php $view->examTitle() ?></h2>
        <?php $view->termEditor() ?>
        <hr>
        <?php $view->questionList(true) ?>
        <?php $view->questionForm($returnTo, true) ?>
        <br>
        <?php $view->deleteQuestionForm($deleteReturnTo) ?>
        <?php $view->adminQuestionForm($returnTo, $deleteReturnTo) ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>