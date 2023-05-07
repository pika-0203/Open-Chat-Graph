<!-- @param array $openChatList -->
<?php foreach ($openChatList as $oc) : ?>
    <section>
        <div class="openchat-item">
            <a href="<?php echo url('/oc/' . $oc['id']) ?>">
                <div class="openchat-item-img">
                    <img src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $oc['img_url'] . \App\Config\AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp') ?>" alt="オープンチャット「<?php echo $oc['name'] ?>」" />
                </div>
                <div class="openchat-item-info">
                    <span class="openchat-item-title"><?php echo $oc['name'] ?></span>
                    <span class="openchat-item-desc"><?php echo $oc['description'] ?></span>
                    <div class="openchat-item-lower">
                        <span>メンバー<?php echo $oc['member'] ?></span>
                        <?php if ($oc['diff_member'] !== 0) : ?>
                            <div class="openchat-item-stats <?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
                                <span><?php echo signedNum($oc['diff_member']) ?></span>
                                <span>(<?php echo signedNum(singnedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </a>
        </div>
    </section>
<?php endforeach; ?>