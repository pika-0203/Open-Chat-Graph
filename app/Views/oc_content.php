<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;
use App\Services\Recommend\RecommendRankingBuilder;

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

          <?php if (isset($oc['diff_member'])) : ?>
            <div class="talkroom_number_of_stats">
              <div class="number-box <?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
                <?php if ($updatedAt->format('Y-m-d') !== date("Y-m-d", strtotime('-1day'))) : ?>
                  <span class="openchat-itme-stats-title">最終更新: <time datetime="<?php echo $updatedAt->format(\DateTime::ATOM) ?>"><?php echo $updatedAt->format('Y/m/d') ?></time></span>
                <?php elseif (($oc['diff_member'] ?? 0) !== 0) : ?>
                  <?php if (($oc['diff_member'] ?? 0) >= RecommendRankingBuilder::MIN_MEMBER_DIFF) : ?>
                    <span aria-hidden="true" style="margin-right: 2px; font-size: 9px; user-select: none;">🚀</span>
                  <?php endif ?>
                  <span class="openchat-itme-stats-title"><time datetime="<?php echo $updatedAt->format(\DateTime::ATOM) ?>">前日</time></span>
                  <div>
                    <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member']) ?>人</span>
                    <span class="openchat-item-stats" style="font-size: 10px"><?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%</span>
                  </div>
                <?php elseif ($oc['diff_member'] === 0) : ?>
                  <span class="openchat-itme-stats-title"><time datetime="<?php echo $updatedAt->format(\DateTime::ATOM) ?>">前日</time></span>
                  <span class="zero-stats">±0</span>
                <?php endif ?>
              </div>

              <div class="number-box weekly <?php echo $oc['diff_member2'] > 0 ? 'positive' : 'negative' ?>">
                <?php if ($updatedAt->format('Y-m-d') !== date("Y-m-d", strtotime('-1day'))) : ?>
                  <span class="openchat-itme-stats-title">(データ更新中)</span>
                <?php elseif ($oc['diff_member2'] ?? 0 !== 0) : ?>
                  <?php if (($oc['diff_member2'] ?? 0) >= RecommendRankingBuilder::MIN_MEMBER_DIFF) : ?>
                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                      <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                    </svg>
                  <?php endif ?>
                  <span class="openchat-itme-stats-title">1週間</span>
                  <div>
                    <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?>人</span>
                    <span class="openchat-item-stats" style="font-size: 10px"><?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%</span>
                  </div>
                <?php elseif ($oc['diff_member2'] === 0) : ?>
                  <span class="openchat-itme-stats-title">1週間</span>
                  <span class="zero-stats">±0</span>
                <?php endif ?>
              </div>
            </div>
          <?php endif ?>

          <?php if (isset($hourlyUpdatedAt) && $oc['rh_diff_member'] > 0) : ?>
            <div class="talkroom_number_of_stats" style="line-height: 135%;">
              <div class="number-box">
                <?php if ($oc['rh_diff_member'] > RecommendRankingBuilder::MIN_MEMBER_DIFF) : ?>
                  <span aria-hidden="true" style="margin-right: 2px; font-size: 9px; user-select: none;">🔥</span>
                <?php endif ?>
                <span style="font-weight: bold; margin-right: 4px" class="openchat-itme-stats-title"><time datetime="<?php echo $hourlyUpdatedAt->format(\DateTime::ATOM) ?>"><?php echo $hourlyUpdatedAt->format('H:i') ?></time></span>
                <div>
                  <span style="font-weight: bold;" class="openchat-item-stats"><?php echo signedNumF($oc['rh_diff_member']) ?>人</span>
                  <span style="font-size: 10px" class="openchat-item-stats"><?php echo signedNum(signedCeil($oc['rh_percent_increase'] * 10) / 10) ?>%</span>
                </div>
              </div>
            </div>
          <?php endif ?>

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

      <?php if (cookie()->has('admin') && cookie()->has('admin-enable')) : ?>
        <form onsubmit="return confirm('変更しますか？')" action="/admin-api" method="POST" style="margin: 1rem 0;">
          <b>タグ: <?php echo $recommend[2] ?: '無し' ?></b>
          <label>タグ変更</label>
          <input type="text" name="ocTag">
          <input type="hidden" name="ocId" value="<?php echo $oc['id'] ?>">
          <input type="submit">
        </form>
      <?php endif ?>

      <div style="display: flex; flex-direction: row; align-items: center;">
        <div aria-hidden="true" style="font-size: 13px; margin-bottom: 8px; margin-right: 4px; user-select: none;">📈</div>
        <h2 class="graph-title">メンバー数の推移グラフ</h2>
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
          <small style="display:block; margin-bottom: 2px;"><a href="<?php echo url('oc/' . $oc['id'] . '/csv') ?>">統計CSVファイル</a></small>
        </nav>
        <aside>
          <span class="openchat-list-date" style="flex-direction: row;">
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 4px; line-height: 1.5;">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div>オプチャ作成:&nbsp;</div>
              <?php endif ?>
              <div>登録:&nbsp;</div>
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div>カテゴリー:&nbsp;</div>
              <?php endif ?>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 4px; line-height: 1.5;">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div><?php echo convertDatetime($oc['api_created_at']) ?></div>
              <?php endif ?>
              <div><?php echo convertDatetime($oc['created_at']) ?></div>
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div><?php echo $category ?></div>
              <?php endif ?>
            </div>
          </span>
        </aside>
      </nav>
      <hr>
      <?php if ($recommend[0]) : ?>
        <?php viewComponent('recommend_list', ['recommend' => $recommend[0], 'member' => $oc['member'], 'tag' => $recommend[2]]) ?>
        <hr style="margin-top: 2px;">
      <?php endif ?>
      <?php if ($recommend[1]) : ?>
        <?php viewComponent('recommend_list', ['recommend' => $recommend[1], 'member' => $oc['member'], 'tag' => $recommend[2]]) ?>
      <?php endif ?>
      <hr style="margin-top: 2px;">
    </article>
    <section>
      <div style="display: flex; flex-direction: row; align-items: center;" class="openchat">
        <div aria-hidden="true" style="font-size: 13px; margin-bottom: 8px; margin-right: 4px; user-select: none;">📝</div>
        <h2 class="graph-title">オープンチャットについてのコメント</h2>
      </div>
      <div id="comment-root"></div>
    </section>
    <footer>
      <aside class="open-btn2">
        <a href="https://openchat-jp.line.me/other/beginners_guide" class="app_link">
          <span class="text">はじめてのLINEオープンチャットガイド</span>
        </a>
        <a href="https://line.me/download" class="app_link">
          <span class="text">LINEアプリをダウンロード</span>
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