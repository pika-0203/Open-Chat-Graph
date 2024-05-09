<!-- @param array $openChatList -->
<!-- @param bool $isHourly -->
<style>
  .openchat-item {
    margin: 20px 0;
    margin-right: 1.5rem;
  }
</style>
<ol class="openchat-item-list unset">
  <?php /** @var \App\Services\Recommend\Dto\RecommendListDto $recommend */

  use App\Config\AppConfig;

  foreach (isset($limit) ? array_slice($recommend->getList($shuffle ?? false), 0, $limit) : $recommend->getList(false) as $key => $oc) : ?>
    <li class="unset">

      <div class="openchat-item">
        <a class="link-overlay unset" href="<?php echo url('/oc/' . $oc['id']) . ($oc['table_name'] === AppConfig::RankingHourTable || $oc['table_name'] === AppConfig::RankingDayTable ? '?limit=hour' : '') ?>" tabindex="-1" aria-hidden="true">
          <span class="visually-hidden"><?php echo $oc['name'] ?></span>
        </a>
        <img alt="<?php echo $oc['name'] ?>" class="openchat-item-img" loading="lazy" src="<?php echo imgPreviewUrl($oc['id'], $oc['img_url']) ?>">
        <h3 class="unset">
          <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $oc['id']) . ($oc['table_name'] === AppConfig::RankingHourTable || $oc['table_name'] === AppConfig::RankingDayTable ? '?limit=hour' : '') ?>"><?php if (($oc['emblem'] ?? 0) === 1) : ?><span class="super-icon sp"></span><?php elseif (($oc['emblem'] ?? 0) === 2) : ?><span class="super-icon official"></span><?php endif ?><?php if (($oc['join_method_type'] ?? 0) === 2) : ?><span class="lock-icon"></span><?php endif ?><?php echo $oc['name'] ?></a>
        </h3>
        <p class="openchat-item-desc unset"><?php echo $oc['description'] ?></p>
        <footer class="openchat-item-lower-outer">
          <div class="openchat-item-lower unset" style="font-size: 12px;">
            <?php if (isset($oc['member'])) : ?>
              <span>
                <?php if ($oc['member'] === $recommend->maxMemberCount) : ?>
                  <span aria-hidden="true" style="font-size: 9px; user-select: none;">🏆</span>
                  <span style="font-weight: bold;">メンバー <?php echo formatMember($oc['member']) ?>人</span>
                <?php else : ?>
                  <span>メンバー <?php echo formatMember($oc['member']) ?>人</span>
                <?php endif ?>
                <?php if ($oc['table_name'] === AppConfig::RankingHourTable) : ?>
                  <span aria-hidden="true" style="font-size: 9px; user-select: none;">🔥</span>
                <?php endif ?>
                <?php if ($oc['table_name'] === AppConfig::RankingDayTable) : ?>
                  <span aria-hidden="true" style="font-size: 9px; user-select: none;">🚀</span>
                <?php endif ?>
                <?php if ($oc['table_name'] === AppConfig::RankingWeekTable) : ?>
                  <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                    <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                  </svg>
                <?php endif ?>
              </span>
            <?php endif ?>
          </div>
        </footer>
      </div>
    </li>
  <?php endforeach ?>
</ol>