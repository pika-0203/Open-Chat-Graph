<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<!-- TODO:CSV出力 -->

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <article class="openchat unset">
        <header class="openchat-header description-close unset" id="openchat-header">
            <a class="overlay-link-box unset" href="<?php echo \App\Config\AppConfig::LINE_OPEN_URL . $oc['url'] ?>">
                <div class="talkroom_banner_img_area unset">
                    <img class=" talkroom_banner_img" aria-hidden="true" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PATH . $oc['img_url'] . '.webp') ?>">
                </div>
                <h1 class="talkroom_link_h1 unset"><?php echo $oc['name'] ?><span class="line-link-icon"></span></h1>
            </a>
            <div class="talkroom_number_of_members <?php echo $oc['diff_member'] > 0 ? 'positive' : 'negative' ?>">
                <span class="number_of_members">メンバー <?php echo $oc['member'] ?></span>
                <span>
                    <?php if ($oc['diff_member'] ?? 0 !== 0) : ?>
                        <span class="openchat-item-stats"><?php echo signedNum($oc['diff_member']) ?></span>
                        <span class="openchat-item-stats">(<?php echo signedNum(singnedCeil($oc['percent_increase'] * 10) / 10) ?>%)</span>
                    <?php elseif ($oc['diff_member'] === 0) : ?>
                        <span class="zero-stats">±0</span>
                    <?php endif ?>
                </span>
            </div>
            <div class="talkroom_description_box">
                <p id="talkroom-description" class="talkroom_description"><?php echo nl2brReplace($oc['description']) ?></p>
            </div>
            <div class="detail_bottom">
                <button id="read_more_btn" class="unset">
                    <div class="read_more_btn_icon"></div>
                    <span class="read_more_btn_text">続きを読む</span>
                </button>
            </div>
        </header>
        <!-- グラフセクション -->
        <div class="graph-title">
            <h2>メンバー数推移</h2>
            <nav class="chart-footer-nav unset" id="chart-footer-nav">
                <button class="chart-btn unset" id="csv-dl">
                    <span>CSVファイルをダウンロード</span>
                </button>
            </nav>
        </div>
        <section class="chart-canvas-section">
            <canvas id="openchat-statistics" aria-label="全期間のメンバー数の折れ線グラフ" role="img"></canvas>
        </section>
        <nav class="chart-btn-nav" id="chart-btn-nav">
            <button class="chart-btn unset" id="btn-week" disabled>1 週間</button>
            <button class="chart-btn unset" id="btn-month">1 ヶ月</button>
            <button class="chart-btn unset" id="btn-all">全期間</button>
        </nav>
    </article>
    <?php statisticsComponent('footer') ?>
    <script>
        const readMoreBtn = document.getElementById('read_more_btn');
        const talkroomDesc = document.getElementById('talkroom-description');

        if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
            readMoreBtn.style.visibility = "hidden";
            readMoreBtn.style.minHeight = "32px";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="/js/oc_page_6.js"></script>
    <script>
        const openChatChart = new OpenChatChartFactory({
                date: <?php echo json_encode($statisticsData['date']) ?>,
                member: <?php echo json_encode($statisticsData['member']) ?>,
            },
            document.getElementById('openchat-statistics'),
            (document.getElementById('site_header').clientWidth - 70) / 2
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

        const isLoggedIn = '<?php echo \App\Services\Auth::id() ?>' !== '0';
        document.getElementById('csv-dl').addEventListener('click', () => {
            if (!isLoggedIn) {
                const modal = document.getElementById('login-modal');
                document.getElementById('login-modal').classList.add('is-login-modal-open');
                document.getElementById('login-modal-close-btn').focus();
            } else {
                location.href = '<?php echo url('/oc/' . $oc['id'] . '/csv') ?>';
            }
        })
    </script>
    <script src="/js/site_header_footer_3.js"></script>
</body>

</html>