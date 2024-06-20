<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;

$view = new AccreditationAdminViewContent($controller);
$returnTo = "?return_to=/accreditation/{$view->controller->type->value}/user?id={$view->controller->myId}";
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
    <?php $view->header() ?>
    <main>
        <h2>問題を投稿<?php $view->examTitle() ?></h2>
        <?php $view->termQ() ?>
        <hr>
        <?php $view->questionForm($returnTo) ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>