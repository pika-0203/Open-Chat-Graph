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
  <div class="body">
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <article class="openchat unset">

      <!-- オープンチャット表示ヘッダー -->
      <section class="openchat-header unset">
        <div class="talkroom_banner_img_area">
          <img class="talkroom_banner_img" aria-hidden="true" alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
        </div>

        <div class="openchat-header-right">
          <a title="<?php echo $oc['name'] ?>" rel="external" target="_blank" href="<?php echo AppConfig::LINE_OPEN_URL . $oc['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX ?>" class="h1-link unset">
            <h1 class="talkroom_link_h1 unset"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><span class="name"><?php echo $oc['name'] ?></span></h1>
            <div class="link-mark"><span class="link-title"><span aria-hidden="true" style="font-size: 10px; margin-right:2px;">🔗</span>LINEオープンチャット公式サイト</span></div>
          </a>
          <div class="talkroom_description_box close" id="talkroom_description_box">
            <p class="talkroom_description" id="talkroom-description">
              <span id="talkroom-description-btn">
                <?php echo nl2brReplace(trim(preg_replace("/(\r\n){3,}|\r{3,}|\n{3,}/", "\n\n", $oc['description']))) ?>
              </span>
            </p>
            <button id="talkroom-description-close-btn" class="close-btn" title="一部を表示">一部を表示</button>
            <div class="more" id="read_more_btn">
              <div class="more-separater">&nbsp;</div>
              <button class="unset more-text" title="もっと見る">…もっと見る</button>
            </div>
          </div>

          <div class="talkroom_number_of_members">
            <span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?>人</span>
          </div>

          <?php if (isset($_hourlyRange)) : ?>
            <div class="talkroom_number_of_stats" style="line-height: 135%;">
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
            <?php if (isset($oc['rh24_diff_member'])) : ?>
              <?php if ($oc['rh24_diff_member'] >= AppConfig::MIN_MEMBER_DIFF_H24) : ?>
                <div class="number-box bold" style="margin-right: 6px;">
                  <span aria-hidden="true" style="margin-right: 1px; font-size: 9px; user-select: none;">🚀</span>
                <?php else : ?>
                  <div class="number-box" style="margin-right: 6px;">
                  <?php endif ?>
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

                <?php if (isset($oc['diff_member2'])) : ?>
                  <div class="number-box weekly">
                    <?php if (isset($oc['diff_member2']) && $oc['diff_member2'] !== 0) : ?>
                      <span class="openchat-itme-stats-title">1週間</span>
                      <div>
                        <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?>人</span><span class="openchat-item-stats percent">(<?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%)</span>
                      </div>
                    <?php elseif (isset($oc['diff_member2']) && $oc['diff_member2'] === 0) : ?>
                      <span class="openchat-itme-stats-title">1週間</span>
                      <span class="zero-stats">±0</span>
                    <?php endif ?>
                  </div>
                <?php endif ?>
                </div>

                <section class="open-btn pc-btn">
                  <?php if ($oc['url']) : ?>
                    <a href="<?php echo AppConfig::LINE_APP_URL . $oc['url'] . AppConfig::LINE_APP_SUFFIX ?>" class="openchat_link">
                      <span class="text">LINEで開く</span>
                    </a>
                  <?php endif ?>
                </section>
      </section>
      <section class="open-btn sp-btn">
        <?php if ($oc['url']) : ?>
          <a href="<?php echo AppConfig::LINE_APP_URL . $oc['url'] . AppConfig::LINE_APP_SUFFIX ?>" class="openchat_link">
            <span class="text">LINEで開く</span>
          </a>
        <?php endif ?>
      </section>

      <?php if (isset($_adminDto)) : ?>
        <?php viewComponent('oc_content_admin', compact('_adminDto')); ?>
      <?php endif ?>

      <div style="display: flex; flex-direction: row; align-items: center;">
        <div aria-hidden="true" style="font-size: 13px; margin-bottom: 8px; margin-right: 4px; user-select: none;">📈</div>
        <h2 class="graph-title">メンバー数の推移グラフ</h2>
        <span class="number-box created-at">
          <div class="openchat-itme-stats-title">登録:&nbsp;</div>
          <div class="openchat-itme-stats-title"><?php echo convertDatetime($oc['created_at']) ?></div>
        </span>
      </div>
      <!-- グラフセクション -->
      <div style="position: relative; max-width: 680px; margin: auto;">
        <div class="chart-canvas-box" id="dummy-canvas"></div>
        <div id="app"></div>
      </div>

      <nav class="oc-desc-nav">
        <nav class="my-list-form">
          <?php if (count($myList) - 1 < AppConfig::MY_LIST_LIMIT || isset($myList[$oc['id']])) : ?>
            <label class="checkbox-label" for="my-list-checkbox">
              <input type="checkbox" id="my-list-checkbox" <?php if (isset($myList[$oc['id']])) echo 'checked' ?>>
              <span>トップにピン留め</span>
            </label>
          <?php endif ?>
        </nav>
        <aside style="display: flex; align-items:center;">
          <span class="openchat-list-date" style="flex-direction: row;">
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; line-height: 1.5;">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div>カテゴリー:&nbsp;</div>
              <?php endif ?>
              <?php if (isset($recommend[2]) && $recommend[2]) : ?>
                <div>タグ:&nbsp;</div>
              <?php endif ?>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; line-height: 1.5;">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <a href="<?php echo url('ranking/' . $oc['category']) ?>" style="width:fit-content; color:inherit; text-wrap: wrap;"><?php echo $category ?></a>
              <?php endif ?>
              <?php if (isset($recommend[2]) && $recommend[2]) : ?>
                <a href="<?php echo url('ranking?keyword=' . urlencode('tag:' . htmlspecialchars_decode($recommend[2]))) ?>" style="width:fit-content; color:inherit; text-wrap: wrap;"><?php echo RecommendUtility::extractTag($recommend[2]) ?></a>
              <?php endif ?>
            </div>
          </span>
        </aside>
      </nav>
      <hr>
      <?php if ($recommend[0]) : ?>
        <?php viewComponent('recommend_list', ['recommend' => $recommend[0], 'member' => $oc['member'], 'tag' => $recommend[2], 'id' => $oc['id']]) ?>
        <hr style="margin-top: 2px;">
      <?php endif ?>
      <section style="all: unset; display: block; margin: 0 -1rem;">
        <div style="display: flex; flex-direction: row; align-items: center;" class="openchat">
          <div aria-hidden="true" style="font-size: 13px; margin-bottom: 8px; margin-right: 4px; user-select: none;">📝</div>
          <h2 class="graph-title">オープンチャットについてのコメント</h2>
        </div>
        <?php viewComponent('comment_desc') ?>
        <div id="comment-root"></div>
      </section>
      <?php if ($recommend[1]) : ?>
        <hr style="margin-top: 2px;">
        <?php viewComponent('recommend_list', ['recommend' => $recommend[1], 'member' => $oc['member'], 'tag' => $recommend[2], 'id' => $oc['id']]) ?>
      <?php endif ?>
    </article>
    <footer>
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
  </div>
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
          window.scrollTo({
            top: 0,
          });
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