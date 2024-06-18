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
        <hr>
        <?php $view->contributors() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>