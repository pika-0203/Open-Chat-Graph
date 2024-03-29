<!-- @param array $openChatList -->
<!-- @param bool $isHourly -->
<ol class="openchat-item-list unset">
  <?php foreach ($openChatList as $oc) : ?>
    <li class="openchat-item unset  <?php echo isset($oc['archived_at']) ? 'registration' : '' ?>">
      <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id'] . (($isHourly ?? false) && ($oc['diff_member'] ?? null) !== null ? '?limit=hour' : '')) ?>" tabindex="-1" aria-hidden="true"></a>
      <img alt="オープンチャット「<?php echo $oc['name'] ?>」のアイコン" class="openchat-item-img" loading="lazy" src="<?php echo imgPreviewUrl($oc['id'], $oc['img_url']) ?>">
      <h3 class="unset">
        <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id'] . (($isHourly ?? false) && ($oc['diff_member'] ?? null) !== null ? '?limit=hour' : '')) ?>"><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></a>
      </h3>
      <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
      <footer class="openchat-item-lower-outer">
        <div class="openchat-item-lower unset <?php echo ($oc['diff_member'] ?? 1) > 0 ? 'positive' : 'negative' ?>">
          <?php if (isset($oc['datetime'])) : ?>
            <span class="registration-date blue"><?php echo ($isAdmin ?? false) ? convertDatetime($oc['datetime'], true) : getCronModifiedDateTime($oc['datetime']) ?></span>
          <?php endif ?>
          <?php if (isset($oc['member'])) : ?>
            <span>参加者数 <?php echo number_format($oc['member']) ?>人</span>
          <?php endif ?>
          <?php if (($oc['diff_member'] ?? 0) !== 0) : ?>
            <span>
              <span class="openchat-item-stats">↑ <?php echo $oc['diff_member'] ?>人増加</span>
            </span>
          <?php elseif (($oc['diff_member'] ?? 1) === 0) : ?>
            <span>±0人</span>
          <?php endif ?>
          <?php if (isset($oc['time'])) : ?>
            <span class="registration-date blue"><?php echo timeElapsedString($oc['time']) ?></span>
          <?php endif ?>
        </div>
        <?php if (isset($oc['category']) && $oc['category']) : ?>
          <div class="openchat-item-mui-chip-outer">
            <span class="openchat-item-mui-chip-inner" aria-label="カテゴリ: <?php echo getCategoryName($oc['category']) ?>"><?php echo getCategoryName($oc['category']) ?></span>
          </div>
        <?php endif ?>
      </footer>
    </li>
  <?php endforeach ?>
</ol>