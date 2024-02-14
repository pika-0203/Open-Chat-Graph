<!-- @param array $openChatList -->
<!-- @param bool $isDaily -->
<!-- @param bool $isHourly -->
<ol class="openchat-item-list unset">
  <?php foreach ($openChatList as $oc) : ?>
    <li class="openchat-item unset  <?php echo isset($oc['archived_at']) ? 'registration' : '' ?>">
      <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id'] . (($isHourly ?? false) ? '?limit=hour' : '')) ?>" tabindex="-1" aria-hidden="true"></a>
      <img alt="オープンチャット「<?php echo $oc['name'] ?>」のアイコン" class="openchat-item-img" loading="lazy" src="<?php echo imgPreviewUrl($oc['img_url']) ?>" <?php echo getImgSetErrorTag() ?>>
      <h3 class="unset">
        <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id'] . (($isHourly ?? false) ? '?limit=hour' : '')) ?>"><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></a>
      </h3>
      <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
      <footer class="openchat-item-lower-outer">
        <div class="openchat-item-lower unset <?php echo ($oc['diff_member'] ?? 1) > 0 ? 'positive' : 'negative' ?> <?php echo ($isDaily ?? true) ? '' : 'weekly' ?>">
          <?php if (isset($oc['datetime'])) : ?>
            <span class="registration-date blue"><?php echo ($isAdmin ?? false) ? convertDatetime($oc['datetime'], true) : getCronModifiedDateTime($oc['datetime']) ?></span>
          <?php endif ?>
          <span>メンバー <?php echo number_format($oc['member']) ?></span>
          <?php if (($oc['diff_member'] ?? 0) !== 0) : ?>
            <span>
              <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member']) ?></span>
              <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
            </span>
          <?php elseif (($oc['diff_member'] ?? 1) === 0) : ?>
            <span class="<?php echo ($isDaily ?? true) ? '' : 'openchat-item-stats-weekly-zero' ?>">±0</span>
          <?php endif ?>
        </div>
        <?php if (isset($oc['category']) && $oc['category']) : ?>
          <div class="openchat-item-mui-chip-outer">
            <span class="openchat-item-mui-chip-inner"><?php echo getCategoryName($oc['category']) ?></span>
          </div>
        <?php endif ?>
      </footer>
    </li>
  <?php endforeach ?>
</ol>