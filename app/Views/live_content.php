<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body class="body">
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <main class="openchat unset hidden-graph" id="graph-area">
        <section class="unset" style="height: auto;">
            <h1 class="talkroom_link_h1 unset" id="octitle">ライブトーク利用時間分析ツール</span></h1>
            <p class="page-desc">トーク履歴からライブトークの利用時間をグラフで表示します。</p>
        </section>
        <!-- グラフセクション -->
        <div class="graph-area">
            <div class="graph-title">
                <b>ライブトーク利用時間</b>
            </div>
            <nav class="chart-btn-nav" id="chart-btn-nav">
                <button class="chart-btn unset" id="btn-week" disabled>1 週間</button>
                <button class="chart-btn unset" id="btn-month">1 ヶ月</button>
                <button class="chart-btn unset" id="btn-all">全期間</button>
            </nav>
            <div class="chart-canvas-section">
                <canvas id="openchat-statistics" aria-label="全期間のメンバー数の折れ線グラフ" role="img"></canvas>
            </div>
            <div class="chart-canvas-section-timeframe">
                <canvas id="openchat-statistics-timeframe" aria-label="時間帯別の利用時間統計グラフ" role="img"></canvas>
            </div>
            <p style="font-size: 14px; margin-top: 1.25rem">累計利用時間: <span id="totalTime"></span></p>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>合計</th>
                        <th>回数</th>
                        <th style="width: 100%;">開始したメンバー</th>
                    </tr>
                </thead>
                <tbody id="live-user-table-body">
                </tbody>
            </table>
            <br>
            <table class="live-table">
                <thead>
                    <tr>
                        <th class="table-date">日付</th>
                        <th>時間</th>
                        <th>回数</th>
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
            <time>2023/07/04</time>
            <small>公開</small>
            <br>
            <time>2023/07/05</time>
            <small>0:00〜9:59間のログが無視されるバグを修正</small>
            <br>
            <time>2023/07/06</time>
            <small>iOS版に対応</small>
            <br>
            <time>2023/07/27</time>
            <small>最初にライブトークが開かれた日からグラフが始まるように変更</small>
            <br>
            <time>2023/09/05</time>
            <small>ユーザー毎の利用時間が表示されるようになりました</small>
        </p>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="<?php echo fileUrl("/js/LiveTalkAnalyzer.js") ?>"></script>
    <script src="<?php echo fileUrl("/js/LiveTalkAnalyzerEventListener.js") ?>"></script>
    <script src="<?php echo fileUrl("/js/LiveTalkChartFactory.js") ?>"></script>
    <script src="<?php echo fileUrl("/js/LiveTalkTimeframeGraph.js") ?>"></script>
    <script>
        Chart.register(ChartDataLabels);
        Chart.register(LiveTalkChartFactory.verticalLinePlugin());

        const liveTalkAnalyzer = new LiveTalkAnalyzerEventListener(7);
        liveTalkAnalyzer.eventListener();
    </script>
</body>

</html>