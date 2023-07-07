<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <main class="openchat unset hidden-graph" id="graph-area">
        <section class="openchat-header unset">
            <h1 class="talkroom_link_h1 unset" id="octitle">ライブトーク利用時間分析ツール</span></h1>
            <p class="page-desc">トーク履歴からライブトークの利用時間をグラフで表示します。</p>
            <hr class="page-desc">
        </section>
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
                        <th class="table-user">開始したメンバー</th>
                    </tr>
                </thead>
                <tbody id="live-table-body">
                </tbody>
            </table>
        </div>
        <form>
            <legend class="small-font">テキスト形式のトーク履歴を選択</legend>
            <input id="file-input" type="file" accept="text/plain">
            <p class="form-errorMessage" id="errorMessage"></p>
            <small>端末上で読み込むため、トーク履歴が当サイトに送信されることはありません。</small>
        </form>
        <p class="page-desc small-font">iOS・Android版LINEで保存されたテキスト形式のトーク履歴に対応しています。</p>
        <p class="page-desc small-font">トーク履歴の保存方法は<a href="https://help.line.me/line/ios/sp?lang=ja&contentId=20007388" rel="external nofollow noopener">LINEヘルプ</a>を参照してください。</p>
        <p class="page-desc small-font">
            <time>2023/7/4</time>
            <small>公開</small>
            <br>
            <time>2023/7/5</time>
            <small>0:00〜9:59間のログが無視されるバグを修正</small>
            <br>
            <time>2023/7/6</time>
            <small>iOS版に対応</small>
        </p>
    </main>
    <footer>
        <?php statisticsComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php statisticsComponent('footer_inner') ?>
    </footer>
    <script src="/js/site_header_footer_6.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="/js/TalkAnalyzer_02.js"></script>
    <script src="/js/TalkGraph_05.js"></script>
    <script src="/js/LiveTalkAnalyzer_04.js"></script>
    <script>
        const liveTalkAnalyzer = new LiveTalkAnalyzer();
        liveTalkAnalyzer.eventListener();
    </script>
</body>

</html>