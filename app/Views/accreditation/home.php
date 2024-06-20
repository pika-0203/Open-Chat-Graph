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
        <br>
        <section style="gap: 2rem; padding: 1rem 0 0 0;">
            <section style="align-items: center; flex-direction: column;">
                <span>投稿された問題数</span><b style="font-size: 56px; line-height: 1;"><?php echo $total_count ?? 0 ?></b>
            </section>
            <section style="align-items: center; flex-direction: column;">
                <span style="font-size: 14px;">出題中の問題数</span><b style="font-size: 40px; line-height: 1;"><?php echo $publishing_count ?? 0 ?></b>
            </section>
        </section>
        <section style="margin: 2rem 0; gap: 1.8rem; font-weight: bold;">
            <?php foreach ([
                'bronze' => 'ブロンズ',
                'gold' => 'ゴールド',
                'silver' => 'シルバー',
            ] as $key => $value) : ?>
                <?php if ($key !== $view->controller->type->value) : ?>
                    <a href="./../<?php echo $key ?>/home"><?php echo $value ?></a>
                <?php else : ?>
                    <b><?php echo $value ?></b>
                <?php endif ?>
            <?php endforeach ?>
        </section>
        <hr>
        <a href="/accreditation" target="_blank" style="font-size: 14px;">公認メンター監修・オプチャ検定｜練習問題</a>
        <hr>
        <?php $view->termHome() ?>
    </main>
    <?php $view->footer() ?>
</body>

</html>