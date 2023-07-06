<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <main class="openchat unset hidden-graph" id="graph-area">
        <header class="openchat-header unset">
            <h1 class="talkroom_link_h1 unset" id="octitle">ライブトーク利用時間分析ツール</span></h1>
            <p class="page-desc">トーク履歴を読み込ませることで、ライブトークの利用時間をグラフと共に確認することができます。<br>iOS・Android版LINEのトーク履歴（テキストファイル）に対応しています。</p>
            <p class="page-desc">ブラウザ上で分析処理を行うので、サーバーにトーク履歴が送信されることはありません。</p>
            <p class="page-desc">
                <time>2023/7/4</time>
                <small>公開</small>
                <br>
                <time>2023/7/5</time>
                <small>0:00〜9:59間のログが無視されるバグを修正</small>
                <br>
                <time>2023/7/6</time>
                <small>iOS版に対応</small>
            </p>
        </header>
        <!-- グラフセクション -->
        <div class="graph-area">
            <div class="graph-title">
                <h2>ライブトーク利用時間</h2>
            </div>
            <div class="chart-canvas-section">
                <canvas id="openchat-statistics" aria-label="全期間のメンバー数の折れ線グラフ" role="img"></canvas>
            </div>
            <nav class="chart-btn-nav" id="chart-btn-nav">
                <button class="chart-btn unset" id="btn-week">1 週間</button>
                <button class="chart-btn unset" id="btn-month" disabled>1 ヶ月</button>
                <button class="chart-btn unset" id="btn-all">全期間</button>
            </nav>
            <table class="live-table">
                <thead>
                    <tr>
                        <th class="table-date">日付</th>
                        <th>回数</th>
                        <th>時間</th>
                        <th class="table-user">開催ユーザー</th>
                    </tr>
                </thead>
                <tbody id="live-table-body">
                </tbody>
            </table>
        </div>
        <form>
            <input id="file-input" type="file" accept="text/plain">
        </form>
    </main>
    <footer>
        <?php statisticsComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php statisticsComponent('footer_inner') ?>
    </footer>
    <script src="/js/site_header_footer_6.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="/js/TalkAnalyzer_02.js"></script>
    <script src="/js/TalkGraph_04.js"></script>
    <script src="/js/LiveTalkAnalyzer_02.js"></script>
    <script>
        const liveTalkAnalyzer = new LiveTalkAnalyzer();
        liveTalkAnalyzer.addEventListener();
    </script>
</body>

</html>