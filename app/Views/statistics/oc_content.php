<header class="site_header">
    <div class="header_inner">
        <a class="left_btn_area" href="/" aria-label="トップページに戻る" ontouchstart="">
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
        <a class="right_btn_area" href="<?php echo \App\Config\AppConfig::LINE_OPEN_URL . $oc['url'] ?>" ontouchstart="">
            LINEで開く
        </a>
    </div>
</header>
<div class="openchat-header description-close" id="openchat-header">
    <div class="talkroom_banner_img_area" oncontextmenu="return false;" onmousedown="return false;">
        <img class="talkroom_banner_img" alt="オープンチャット「<?php echo $oc['name'] ?>」のメイン画像" src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PATH . $oc['img_url'] . '.webp') ?>">
    </div>
    <header class="talkroom_header">
        <article class="talkroom_detail">
            <div class="talkroom_detail_inner">
                <h1 class="talkroom_title" id="op_title"><?php echo $oc['name'] ?></h1>
                <div class="talkroom_rating">
                    <span class="number_of_members">メンバー <?php echo $oc['member'] ?></span>
                </div>
                <p id="talkroom-description" class="talkroom_description" oncontextmenu="return false;">
                    <?php echo nl2br($oc['description']) ?>
                </p>
                <div class="detail_bottom">
                    <button aria-label="続きを読む" id="read_more_btn">
                        <div class="read_more_btn_icon"></div>
                    </button>
                </div>
            </div>
        </article>
    </header>
</div>
<main>
    <section class="chart-canvas-section">
        <canvas id="openchat-statistics" aria-label="全期間のメンバー数の折れ線グラフ" role="img"></canvas>
        <hr>
        <canvas id="openchat-statistics2" aria-label="過去３ヶ月のメンバー数の棒グラフ" role="img"></canvas>
        <hr>
    </section>
</main>
<script>    
    const readMoreBtn = document.getElementById('read_more_btn');
    const talkroomDesc = document.getElementById('talkroom-description');

    // 説明文のもっと見るボタンの表示・非表示処理
    if (talkroomDesc.offsetHeight >= talkroomDesc.scrollHeight) {
        // もっと見るボタンを非表示にする場合
        readMoreBtn.style.display = "none";
    } else {
        // もっと見るボタンのイベントリスナー
        readMoreBtn.addEventListener('click', (e) => {
            document.getElementById('openchat-header').classList.toggle('description-close');
            window.scrollTo(0, 0);
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
    const date = <?php echo json_encode($allStatistics['date']) ?>;
    const member = <?php echo json_encode($allStatistics['member']) ?>;
    const date2 = <?php echo json_encode($weeklyStatistics['date']) ?>;
    const member2 = <?php echo json_encode($weeklyStatistics['member']) ?>;

    const openChatChartConfig = {
        type: 'line',
        data: {
            labels: date,
            datasets: [{
                label: 'メンバー',
                data: member,
                pointRadius: 0,
                borderColor: '#6de67b',
                backgroundColor: '#f9f9f9',
                tension: 0.3,
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
            layout: {
                padding: {
                    right: 12,
                    left: 12
                }
            },
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
                datalabels: {
                    display: false,
                },
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x',
                    },
                    zoom: {
                        wheel: {
                            enabled: true,
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x',
                    },
                },
            },
        },
    };

    const openChatChartConfig2 = {
        type: 'bar',
        data: {
            labels: date2,
            datasets: [{
                label: 'メンバー数',
                data: member2,
                backgroundColor: '#11d77b',
                datalabels: {
                    align: 'center',
                    anchor: 'center',
                },
            }],
        },
        options: {
            scales: {
                x: {
                    ticks: {
                        stepSize: 1,
                    },
                },
            },
            y: {
                max: 6,
                ticks: {
                    autoSkip: false,
                },
            },
            indexAxis: 'y',
            layout: {
                padding: 0,
            },
            plugins: {
                tooltip: {
                    enabled: false
                },
                legend: {
                    display: false,
                },
                datalabels: {
                    clip: true,
                    borderRadius: 4,
                    color: 'white',
                    font: {
                        size: 11,
                        weight: 'bold',
                    },
                    formatter: Math.round,
                    padding: 3,
                },
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'y',
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
    const chart2 = new Chart(
        document.getElementById('openchat-statistics2'),
        openChatChartConfig2
    );

    chart.data.datasets.reverse();
    chart.update();
</script>