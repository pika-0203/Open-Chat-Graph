<?php

use App\Config\AppConfig;
?>

<!-- 固定ヘッダー -->
<header class="site_header">
    <div class="header_inner">
        <a id="left_btn_area" href="<?php echo url() ?>" aria-label="トップページに戻る" ontouchstart="">
            <div class="left_btn">
                <div class="return_btn"></div>
            </div>
        </a>
        <div class="header_title">
            <div class="title_container">
                <span class="title"><?php echo $oc['name'] ?></span>
                <span class="member">(<?php echo $oc['member'] ?>)</span>
            </div>
            <div class="site_title">
                <span>オープンチャット グラフ</span>
            </div>
        </div>
        <a class="right_btn_area" href="<?php echo AppConfig::LINE_OPEN_URL . $oc['url'] ?>" ontouchstart="">
            LINEで開く
        </a>
    </div>
</header>
<!-- オープンチャット表示ヘッダー -->
<div class="openchat-header description-close" id="openchat-header">
    <div class="talkroom_banner_img_area" oncontextmenu="return false;" onmousedown="return false;">
        <img class="talkroom_banner_img" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo url(AppConfig::OPENCHAT_IMG_PATH . $oc['img_url'] . '.webp') ?>">
    </div>
    <header class="talkroom_header">
        <article class="talkroom_detail">
            <div class="talkroom_detail_inner">
                <h1 class="talkroom_title" id="op_title"><?php echo $oc['name'] ?></h1>
                <div class="talkroom_rating">
                    <span class="number_of_members">メンバー <?php echo $oc['member'] ?></span>
                </div>
                <p id="talkroom-description" class="talkroom_description" oncontextmenu="return false;"><?php echo nl2brReplace($oc['description']) ?></p>
                <div class="detail_bottom">
                    <button aria-label="続きを読む" id="read_more_btn">
                        <div class="read_more_btn_icon"></div>
                    </button>
                </div>
            </div>
        </article>
    </header>
</div>
<!-- メインエリア -->
<main>
    <!-- グラフセクション -->
    <h2>メンバー数推移</h2>
    <section class="chart-canvas-section">
        <canvas id="openchat-statistics" aria-label="全期間のメンバー数の折れ線グラフ" role="img"></canvas>
    </section>
</main>
<!-- テンプレートのJS -->
<script>
    const readMoreBtn = document.getElementById('read_more_btn');
    const talkroomDesc = document.getElementById('talkroom-description');

    // 説明文のもっと見るボタンの表示・非表示処理
    if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
        // 非表示にする場合
        readMoreBtn.style.display = "none";
    } else {
        // ボタンのイベント
        readMoreBtn.addEventListener('click', (e) => {
            document.getElementById('openchat-header').classList.toggle('description-close');
            window.scrollTo(0, 0);
        });
    }

    // 戻るボタンの処理
    document.getElementById("left_btn_area").addEventListener("click", (e) => {
        e.preventDefault();
        // リファラーが自サイトの場合は履歴から戻る
        if (document.referrer.indexOf(e.currentTarget.href) !== -1) {
            history.back();
        } else {
            window.location.href = e.currentTarget.href;
        }
    });
</script>
<!-- グラフのJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    const date = <?php echo json_encode($statisticsData['date']) ?>;
    const member = <?php echo json_encode($statisticsData['member']) ?>;

    const openChatChartConfig = {
        type: 'line',
        data: {
            labels: date,
            datasets: [{
                label: 'メンバー',
                data: member,
                pointRadius: 0,
                borderColor: '#11d77b',
                fill: true,
                borderWidth: 2,
                datalabels: {
                    align: 'end',
                    anchor: 'end',
                },
            }],
        },
        options: {
            aspectRatio: 2 / 1,
            scales: {
                x: {
                    ticks: {
                        autoSkip: true,
                    },
                },
                y: {
                    grace: '1%',
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                datalabels: {
                    clip: false,
                    borderRadius: 4,
                    color: 'white',
                    backgroundColor: '#11d77b',
                    font: {
                        size: 11,
                        weight: 'bold',
                    },
                },
            },
        },
    };

    Chart.register(ChartDataLabels);

    const chart = new Chart(
        document.getElementById('openchat-statistics'),
        openChatChartConfig
    );
</script>