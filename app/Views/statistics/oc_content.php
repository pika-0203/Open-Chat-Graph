<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <article class="openchat unset">
        <header class="openchat-header description-close unset" id="openchat-header">
            <a class="overlay-link-box unset" rel="external nofollow noopener" href="<?php echo \App\Config\AppConfig::LINE_OPEN_URL . $oc['url'] ?>">
                <div class="talkroom_banner_img_area unset">
                    <img class=" talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PATH . $oc['img_url'] . '.webp') ?>">
                </div>
                <h1 class="talkroom_link_h1 unset"><?php echo $oc['name'] ?><span class="line-link-icon"></span></h1>
            </a>
            <div class="talkroom_number_of_members">
                <span class="number_of_members">メンバー <?php echo $oc['member'] ?></span>
            </div>
            <div class="talkroom_description_box">
                <p id="talkroom-description" class="talkroom_description"><?php echo nl2brReplace($oc['description']) ?></p>
            </div>
            <div class="detail_bottom" id="chart-footer-nav">
                <button id="read_more_btn" class="unset">
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
                        <time datetime="<?php echo dateTimeAttr($oc['updated_at']) ?>"><?php echo getDailyRankingDateTime($oc['updated_at']) ?></time>
                    </div>
                    <div class="<?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
                        <?php if ($oc['diff_member'] ?? 0 !== 0) : ?>
                            <span class="openchat-itme-stats-title">前日比</span>
                            <span class="openchat-item-stats"><?php echo signedNum($oc['diff_member']) ?></span>
                            <span class="openchat-item-stats">(<?php echo signedNum(signedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                        <?php elseif ($oc['diff_member'] === 0) : ?>
                            <span class="openchat-itme-stats-title">前日比</span>
                            <span class="zero-stats">±0</span>
                        <?php endif ?>
                    </div>
                    <div class="weekly <?php echo $oc['diff_member2'] > 0 ? 'positive' : 'negative' ?>">
                        <?php if ($oc['diff_member2'] ?? 0 !== 0) : ?>
                            <span class="openchat-itme-stats-title">前週比</span>
                            <span class="openchat-item-stats"><?php echo signedNum($oc['diff_member2']) ?></span>
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
    </article>
    <footer>
        <?php statisticsComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php statisticsComponent('footer_inner') ?>
    </footer>
    <script>
        const readMoreBtn = document.getElementById('read_more_btn');
        const talkroomDesc = document.getElementById('talkroom-description');

        if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
            readMoreBtn.style.visibility = "hidden";
        } else {
            const openChatHeader = document.getElementById('openchat-header');
            readMoreBtn.addEventListener('click', () => {
                if (openChatHeader.classList.contains('description-close')) {
                    openChatHeader.classList.remove('description-close');
                } else {
                    window.scrollTo(0, 0);
                    openChatHeader.classList.add('description-close');
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="/js/oc_page_17.js"></script>
    <script>
        const openChatChart = new OpenChatChartFactory({
                date: <?php echo json_encode($statisticsData['date']) ?>,
                member: <?php echo json_encode($statisticsData['member']) ?>,
            },
            document.getElementById('openchat-statistics'),
        );

        const buttons = document.getElementById('chart-btn-nav').querySelectorAll('.chart-btn');
        const chartFooterNav = document.getElementById('chart-footer-nav');
        buttons.forEach(el => el.addEventListener('click', e => {
            if (e.target.id === "btn-week") {
                openChatChart.update(8);
                chartFooterNav.classList.remove("disabled-style");
            } else if (e.target.id === "btn-month") {
                openChatChart.update(31);
                chartFooterNav.classList.remove("disabled-style");
            } else if (e.target.id === "btn-all") {
                openChatChart.update(0);
                chartFooterNav.classList.add("disabled-style");
            }
            buttons.forEach(btn => btn.disabled = false);
            e.target.disabled = true;
        }));
    </script>
    <script src="/js/site_header_footer_6.js"></script>
    <?php echo $_schema ?>
</body>

</html>