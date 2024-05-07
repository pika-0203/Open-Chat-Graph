<!-- @param array $openChatList -->
<!-- @param bool $_now -->
<ol class="openchat-item-list unset">
  <?php foreach ($openChatList as $key => $oc) : ?>
    <?php $timeFrame = $oc['end_datetime'] ? calculateTimeFrame($_now, $oc['end_datetime']) : calculateTimeFrame($_now, $oc['old_datetime']) ?>
    <li style="all: unset; display: block;">
      <?php if ($key && $key % 10 === 0) : ?>
        <div style="margin: 1rem 0;">
          <?php viewComponent('ads/google-publication-analytics') ?>
        </div>
      <?php endif ?>
      <div class="openchat-item unset" style="margin-right: 0;">
        <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id'] . "?bar=ranking&limit={$timeFrame}") ?>" tabindex="-1" aria-hidden="true">
          <span class="visually-hidden"><?php echo $oc['name'] ?></span>
        </a>
        <img alt="<?php echo $oc['name'] ?>" class="openchat-item-img" loading="lazy" src="<?php echo imgPreviewUrl($oc['id'], $oc['img_url']) ?>">
        <h3 class="unset">
          <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id'] . "?bar=ranking&limit={$timeFrame}") ?>"><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><?php if (($oc['join_method_type'] ?? 0) === 2) : ?><span class="lock-icon"></span><?php endif ?><span><?php echo $oc['name'] ?></span></a>
        </h3>
        <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
        <footer class="openchat-item-lower-outer" style="gap: 0;">
          <div class="openchat-item-lower unset">
            <span class="member-count">
              <span>メンバー <?php echo formatMember($oc['old_member']) ?></span>
              <span class="openchat-item-stats">(<?php echo signedNumF($oc['member'] - $oc['old_member']) ?: '±0' ?>)</span>
            </span>
            <span class="openchat-item-stats">順位 <?php echo calculatePositionPercentage($oc['percentage']) ?></span>
          </div>
          <?php if (isset($oc['category']) && $oc['category']) : ?>
            <div class="openchat-item-mui-chip-outer" style="margin-top: 0px;">
              <span class="openchat-item-mui-chip-inner" aria-label="カテゴリ: <?php echo getCategoryName($oc['category']) ?>"><?php echo getCategoryName($oc['category']) ?></span>
            </div>
          <?php endif ?>
          <?php if ($oc['update_items']) : ?>
            <div class="openchat-item-lower unset" style="margin-top: 2px;">
              <?php if ($oc['updated_at']) : ?>
                <span>変更により未掲載: </span>
              <?php else : ?>
                <span>変更箇所: </span>
              <?php endif ?>
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
            </div>
          <?php endif ?>
          <div class="openchat-item-lower unset" style="margin-top: 1px;">
            <?php if (isset($oc['end_datetime'])) : ?>
              <span class="registration-date"><span style="font-weight: bold;">再掲載</span> <span class="blue"><?php echo calculateTimeDifference($oc['end_datetime'], $oc['old_datetime']) ?></span> <?php echo formatDateTimeHourly2($oc['old_datetime']) ?>~<?php echo formatDateTimeHourly2($oc['end_datetime']) ?></span>
            <?php else : ?>
              <span class="registration-date">未掲載 <span class="blue"><?php echo $_now === $oc['old_datetime'] ? 'たった今' : calculateTimeDifference($_now, $oc['old_datetime']) . '前' ?></span> <?php echo formatDateTimeHourly2($oc['old_datetime']) ?>~</span>
            <?php endif ?>
          </div>
        </footer>
      </div>
    </li>
  <?php endforeach ?>
</ol>