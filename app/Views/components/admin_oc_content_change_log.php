<!-- @param array $archiveList -->
<h2 class="changelog-h2">変更履歴</h2>
<ol class="openchat-item-list changelog-area description-close unset" id="changelog-area">
    <?php foreach ($archiveList as $key => $oc) : ?>
        <li class="openchat-item <?php if (2 < $key) echo 'hidden-changelog' ?> unset registration">
            <?php $url = 'oc/' . $oc['id'] . '/archive/' . $oc['group_id'] ?>
            <span class="registration-date blue"><?php echo convertDatetimeAndOneDayBefore($oc['archived_at']) ?> 時点</span>
            <a class="link-overlay unset" href="<?php echo url($url) ?>" tabindex="-1" aria-hidden="true"></a>
            <img alt="オープンチャット「<?php echo $oc['name'] ?>」のアイコン" class="openchat-item-img <?php if ($oc['update_img'] ?? 0) echo 'border-blue' ?>" loading="lazy" src="<?php echo imgPreviewUrlLocal($oc['id'], $oc['img_url']) ?>">
            <h3 class="unset" <?php aliveStyleColor($oc) ?>>
                <a class="openchat-item-title unset" href="<?php echo url($url) ?>" style="<?php if ($oc['update_name']) echo 'color:#1042E5' ?>"><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></a>
            </h3>
            <p class="openchat-item-desc unset" style="<?php if ($oc['update_description']) echo 'color:#1042E5' ?>"><?php echo $oc['description'] ?></p>
            <footer class="openchat-item-lower unset" style="padding: 0;">
                <span>メンバー <?php echo number_format($oc['member']) ?></span>
            </footer>
        </li>
    <?php endforeach; ?>
    <?php if (count($archiveList) > 3) : ?>
        <button id="read_more_btn_changelog" class="unset" aria-label="続きを読む">
            <div class="read_more_btn_icon"></div>
            <span class="read_more_btn_text">続きを読む</span>
        </button>
    <?php endif ?>
</ol>