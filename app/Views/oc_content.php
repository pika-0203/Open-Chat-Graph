<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;

viewComponent('oc_head', compact('_css', '_meta')); ?>

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
      <header class="openchat-header unset">
        <div class="talkroom_banner_img_area">
          <img class="talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo imgPreviewUrl($oc['img_url']) ?>" <?php echo getImgSetErrorTag() ?>>
        </div>

        <div class="openchat-header-right">
          <a rel="external" target="_blank" href="<?php echo AppConfig::LINE_OPEN_URL . $oc['emid'] ?>" class="h1-link unset">
            <h1 class="talkroom_link_h1 unset"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><span class="name"><?php echo $oc['name'] ?></span>
              <div class="link-mark"><span class="link-title">LINEオープンチャット公式サイト</span><span class="line-link-icon"></span></div>
            </h1>
          </a>

          <div class="talkroom_description_box close" id="talkroom_description_box">
            <p class="talkroom_description" id="talkroom-description">
              <span id="talkroom-description-btn">
                <?php echo nl2brReplace($oc['description']) ?>
              </span>
              <br>
              <button id="talkroom-description-close-btn">一部を表示</button>
            </p>
            <div class="more" id="read_more_btn">
              <div class="more-separater">&nbsp;</div>
              <div class="more-text">…もっと見る</div>
            </div>
          </div>

          <div class="talkroom_number_of_members">
            <span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?></span>
          </div>
          <?php if (isset($oc['diff_member'])) : ?>
            <div class="talkroom_number_of_stats">
              <div class="number-box <?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
                <?php if ($oc['diff_member'] ?? 0 !== 0) : ?>
                  <span class="openchat-itme-stats-title">昨日</span>
                  <div>
                    <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member']) ?></span>
                    <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                  </div>
                <?php elseif ($oc['diff_member'] === 0) : ?>
                  <span class="openchat-itme-stats-title">昨日</span>
                  <span class="zero-stats">±0</span>
                <?php endif ?>
              </div>
              <div class="number-box weekly <?php echo $oc['diff_member2'] > 0 ? 'positive' : 'negative' ?>">
                <?php if ($oc['diff_member2'] ?? 0 !== 0) : ?>
                  <span class="openchat-itme-stats-title">1週間</span>
                  <div>
                    <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?></span>
                    <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%)</span>
                  </div>
                <?php elseif ($oc['diff_member2'] === 0) : ?>
                  <span class="openchat-itme-stats-title">1週間</span>
                  <span class="zero-stats">±0</span>
                <?php endif ?>
              </div>
            </div>
          <?php endif ?>
        </div>
      </header>
      <hr>
      <!-- グラフセクション -->
      <div class="chart-canvas-box" id="dummy-canvas"></div>
      <div id="app"></div>

      <footer class="unset">
        <form class="my-list-form">
          <?php if (count($myList) - 1 < AppConfig::MY_LIST_LIMIT || isset($myList[$oc['id']])) : ?>
            <label class="checkbox-label" for="my-list-checkbox">
              <input type="checkbox" id="my-list-checkbox" <?php if (isset($myList[$oc['id']])) echo 'checked' ?>>
              <span>トップにピン留め</span>
            </label>
          <?php endif ?>
        </form>
        <div>
          <span class="openchat-list-date" style="flex-direction: row;">
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 4px">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div>オプチャ作成:&nbsp;</div>
              <?php endif ?>
              <div>登録:&nbsp;</div>
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div>カテゴリー:&nbsp;</div>
              <?php endif ?>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 4px">
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div><?php echo convertDatetime($oc['api_created_at']) ?></div>
              <?php endif ?>
              <div><?php echo convertDatetime($oc['created_at']) ?></div>
              <?php if (is_int($oc['api_created_at'])) : ?>
                <div><?php echo $category ?></div>
              <?php endif ?>
            </div>
          </span>
        </div>
      </footer>

    </article>

    <div id="comment-root" style="margin: 1rem;"></div>
    <hr>
    <footer>
      <?php viewComponent('footer_inner') ?>
    </footer>
  </div>
  <script>
    ;
    (function() {
      // 説明文の続きを読むボタン
      const readMoreBtn = document.getElementById('read_more_btn')
      const talkroomDesc = document.getElementById('talkroom-description')
      const talkroomDescBox = document.getElementById('talkroom_description_box')

      if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
        talkroomDescBox.classList.add('hidden')
      } else {
        const open = document.getElementById('talkroom-description-btn')
        const close = document.getElementById('talkroom-description-close-btn')

        readMoreBtn.style.visibility = "visible"
        open.addEventListener('click', () => talkroomDescBox.classList.remove('close'))
        readMoreBtn.addEventListener('click', () => talkroomDescBox.classList.remove('close'))
        close.addEventListener('click', () => talkroomDescBox.classList.add('close'))
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