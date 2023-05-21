<!-- @param array $openChatList -->
<ol class="openchat-item-list unset">
    <?php foreach ($openChatList as $oc) : ?>
        <li class="openchat-item unset">
            <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id']) ?>" tabindex="-1" aria-hidden="true"></a>
            <img alt="オープンチャット「<?php echo $oc['name'] ?>」のアイコン" class="openchat-item-img" loading="lazy" src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $oc['img_url'] . \App\Config\AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp') ?>">
            <h3 class="unset">
                <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id']) ?>"><?php echo $oc['name'] ?></a>
            </h3>
            <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
            <footer class="openchat-item-lower unset <?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
                <span>メンバー <?php echo $oc['member'] ?></span>
                <?php if ($oc['diff_member'] ?? 0 !== 0) : ?>
                    <span class="openchat-item-stats"><?php echo signedNum($oc['diff_member']) ?></span>
                    <span class="openchat-item-stats">(<?php echo signedNum(singnedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                <?php elseif ($oc['diff_member'] === 0) : ?>
                    <span>±0</span>
                <?php endif ?>
            </footer>
        </li>
    <?php endforeach; ?>
</ol>