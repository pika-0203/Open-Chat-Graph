<!-- @param array $openChatList -->
<!-- @param bool $_now -->
<ol class="openchat-item-list unset">
  <?php foreach ($openChatList as $oc) : ?>
    <li class="openchat-item unset" style="margin-right: 0;">
      <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id'] . (($isHourly ?? false) && ($oc['diff_member'] ?? null) !== null ? '?limit=hour' : '')) ?>" tabindex="-1" aria-hidden="true">
        <span class="visually-hidden"><?php echo $oc['name'] ?></span>
      </a>
      <img alt="<?php echo $oc['name'] ?>" class="openchat-item-img" loading="lazy" src="<?php echo imgPreviewUrl($oc['id'], $oc['img_url']) ?>">
      <h3 class="unset">
        <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id'] . (($isHourly ?? false) && ($oc['diff_member'] ?? null) !== null ? '?limit=hour' : '')) ?>"><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><span><?php echo $oc['name'] ?></span></a>
      </h3>
      <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
      <footer class="openchat-item-lower-outer" style="margin-top: 2px; gap: 0;">
        <div class="openchat-item-lower unset" style="font-size: 12px; <?php if ($oc['end_datetime'] === $_now || $oc['old_datetime'] === $_now) echo 'font-weight: bold;' ?>">
          <?php if (isset($oc['end_datetime'])) : ?>
            <span class="registration-date blue">再掲載 <span style="font-weight: bold;"><?php echo calculateTimeDifference($oc['end_datetime'], $oc['old_datetime']) ?></span>: <?php echo convertDatetime($oc['old_datetime'], true) ?>~<?php echo convertDatetime($oc['end_datetime'], true) ?></span>
          <?php else : ?>
            <span class="registration-date" style="color: #ff5d6d;">未掲載: <?php echo convertDatetime($oc['old_datetime'], true) ?>~</span>
          <?php endif ?>
        </div>
        <div class="openchat-item-lower unset" style="font-size: 12px;">
          <span class="openchat-item-stats" style="font-weight: <?php echo ($oc['percentage'] <= 50) ? "bold" : "normal" ?>;">最終順位: <?php echo calculatePositionPercentage($oc['percentage']) ?></span>
          <span style="margin-left: 4px;">
            <span>最終人数: <?php echo formatMember($oc['old_member']) ?>人</span>
            <span class="openchat-item-stats">(<?php echo signedNumF($oc['member'] - $oc['old_member']) ?: '±0' ?>人)</span>
          </span>
        </div>
        <div class="openchat-item-lower unset" style="font-size: 12px; font-weight: <?php echo $oc['update_items'] ? "bold" : "normal" ?>; color: <?php echo $oc['update_items'] ? "#aaa" : "#b7b7b7" ?>;">
          <span>変更箇所: </span>
          <?php if ($oc['update_items']) : ?>
            <?php foreach ($oc['update_items'] as $item) : ?>
              <?php if ($item === 'name') : ?>
                <span>ルーム名</span>
              <?php elseif ($item === 'description') : ?>
                <span>説明文</span>
              <?php elseif ($item === 'img_url') : ?>
                <span>画像</span>
              <?php elseif ($item === 'join_method_type') : ?>
                <span>公開設定</span>
              <?php elseif ($item === 'category') : ?>
                <span>カテゴリー</span>
              <?php elseif ($item === 'emblem') : ?>
                <span>バッジ</span>
              <?php else : ?>
                <span><?php echo $item ?></span>
              <?php endif ?>
            <?php endforeach ?>
          <?php else : ?>
            <span>なし</span>
          <?php endif ?>
        </div>
        <?php if (isset($oc['category']) && $oc['category']) : ?>
          <!--<div class="openchat-item-mui-chip-outer">
            <span class="openchat-item-mui-chip-inner" aria-label="カテゴリ: <?php echo getCategoryName($oc['category']) ?>"><?php echo getCategoryName($oc['category']) ?></span>
          </div>-->
        <?php endif ?>
      </footer>
    </li>
  <?php endforeach ?>
</ol>