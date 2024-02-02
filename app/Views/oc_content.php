<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;

viewComponent('head', compact('_css', '_meta')); ?>

<body>
  <!-- 固定ヘッダー -->
  <?php viewComponent('site_header') ?>
  <article class="openchat unset">

    <!-- オープンチャット表示ヘッダー -->
    <header class="openchat-header unset">
      <div class="talkroom_banner_img_area">
        <img class="talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo imgUrl($oc['img_url']) ?>" <?php echo getImgSetErrorTag() ?>>
      </div>

      <div class="openchat-header-right">
        <a rel="external" target="_blank" href="<?php echo AppConfig::LINE_OPEN_URL . $oc['emid'] ?>" class="h1-link unset">
          <h1 class="talkroom_link_h1 unset"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><span class="name"><?php echo $oc['name'] ?></span>
            <div class="link-mark"><span class="line-link-icon"> </span><span class="link-title">LINEオープンチャット公式サイト</span></div>
          </h1>
        </a>
        <div class="talkroom_number_of_members">
          <span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?></span>
        </div>
        <div class="talkroom_description_box">
          <p id="talkroom-description" class="talkroom_description"><?php echo trimOpenChatListDesc($oc['description'], 300) ?></p>
        </div>
        <div class="openchat-list-date">
          <div class="refresh-icon"></div>
          <time datetime="<?php echo dateTimeAttr($oc['updated_at']) ?>"><?php echo convertDatetime($oc['updated_at'], true) ?></time>
        </div>
        <div class="talkroom_number_of_stats">
          <div class="number-box <?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
            <?php if ($oc['diff_member'] ?? 0 !== 0) : ?>
              <span class="openchat-itme-stats-title">前日比</span>
              <div>
                <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member']) ?></span>
                <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
              </div>
            <?php elseif ($oc['diff_member'] === 0) : ?>
              <span class="openchat-itme-stats-title">前日比</span>
              <span class="zero-stats">±0</span>
            <?php endif ?>
          </div>
          <div class="number-box weekly <?php echo $oc['diff_member2'] > 0 ? 'positive' : 'negative' ?>">
            <?php if ($oc['diff_member2'] ?? 0 !== 0) : ?>
              <span class="openchat-itme-stats-title">前週比</span>
              <div>
                <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?></span>
                <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%)</span>
              </div>
            <?php elseif ($oc['diff_member2'] === 0) : ?>
              <span class="openchat-itme-stats-title">前週比</span>
              <span class="zero-stats">±0</span>
            <?php endif ?>
          </div>
        </div>
      </div>
    </header>
    <hr>
    <!-- グラフセクション -->
    <script type="module" crossorigin src="/<?php echo getFilePath('js/chart', 'index-*.js') ?>"></script>
    <style>
      .limit-btns .MuiTab-fullWidth {
        min-width: 0;
        padding: 0;
      }

      .chart-canvas-box {
        aspect-ratio: 1.7 / 1;
        width: 100%;
        margin: 0 auto;
        padding: 0;
        user-select: none;
        -webkit-user-select: none;
      }

      @media screen and (max-width: 511px) {
        .chart-canvas-box {
          aspect-ratio: 1.2 / 1;
        }
      }

      @media screen and (max-width: 359px) {
        .chart-canvas-box {
          margin: 0 -0.75rem;
          width: calc(100% + 1.5rem);
          aspect-ratio: 1.1 / 1;
        }

        .limit-btns .MuiTab-fullWidth {
          font-size: 13px;
        }
      }
    </style>
    <div class="chart-canvas-box">
      <canvas id="chart-preact-canvas"></canvas>
    </div>
    <div id="app" data-oc-id="<?php echo $oc['id'] ?>" data-category="<?php echo $category ? $category : '' ?>"></div>
    <hr>
    <footer class="unset">
      <form class="my-list-form">
        <?php if (count($myList) - 1 < AppConfig::MY_LIST_LIMIT || isset($myList[$oc['id']])) : ?>
          <label class="checkbox-label" for="my-list-checkbox">
            <input type="checkbox" id="my-list-checkbox" <?php if (isset($myList[$oc['id']])) echo 'checked' ?>>
            <span>トップにピン留め</span>
          </label>
        <?php endif ?>
      </form>
      <span class="openchat-list-date" style="flex-direction: row;">
        <div class="inner">
          <?php if (is_int($oc['api_created_at'])) : ?>
            <div>カテゴリー:&nbsp;</div>
          <?php endif ?>
          <?php if (is_int($oc['api_created_at'])) : ?>
            <div>オプチャ作成:&nbsp;</div>
          <?php endif ?>
          <div>登録:&nbsp;</div>
        </div>
        <div style="display: flex; flex-direction: column; justify-content: space-between;">
          <?php if (is_int($oc['api_created_at'])) : ?>
            <div><?php echo $category ? $category : 'その他' ?></div>
          <?php endif ?>
          <?php if (is_int($oc['api_created_at'])) : ?>
            <div><?php echo convertDatetime($oc['api_created_at']) ?></div>
          <?php endif ?>
          <div><?php echo convertDatetime($oc['created_at']) ?></div>
        </div>
      </span>
    </footer>

  </article>

  <footer>
    <?php viewComponent('footer_inner') ?>
  </footer>

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