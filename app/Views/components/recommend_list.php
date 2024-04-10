<?php

/** @var \App\Services\Recommend\Dto\RecommendListDto $recommend */

use App\Config\AppConfig;
use App\Services\Recommend\Enum\RecommendListType;

?>
<aside style="margin: 0 -1rem;">
    <div class="btn-wrapper">
        <div style="display: flex; flex-direction: row; /* align-items: center; */ margin: 0 1rem;">
            <div aria-hidden="true" style="font-size: 12px; user-select: none;">🎖</div>
            <?php if ($recommend->type === RecommendListType::Category) : ?>
                <h3>
                    <span>
                        「<?php echo $recommend->listName ?>」
                    </span>
                    <div>
                        <span>カテゴリーのおすすめ</span>
                    </div>
                    <small style="font-size: 11px; font-weight:normal; color:#b7b7b7; margin-left: 4px;">最新</small>
                </h3>
            <?php else : ?>
                <a class="unset" style="cursor: pointer;" href="<?php echo url("recommend?tag=" . urlencode($recommend->listName)) ?>">
                    <h3>
                        <span style="text-decoration: underline; text-decoration-color: #777;">
                            「<?php echo $recommend->listName ?>」
                        </span>
                        <div style="text-decoration: underline; text-decoration-color: #777;">
                            <span>関連のおすすめ</span>
                        </div>
                        <small style="font-size: 11px; font-weight:normal; color:#b7b7b7; margin-left: 4px;">最新</small>
                    </h3>
                </a>
            <?php endif ?>
        </div>
        <button type="button" class="read-more-list-btn" onclick="this.textContent = this.parentElement.nextElementSibling.classList.toggle('show-all') ? '一部を表示' : 'もっと見る';">もっと見る</button>
    </div>
    <ul class="recommend-list">
        <?php foreach ($recommend->getList(false) as $roc) : ?>
            <li>
                <a href="<?php echo url('/oc/' . $roc['id']) . ($roc['table_name'] === AppConfig::RankingHourTable ? '?limit=hour' : '') ?>">
                    <img loading="lazy" alt="<?php echo $roc['name'] ?>" src="<?php echo imgPreviewUrl($roc['id'], $roc['img_url']) ?>" />
                    <h4>
                        <?php echo $roc['name'] ?>
                    </h4>
                    <div class="recommend-desc"><?php echo $roc['description'] ?></div>
                    <div class="recommend-member">
                        <span>
                            <?php if ($roc['member'] === $recommend->maxMemberCount && ($tag !== $recommend->listName || $recommend->maxMemberCount >= $member)) : ?>
                                <span aria-hidden="true" style="margin: 0 -2px; font-size: 9px; user-select: none;">🏆</span>
                                <span style="font-weight: bold;">メンバー <?php echo formatMember($roc['member']) ?>人</span>
                            <?php else : ?>
                                <span>メンバー <?php echo formatMember($roc['member']) ?>人</span>
                            <?php endif ?>
                            <?php if ($roc['table_name'] === AppConfig::RankingHourTable) : ?>
                                <span aria-hidden="true" style="margin: 0 -3px; font-size: 9px; user-select: none;">🔥</span>
                            <?php endif ?>
                            <?php if ($roc['table_name'] === AppConfig::RankingDayTable) : ?>
                                <span aria-hidden="true" style="margin: 0 -2px; font-size: 9px; user-select: none;">🚀</span>
                            <?php endif ?>
                            <?php if ($roc['table_name'] === AppConfig::RankingWeekTable) : ?>
                                <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                    <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                </svg>
                            <?php endif ?>
                        </span>
                    </div>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
</aside>