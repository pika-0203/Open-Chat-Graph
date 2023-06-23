<!-- @param array $openChatList -->
<!-- @param array $isDaily -->
<ol class="openchat-item-list unset">
    <?php foreach ($openChatList as $oc) : ?>
        <li class="openchat-item unset">
            <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id']) ?>" tabindex="-1" aria-hidden="true"></a>
            <img alt="オープンチャット「<?php echo $oc['name'] ?>」のアイコン" class="openchat-item-img" loading="lazy" src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $oc['img_url'] . \App\Config\AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp') ?>">
            <h3 class="unset">
                <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id']) ?>"><?php echo $oc['name'] ?></a>
            </h3>
            <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
            <footer class="openchat-item-lower unset <?php echo ($oc['diff_member'] ?? 0) > 0 ? 'positive' : 'negative' ?> <?php echo ($isDaily ?? true) ? '' : 'weekly' ?>">
                <span>メンバー <?php echo $oc['member'] ?></span>
                <?php if (($oc['diff_member'] ?? 0) !== 0) : ?>
                    <span>
                        <span class="openchat-item-stats"><?php echo signedNum($oc['diff_member']) ?></span>
                        <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                        <span class="openchat-item-stats"><?php echo signedNum($oc['diff_member']) ?></span>
                        <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                    </span>
                <?php elseif (($oc['diff_member'] ?? 1) === 0) : ?>
                    <span class="<?php echo ($isDaily ?? true) ? '' : 'openchat-item-stats-weekly-zero' ?>">±0</span>
                <?php endif ?>
            </footer>
        </li>
    <?php endforeach; ?>
</ol>