<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;

viewComponent('head', compact('_css', '_meta', 'noindex')); ?>

<body>
  <!-- 固定ヘッダー -->
  <?php viewComponent('site_header') ?>
  <article class="openchat unset">

    <!-- オープンチャット表示ヘッダー -->
    <header class="openchat-header description-close unset" id="openchat-header">

      <?php if (($oc['is_alive'] ?? 1) === 1) : ?>
        <a class="overlay-link-box unset" rel="external nofollow noopener" href="<?php echo AppConfig::LINE_OPEN_URL . $oc['url'] ?>">
          <div class="talkroom_banner_img_area unset">
            <img class=" talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo imgUrlLocal($oc['id'], $oc['img_url']) ?>">
          </div>
          <h1 class="talkroom_link_h1 unset"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?><span class="name"><?php echo $oc['name'] ?></span>
            <div class="link-mark"><span class="line-link-icon"> </span><span class="link-title">LINEで開く</span></div>
          </h1>
        </a>
      <?php else : ?>
        <div class="overlay-link-box unset">
          <div class="talkroom_banner_img_area unset">
            <img class=" talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo imgUrlLocal($oc['id'], $oc['img_url']) ?>">
          </div>
          <h1 class="talkroom_link_h1 unset" style="color:#cf1c1c"><?php if ($oc['emblem'] === 1) : ?><span class="super-icon sp"></span><?php elseif ($oc['emblem'] === 2) : ?><span class="super-icon official"></span><?php endif ?>【削除済み】<?php echo $oc['name'] ?></h1>
        </div>
      <?php endif ?>

      <div class="talkroom_number_of_members">
        <span class="number_of_members">メンバー <?php echo number_format($oc['member']) ?></span>
      </div>
      <div class="talkroom_description_box">
        <p id="talkroom-description" class="talkroom_description"><?php echo nl2brReplace($oc['description']) ?></p>
      </div>
      <div class="detail_bottom" id="chart-footer-nav">
        <button id="read_more_btn" class="unset" aria-label="続きを読む">
          <div class="read_more_btn_icon"></div>
          <span class="read_more_btn_text">続きを読む</span>
        </button>
        <nav class="chart-footer-nav unset">
          <a href="<?php echo url('/oc/' . $oc['id'] . '/csv') ?>" download class="chart-btn unset">
            <span>CSVダウンロード</span>
          </a>
        </nav>
        <div class="talkroom_number_of_stats">
          <div class="openchat-list-date">
            <div class="refresh-icon"></div>
            <time datetime="<?php echo dateTimeAttr($oc['updated_at']) ?>"><?php echo convertDatetime($oc['updated_at'], true) ?></time>
          </div>
          <div class="<?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
            <?php if ($oc['diff_member'] ?? 0 !== 0) : ?>
              <span class="openchat-itme-stats-title">前日比</span>
              <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member']) ?></span>
              <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
            <?php elseif ($oc['diff_member'] === 0) : ?>
              <span class="openchat-itme-stats-title">前日比</span>
              <span class="zero-stats">±0</span>
            <?php endif ?>
          </div>
          <div class="weekly <?php echo $oc['diff_member2'] > 0 ? 'positive' : 'negative' ?>">
            <?php if ($oc['diff_member2'] ?? 0 !== 0) : ?>
              <span class="openchat-itme-stats-title">前週比</span>
              <span class="openchat-item-stats"><?php echo signedNumF($oc['diff_member2']) ?></span>
              <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase2'] * 10) / 10) ?>%)</span>
            <?php elseif ($oc['diff_member2'] === 0) : ?>
              <span class="openchat-itme-stats-title">前週比</span>
              <span class="zero-stats">±0</span>
            <?php endif ?>
          </div>
        </div>
      </div>
    </header>

    <!-- グラフセクション -->
    <div class="graph-title">
      <h2>メンバー数の推移</h2>
    </div>
    <div class="chart-canvas-section">
      <canvas id="openchat-statistics" aria-label="全期間のメンバー数の折れ線グラフ" role="img"></canvas>
    </div>

    <nav class="chart-btn-nav" id="chart-btn-nav">
      <button class="chart-btn unset" id="btn-week" disabled>1 週間</button>
      <button class="chart-btn unset" id="btn-month">1 ヶ月</button>
      <button class="chart-btn unset" id="btn-all">全期間</button>
    </nav>

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
        <div style="display: flex; flex-direction: column; justify-content: space-between;">
          <?php if (is_int($oc['category'])) : ?>
            <div>カテゴリー:&nbsp;</div>
          <?php endif ?>
          <?php if (is_int($oc['api_created_at'])) : ?>
            <div>オプチャ作成:&nbsp;</div>
          <?php endif ?>
          <div>登録:&nbsp;</div>
        </div>
        <div style="display: flex; flex-direction: column; justify-content: space-between;">
          <?php if (is_int($oc['category'])) : ?>
            <div><?php echo array_search($oc['category'], AppConfig::OPEN_CHAT_CATEGORY) ?></div>
          <?php endif ?>
          <?php if (is_int($oc['api_created_at'])) : ?>
            <div><?php echo convertDatetime($oc['api_created_at']) ?></div>
          <?php endif ?>
          <div><?php echo convertDatetime($oc['created_at']) ?></div>
        </div>
      </span>

    </footer>

  </article>

  <!-- チェンジログテーブル -->
  <?php if ($archiveList) : ?>
    <?php viewComponent('admin_oc_content_change_log', compact('archiveList')) ?>
  <?php endif ?>

  <?php if (cookie()->has('labs-joincount')) : ?>
    <!-- ライブトーク利用時間分析ツールのファイル読み込みフォーム -->
    <?php viewComponent('labs_joincount_form') ?>
  <?php endif ?>

  <footer>
    <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
    <?php viewComponent('footer_inner') ?>
  </footer>

  <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
  <script>
    ;
    (function() {
      // 説明文の続きを読むボタン
      const readMoreBtn = document.getElementById('read_more_btn')
      const talkroomDesc = document.getElementById('talkroom-description')

      if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
        readMoreBtn.style.visibility = "hidden"
      } else {
        const openChatHeader = document.getElementById('openchat-header')

        readMoreBtn.addEventListener('click', () => {
          if (openChatHeader.classList.contains('description-close')) {
            openChatHeader.classList.remove('description-close')
          } else {
            window.scrollTo(0, 0);
            openChatHeader.classList.add('description-close')
          }
        })
      }

      // 変更履歴の続きを読むボタン
      const readMoreBtnChangeLog = document.getElementById('read_more_btn_changelog')
      const changeLogArea = document.getElementById('changelog-area')

      const changeArchiveList = () => {
        document.querySelectorAll('.hidden-changelog').forEach(el => {
          el.classList.toggle('show-changelog')
        })

        if (changeLogArea.classList.contains('description-close')) {
          changeLogArea.classList.remove('description-close')
          history.replaceState('', '', `${location.pathname}?archive_list=open`)
        } else {
          changeLogArea.classList.add('description-close')
          history.replaceState('', '', `${location.pathname}`)
        }
      }

      readMoreBtnChangeLog && readMoreBtnChangeLog.addEventListener('click', changeArchiveList)

      const urlParams = new URLSearchParams(window.location.search)
      if (urlParams.has('archive_list') && urlParams.get('archive_list') === 'open') {
        changeArchiveList()
      }
    })()
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  <script src="<?php echo fileUrl("/js/OpenChatChartFactory.js") ?>"></script>
  <script>
    const statisticsDate = <?php echo json_encode($statisticsData['date']) ?>;
    const statisticsMember = <?php echo json_encode($statisticsData['member']) ?>;

    let openChatChartInstance = new OpenChatChartFactory({
        date: statisticsDate,
        member: statisticsMember,
      },
      document.getElementById('openchat-statistics'),
    )

    ;
    (function() {
      const chartNavButtons = document.getElementById('chart-btn-nav').querySelectorAll('.chart-btn')
      const chartFooterNav = document.getElementById('chart-footer-nav')

      chartNavButtons.forEach(el => el.addEventListener('click', e => {
        if (e.target.id === "btn-week") {
          openChatChartInstance?.update(8)
          chartFooterNav.classList.remove("disabled-style")
        } else if (e.target.id === "btn-month") {
          openChatChartInstance?.update(31)
          chartFooterNav.classList.remove("disabled-style")
        } else if (e.target.id === "btn-all") {
          openChatChartInstance?.update(0)
          chartFooterNav.classList.add("disabled-style")
        }
        chartNavButtons.forEach(btn => btn.disabled = false)
        e.target.disabled = true
      }))
    })()
  </script>

  <?php if (cookie()->has('labs-joincount')) : ?>
    <script type="module">
      import {
        JoinCountTalkAnalyzerEventListener
      } from '<?php echo fileUrl('/js/JoinCountTalkAnalyzerEventListener.js') ?>'

      import {
        OpenChatChartFactoryWithJoinCount
      } from '<?php echo fileUrl('/js/OpenChatChartFactoryWithJoinCount.js') ?>'

      import {
        JoinCountTalkAnalyzer
      } from '<?php echo fileUrl('/js/JoinCountTalkAnalyzer.js') ?>'

      const talkAnalyzer = new JoinCountTalkAnalyzerEventListener(
        statisticsDate[statisticsDate.length - 1],
        statisticsDate[0],
        statisticsDate,
        statisticsMember,
        OpenChatChartFactoryWithJoinCount,
        JoinCountTalkAnalyzer
      )
    </script>
  <?php endif ?>

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
  <?php echo $_schema ?>
</body>

</html>