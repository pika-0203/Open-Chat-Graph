<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;
use App\Services\Recommend\RecommendRankingBuilder;
use App\Services\Recommend\RecommendUtility;

/**
 * @var \DateTime $updatedAt
 */
/**
 * @var \DateTime $hourlyUpdatedAt
 */

viewComponent('oc_head', compact('_css', '_meta', '_schema')); ?>

<body>
  <script type="application/json" id="chart-arg">
    <?php echo json_encode($_chartArgDto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
  </script>
  <script type="application/json" id="stats-dto">
    <?php echo json_encode($_statsDto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
  </script>
  <script type="application/json" id="comment-app-init-dto">
    <?php echo json_encode($_commentArgDto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
  </script>
  <!-- 固定ヘッダー -->
  <?php viewComponent('site_header') ?>
  <article class="unset openchat body" style="overflow: hidden;">
    <?php viewComponent('ads/google-rectangle') ?>


    <!-- オープンチャット表示ヘッダー -->
    <section class="openchat-header unset" style="padding-top: 0;">
      <div class="talkroom_banner_img_area">
        <img class="talkroom_banner_img" aria-hidden="true" alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
      </div>

      <div class="openchat-header-right">
        <a title="<?php echo $oc['name'] ?>" rel="external" target="_blank" href="<?php echo AppConfig::LINE_OPEN_URL . $oc['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX ?>" class="h1-link unset">
          <div class="link-mark"><span class="link-title" style="margin-top: -2px;"><span aria-hidden="true" style="font-size: 10px; margin-right:2px;"></span>LINEオープンチャット</span></div>
          <h1 class="talkroom_link_h1 unset"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></h1>
        </a>

        <div class="talkroom_number_of_members">
          <span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?>人</span>
        </div>

        <?php if (isset($_hourlyRange)) : ?>
          <div class="talkroom_number_of_stats" style="line-height: 135%; margin-top: 4px;">
            <div class="number-box bold">
              <span aria-hidden="true" style="margin-right: 1px; font-size: 9px; user-select: none;">🔥</span>
              <span style="margin-right: 4px;" class="openchat-itme-stats-title"><?php echo $_hourlyRange ?></span>
              <div>
                <span class="openchat-item-stats"><?php echo signedNumF($oc['rh_diff_member']) ?>人</span><span class="openchat-item-stats percent">(<?php echo signedNum(signedCeil($oc['rh_percent_increase'] * 10) / 10) ?>%)</span>
              </div>
            </div>
          </div>
        <?php endif ?>

        <div class="talkroom_number_of_stats">

          <?php if (isset($oc['rh24_diff_member']) && $oc['rh24_diff_member'] >= AppConfig::MIN_MEMBER_DIFF_H24) : ?>
            <div class="number-box bold" style="margin-right: 6px;">
              <span aria-hidden="true" style="margin-right: 1px; font-size: 9px; user-select: none;">🚀</span>
              <span class="openchat-itme-stats-title">24時間</span>
              <div>
                <span class="openchat-item-stats"><?php echo signedNumF($oc['rh24_diff_member']) ?>人</span><span class="openchat-item-stats percent">(<?php echo signedNum(signedCeil($oc['rh24_percent_increase'] * 10) / 10) ?>%)</span>
              </div>
            </div>
          <?php elseif (isset($oc['rh24_diff_member'])) : ?>
            <div class="number-box" style="margin-right: 6px;">
              <span class="openchat-itme-stats-title">24時間</span>
              <?php if (($oc['rh24_diff_member'] ?? 0) !== 0) : ?>
                <div>
                  <span class="openchat-item-stats"><?php echo signedNumF($oc['rh24_diff_member']) ?>人</span><span class="openchat-item-stats percent">(<?php echo signedNum(signedCeil($oc['rh24_percent_increase'] * 10) / 10) ?>%)</span>
                </div>
              <?php elseif ($oc['rh24_diff_member'] === 0) : ?>
                <span class="zero-stats">±0</span>
              <?php endif ?>
            </div>
          <?php endif ?>

          <?php if (isset($oc['diff_member2']) && $oc['diff_member2'] >= AppConfig::MIN_MEMBER_DIFF_H24) : ?>
            <div class="number-box bold" style="margin-right: 6px;">
              <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
              </svg>
              <span class="openchat-itme-stats-title">1週間</span>
              <div>
                <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?>人</span><span class="openchat-item-stats percent">(<?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%)</span>
              </div>
            </div>
          <?php elseif (isset($oc['diff_member2'])) : ?>
            <div class="number-box" style="margin-right: 6px;">
              <span class="openchat-itme-stats-title">1週間</span>
              <?php if (($oc['diff_member2'] ?? 0) !== 0) : ?>
                <div>
                  <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?>人</span><span class="openchat-item-stats percent">(<?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%)</span>
                </div>
              <?php elseif ($oc['diff_member2'] === 0) : ?>
                <span class="zero-stats">±0</span>
              <?php endif ?>
            </div>
          <?php endif ?>

        </div>

        <?php if ($oc['url']) : ?>
          <section class="open-btn pc-btn">
            <a href="<?php echo AppConfig::LINE_APP_URL . $oc['url'] . AppConfig::LINE_APP_SUFFIX ?>" class="openchat_link">
              <?php if ($oc['join_method_type'] !== 0) : ?>
                <svg style="height: 13px; fill: #fff; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 489.4 489.4" xml:space="preserve">
                  <path d="M99 147v51.1h-3.4c-21.4 0-38.8 17.4-38.8 38.8v213.7c0 21.4 17.4 38.8 38.8 38.8h298.2c21.4 0 38.8-17.4 38.8-38.8V236.8c0-21.4-17.4-38.8-38.8-38.8h-1v-51.1C392.8 65.9 326.9 0 245.9 0 164.9.1 99 66 99 147m168.7 206.2c-3 2.2-3.8 4.3-3.8 7.8.1 15.7.1 31.3.1 47 .3 6.5-3 12.9-8.8 15.8-13.7 7-27.4-2.8-27.4-15.8v-.1c0-15.7 0-31.4.1-47.1 0-3.2-.7-5.3-3.5-7.4-14.2-10.5-18.9-28.4-11.8-44.1 6.9-15.3 23.8-24.3 39.7-21.1 17.7 3.6 30 17.8 30.2 35.5 0 12.3-4.9 22.3-14.8 29.5M163.3 147c0-45.6 37.1-82.6 82.6-82.6 45.6 0 82.6 37.1 82.6 82.6v51.1H163.3z" />
                </svg>
              <?php endif ?>
              <span class="text">LINEで開く</span>
              <?php if ($oc['join_method_type'] === 1) : ?>
                <span style="font-size: 12px; margin-left: 4px; font-weight: normal; line-height: 2;" class="text">承認制</span>
              <?php endif ?>
              <?php if ($oc['join_method_type'] === 2) : ?>
                <span style="font-size: 12px; margin-left: 4px; font-weight: normal; line-height: 2;" class="text">参加コード入力制</span>
              <?php endif ?>
            </a>
          </section>
        <?php endif ?>

      </div>

    </section>

    <?php if ($oc['description']) : ?>
      <hr class="hr-bottom" style="padding: 8px 0; margin-top: 0;">
      <div class="talkroom_description_box close" id="talkroom_description_box" style="margin: 0 1rem 1rem 1rem;">
        <p class="talkroom_description" id="talkroom-description">
          <span id="talkroom-description-btn">
            <?php echo nl2brReplace(trim(preg_replace("/(\r\n){3,}|\r{3,}|\n{3,}/", "\n\n", $oc['description']))) ?>
          </span>
        </p>
        <button id="talkroom-description-close-btn" class="close-btn" title="一部を表示">一部を表示</button>
        <div class="more" id="read_more_btn">
          <div class="more-separater">&nbsp;</div>
          <button class="unset more-text" style="font-weight: bold; color: #111;" title="もっと見る">…もっと見る</button>
        </div>
      </div>
    <?php endif ?>

    <!-- <hr class="hr-bottom" style="padding: 8px 0; margin-top: 0;"> -->

    <section class="open-btn sp-btn" style="padding: 1rem;">
      <?php if ($oc['url']) : ?>
        <a href="<?php echo AppConfig::LINE_APP_URL . $oc['url'] . AppConfig::LINE_APP_SUFFIX ?>" class="openchat_link">
          <?php if ($oc['join_method_type'] !== 0) : ?>
            <svg style="height: 12px; fill: white; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 489.4 489.4" xml:space="preserve">
              <path d="M99 147v51.1h-3.4c-21.4 0-38.8 17.4-38.8 38.8v213.7c0 21.4 17.4 38.8 38.8 38.8h298.2c21.4 0 38.8-17.4 38.8-38.8V236.8c0-21.4-17.4-38.8-38.8-38.8h-1v-51.1C392.8 65.9 326.9 0 245.9 0 164.9.1 99 66 99 147m168.7 206.2c-3 2.2-3.8 4.3-3.8 7.8.1 15.7.1 31.3.1 47 .3 6.5-3 12.9-8.8 15.8-13.7 7-27.4-2.8-27.4-15.8v-.1c0-15.7 0-31.4.1-47.1 0-3.2-.7-5.3-3.5-7.4-14.2-10.5-18.9-28.4-11.8-44.1 6.9-15.3 23.8-24.3 39.7-21.1 17.7 3.6 30 17.8 30.2 35.5 0 12.3-4.9 22.3-14.8 29.5M163.3 147c0-45.6 37.1-82.6 82.6-82.6 45.6 0 82.6 37.1 82.6 82.6v51.1H163.3z" />
            </svg>
          <?php endif ?>
          <span class="text">LINEで開く</span>
          <?php if ($oc['join_method_type'] === 1) : ?>
            <span style="font-size: 12px; margin-left: 4px; font-weight: normal;" class="text">承認制</span>
          <?php endif ?>
          <?php if ($oc['join_method_type'] === 2) : ?>
            <span style="font-size: 12px; margin-left: 4px; font-weight: normal;" class="text">参加コード入力制</span>
          <?php endif ?>
        </a>
      <?php endif ?>
    </section>
    <hr class="hr-bottom">

    <?php viewComponent('ads/google-rectangle') ?>

    <?php if ($recommend[0] || $recommend[3]) : ?>
      <aside class="recommend-list-aside">
        <?php viewComponent('recommend_list2', ['recommend' => $recommend[0] ?: $recommend[3], 'member' => $oc['member'], 'tag' => $recommend[2], 'id' => $oc['id']]) ?>
      </aside>
      <hr class="hr-bottom">
      <?php viewComponent('ads/google-rectangle') ?>
    <?php endif ?>

    <?php if (isset($_adminDto)) : ?>
      <?php viewComponent('oc_content_admin', compact('_adminDto')); ?>
    <?php endif ?>

    <!-- <hr class="hr-top"> -->
    <section class="openchat-graph-section">
      <!-- <section class="open-btn sp-btn" style="padding: 0 0 1rem 0;">
        <?php if ($oc['url']) : ?>
          <a href="<?php echo AppConfig::LINE_APP_URL . $oc['url'] . AppConfig::LINE_APP_SUFFIX ?>" class="openchat_link">
            <?php if ($oc['join_method_type'] !== 0) : ?>
              <svg style="height: 12px; fill: white; margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 489.4 489.4" xml:space="preserve">
                <path d="M99 147v51.1h-3.4c-21.4 0-38.8 17.4-38.8 38.8v213.7c0 21.4 17.4 38.8 38.8 38.8h298.2c21.4 0 38.8-17.4 38.8-38.8V236.8c0-21.4-17.4-38.8-38.8-38.8h-1v-51.1C392.8 65.9 326.9 0 245.9 0 164.9.1 99 66 99 147m168.7 206.2c-3 2.2-3.8 4.3-3.8 7.8.1 15.7.1 31.3.1 47 .3 6.5-3 12.9-8.8 15.8-13.7 7-27.4-2.8-27.4-15.8v-.1c0-15.7 0-31.4.1-47.1 0-3.2-.7-5.3-3.5-7.4-14.2-10.5-18.9-28.4-11.8-44.1 6.9-15.3 23.8-24.3 39.7-21.1 17.7 3.6 30 17.8 30.2 35.5 0 12.3-4.9 22.3-14.8 29.5M163.3 147c0-45.6 37.1-82.6 82.6-82.6 45.6 0 82.6 37.1 82.6 82.6v51.1H163.3z" />
              </svg>
            <?php endif ?>
            <span class="text">LINEで開く</span>
            <?php if ($oc['join_method_type'] === 1) : ?>
              <span style="font-size: 12px; margin-left: 4px; font-weight: normal;" class="text">承認制</span>
            <?php endif ?>
            <?php if ($oc['join_method_type'] === 2) : ?>
              <span style="font-size: 12px; margin-left: 4px; font-weight: normal;" class="text">参加コード入力制</span>
            <?php endif ?>
          </a>
        <?php endif ?>
      </section>
      <hr class="hr-bottom"> -->

      <div class="title-bar">
        <img class="openchat-item-title-img" aria-hidden="true" alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
        <div>
          <h2 class="graph-title">
            <div>メンバー数の推移グラフ</div>
          </h2>
          <div class="title-bar-oc-name"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></div>
        </div>
        <span class="number-box created-at">
          <div class="openchat-itme-stats-title">登録:&nbsp;</div>
          <div class="openchat-itme-stats-title"><?php echo convertDatetime($oc['created_at']) ?></div>
        </span>
      </div>
      <!-- グラフセクション -->
      <div style="position: relative; margin: auto; padding-bottom: 1rem" id="graph-box">
        <div class="chart-canvas-box" id="dummy-canvas"></div>
        <div id="app"></div>
      </div>

      <nav style="padding-bottom: 0;" class="oc-desc-nav <?php if (!is_int($oc['api_created_at'])) echo 'no-ranking' ?>">
        <aside class="oc-desc-nav-category" style="display: flex; align-items:center;">
          <span class="openchat-list-date" style="flex-direction: row; height: fit-content; flex-wrap: nowrap; color: #111;">
            <div style="display: flex; flex-direction: column; justify-content: flex-start; gap: 1.5rem; line-height: 1.5; height: 100%; word-break: keep-all; font-weight: bold; align-items: center;">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div>カテゴリー&nbsp;</div>
              <?php endif ?>
              <?php if (isset($recommend[2]) && $recommend[2]) : ?>
                <div>タグ&nbsp;</div>
              <?php endif ?>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 1.5rem; line-height: 1.5; height: 100%">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <a href="<?php echo url('ranking/' . $oc['category']) ?>" style="width:fit-content; color:inherit; text-wrap: wrap;"><?php echo $category ?></a>
              <?php endif ?>
              <?php if (isset($recommend[2]) && $recommend[2]) : ?>
                <a href="<?php echo url('ranking?keyword=' . urlencode('tag:' . htmlspecialchars_decode($recommend[2]))) ?>" style="width:fit-content; color:inherit; text-wrap: wrap;"><?php echo $recommend[2] ?></a>
              <?php endif ?>
            </div>
          </span>
        </aside>

        <nav class="my-list-form">
          <?php if (count($myList) - 1 < AppConfig::MY_LIST_LIMIT || isset($myList[$oc['id']])) : ?>
            <label class="checkbox-label" for="my-list-checkbox">
              <input type="checkbox" id="my-list-checkbox" <?php if (isset($myList[$oc['id']])) echo 'checked' ?>>
              <span>トップにピン留め</span>
            </label>
          <?php endif ?>
        </nav>
      </nav>

    </section>
    <hr class="hr-bottom">

    <?php viewComponent('ads/google-responsive') ?>

    <?php if ($recommend[1]) : ?>
      <aside class="recommend-list-aside">
        <?php viewComponent('recommend_list2', ['recommend' => $recommend[1], 'member' => $oc['member'], 'tag' => $recommend[2], 'id' => $oc['id']]) ?>
      </aside>
      <hr class="hr-bottom">
      <?php viewComponent('ads/google-responsive') ?>
    <?php endif ?>
    <?php if ($recommend[0] && $recommend[3]) : ?>
      <aside class="recommend-list-aside">
        <?php viewComponent('recommend_list2', ['recommend' => $recommend[3], 'member' => $oc['member'], 'tag' => $recommend[2], 'id' => $oc['id']]) ?>
      </aside>
      <hr class="hr-bottom">
      <?php viewComponent('ads/google-responsive') ?>
    <?php endif ?>
    <?php if (isset($officialDto) && $officialDto && $officialDto2) : ?>
      <aside class="recommend-list-aside">
        <?php viewComponent('recommend_list2', ['recommend' => $oc['emblem'] == 1 ? $officialDto : $officialDto2, 'id' => $oc['id']]) ?>
      </aside>
      <hr class="hr-bottom">
      <?php viewComponent('ads/google-responsive') ?>
      <aside class="recommend-list-aside">
        <?php viewComponent('recommend_list2', ['recommend' => $oc['emblem'] == 1 ? $officialDto2 : $officialDto, 'id' => $oc['id']]) ?>
      </aside>
      <hr class="hr-bottom">
      <?php viewComponent('ads/google-responsive') ?>
    <?php endif ?>

    <section class="comment-section">
      <div style="display: flex; flex-direction: row; align-items: center; gap: 6px;">
        <img class="openchat-item-title-img" aria-hidden="true" alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
        <div>
          <h2 class="graph-title">
            <div>オープンチャットについてのコメント</div>
          </h2>
          <div class="title-bar-oc-name" style="padding-right: 1.5rem;"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><?php echo $oc['name'] ?></div>
        </div>
      </div>
      <div style="margin-bottom: 1.5rem; margin-top: 13px;">
        <?php viewComponent('comment_desc') ?>
      </div>
      <div id="comment-root"></div>
    </section>

    <?php viewComponent('ads/google-responsive') ?>

    <aside class="recommend-list-aside">
      <?php viewComponent('top_ranking_comment_list_hour24', compact('dto')) ?>
    </aside>
    <hr class="hr-bottom">
    <?php viewComponent('ads/google-responsive') ?>

    <aside class="recommend-list-aside">
      <?php viewComponent('top_ranking_comment_list_hour', compact('dto')) ?>
    </aside>
    <hr class="hr-bottom">
    <?php viewComponent('ads/google-responsive') ?>

    <footer class="oc-page-footer">
      <aside class="open-btn2">
        <a href="https://openchat-jp.line.me/other/beginners_guide" class="app_link">
          <span class="text">はじめてのLINEオープンチャットガイド（LINE公式）</span>
        </a>
        <a href="https://line.me/download" class="app_link app-dl">
          <span class="text">LINEアプリをダウンロード（LINE公式）</span>
        </a>
        <a href="<?php echo url('oc/' . $oc['id'] . '/csv') ?>" class="app_link csv-dl" style="
          margin-bottom: 1rem;
          margin-top: .5rem;">
          <span class="text">人数統計CSVをダウンロード</span>
        </a>
      </aside>
      <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
      <?php viewComponent('footer_inner') ?>
    </footer>
  </article>
  <?php echo $_breadcrumbsShema ?>
  <script>
    ;
    (function() {
      // 説明文の続きを読むボタン
      const readMoreBtn = document.getElementById('read_more_btn')
      const talkroomDesc = document.getElementById('talkroom-description')
      const talkroomDescBox = document.getElementById('talkroom_description_box')

      const closeId = 'talkroom-description-close-btn'

      if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
        talkroomDescBox.classList.add('hidden')
      } else {
        const open = document.getElementById(closeId)
        const close = document.getElementById('talkroom-description-close-btn')

        readMoreBtn.style.visibility = "visible"
        talkroomDesc.addEventListener('click', (e) => e.target.id !== closeId && talkroomDescBox.classList.remove('close'))
        readMoreBtn.addEventListener('click', (e) => e.target.id !== closeId && talkroomDescBox.classList.remove('close'))
        close.addEventListener('click', () => {
          talkroomDescBox.classList.add('close')
        })
      }
    })()
  </script>
  <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
  <script type="module">
    import {
      JsonCookie
    } from '<?php echo fileUrl('/js/JsonCookie.js') ?>'

    const OPEN_CHAT_ID = <?php echo $oc['id'] ?>;
    const MY_LIST_LIMIT = <?php echo AppConfig::MY_LIST_LIMIT ?>;

    const myListCheckbox = document.getElementById('my-list-checkbox')
    const myListJsonCookie = new JsonCookie('myList')

    myListCheckbox && myListCheckbox.addEventListener('change', () => {
      const listLen = (Object.keys(myListJsonCookie.get() || {}).length)

      if (!myListCheckbox.checked) {
        // チェック解除で削除する場合
        if (listLen <= 2) {
          myListJsonCookie.remove()
        } else {
          const expiresTimestamp = myListJsonCookie.remove(OPEN_CHAT_ID)
          myListJsonCookie.set('expires', expiresTimestamp)
        }
        return
      }

      if (listLen > MY_LIST_LIMIT) {
        // リストの上限数を超えている場合
        const label = document.querySelector('.my-list-form label span')
        label.textContent = 'ピン留めの最大数を超えました。'
        label.style.color = 'Red'
        return
      }

      // リストに追加する場合
      const expiresTimestamp = myListJsonCookie.set(OPEN_CHAT_ID, 1)
      myListJsonCookie.set('expires', expiresTimestamp)
    })
  </script>
</body>

</html>