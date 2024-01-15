<!-- @param array $archiveList -->
<!-- @param array $isDaily -->
<?php if ($archiveList) : ?>
    <ol class="openchat changelog-area description-close unset" id="changelog-area">
        <h2>変更履歴</h2>
        <?php foreach ($archiveList as $key => $oc) : ?>
            <li class="<?php if ($key > 3) echo 'hidden-changelog' ?>">
                <span class="archived-at"><?php echo convertDatetime($oc['archived_at']) ?></span>
                <br>
                <span>
                    <?php
                    $elements = [];

                    if ($oc['update_name']) $elements[] = 'オープンチャット名';
                    if ($oc['update_description']) $elements[] = '説明文';
                    if ($oc['update_img']) $elements[] = '画像';

                    foreach ($elements as $index => $text) {
                        if (!$text) {
                            continue;
                        }
                        echo $text;
                        if ($elements[$index + 1] ?? false) {
                            echo ', ';
                        }
                    }
                    ?>
                </span>

                <span class="archived-member">(メンバー <?php echo number_format($oc['member']) ?>)</span>
            </li>
        <?php endforeach; ?>
        <?php if (count($archiveList) > 4) : ?>
            <button id="read_more_btn_changelog" class="unset" aria-label="続きを読む">
                <div class="read_more_btn_icon"></div>
                <span class="read_more_btn_text">続きを読む</span>
            </button>
        <?php endif ?>
    </ol>
<?php endif ?>