<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <?php statisticsComponent('site_header') ?>
    <main>
        <header>
            <span class="main-header-title">OPENCHAT GRAPH</span>
            <span class="main-header-title-desc">メンバー数の統計をグラフで分析</span>
        </header>
        <form class="add-openchat-form unset" id="add-openchat-form" action="/oc" method="POST">
            <label for="add-openchat-input-url">オープンチャットを登録する</label>
            <input name="url" id="add-openchat-input-url" placeholder="オープンチャットのURL" spellcheck="false" type="text" aria-describedby="add-openchat-describedby" autocomplete="off">
            <span class="add-openchat-message" id="add-openchat-describedby">正しいURLを入力してください</span>
            <!-- 送信後のレスポンス -->
            <?php if (session()->has('id')) : ?>
                <!-- 登録完了 or 登録済 -->
                <sup class="add-openchat-message success" id="add-openchat-describedby"><?php echo h(session('message')) ?></sup>
                <div class="openchat-item add-openchat-form-item">
                    <a class="link-overlay unset" href="<?php echo url('/oc/' . $requestOpenChat['id']) ?>" tabindex="-1"></a>
                    <img alt class="openchat-item-img" src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $requestOpenChat['img_url'] . \App\Config\AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp') ?>">
                    <h2 class="unset">
                        <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $requestOpenChat['id']) ?>"><?php echo $requestOpenChat['name'] ?></a>
                    </h2>
                    <p class="openchat-item-desc unset"><?php echo $requestOpenChat['description'] ?></p>
                    <footer class="openchat-item-lower unset">
                        <span>メンバー <?php echo $requestOpenChat['member'] ?></span>
                    </footer>
                </div>
            <?php elseif (session()->has('message')) : ?>
                <!-- 無効なURLの場合 -->
                <div class="add-openchat-message false" id="add-openchat-describedby"><?php echo h(session('message')) ?></div>
            <?php endif ?>
            <?php foreach (session()->getError() as $error) : ?>
                <!-- その他エラーメッセージ -->
                <div class="add-openchat-message false" id="add-openchat-describedby"><?php echo h($error['message']) ?></div>
            <?php endforeach ?>
            <button type="submit" name="submit" class="ellipse-btn add-openchat" disabled>統計を始める</button>
        </form>
        <hr>
        <article class="top-ranking" style="margin-bottom: -1rem;">
            <header class="openchat-list-title-area unset">
                <a class="openchat-list-date unset ranking-url" href="<?php echo url('ranking') ?>">
                    <h2 class="unset">
                        <span class="openchat-list-title">ランキング</span>
                    </h2>
                    <div class="refresh-time">
                        <div class="refresh-icon"></div>
                        <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo getDailyRankingDateTime($updatedAt) ?></time>
                    </div>
                </a>
            </header>
            <nav class="chart-btn-nav" id="chart-btn-nav">
                <button class="chart-btn unset" id="btn-daily" disabled>
                    <div class="btn-text">
                        <span>前日比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="chart-btn unset" id="btn-weekly">
                    <div class="btn-text">
                        <span>前週比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
            </nav>
            <div id="list-daily" class="">
                <?php statisticsComponent('open_chat_list', ['openChatList' => $openChatList, 'isDaily' => true]) ?>
            </div>
            <div id="list-weekly" class="disabledList">
                <?php statisticsComponent('open_chat_list', ['openChatList' => $pastWeekOpenChatList, 'isDaily' => false]) ?>
            </div>

            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">詳しく見る</span>
            </a>
        </article>
    </main>
    <footer>
        <?php statisticsComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php statisticsComponent('footer_inner') ?>
    </footer>
    <!-- フォームのJS -->
    <script type="module">
        import {
            OpenChatUrlValidator
        } from '/js/OpenChatUrlValidator.js';

        const addOpenChatForm = document.getElementById('add-openchat-form')
        const inputValidator = new OpenChatUrlValidator(addOpenChatForm)
        addOpenChatForm.addEventListener('input', () => inputValidator.handle())
        // 古いSafariの対策
        addOpenChatForm.addEventListener('submit', e => e.target.elements['submit'].disabled && e.preventDefault())
    </script>
    <script>
        const buttons = document.getElementById('chart-btn-nav').querySelectorAll('.chart-btn')
        const btnDaily = document.getElementById('btn-daily')
        const btnWeekly = document.getElementById('btn-weekly')
        const listDaily = document.getElementById('list-daily')
        const listWeekly = document.getElementById('list-weekly')
        const dis = 'disabledList'
        const rankingUrlToggle = (q = '') => document.querySelectorAll('.ranking-url').forEach(el => el.setAttribute('href', '<?php echo url('ranking') ?>' + q));

        btnDaily.addEventListener('click', e => {
            listDaily.classList.remove(dis)
            listWeekly.classList.add(dis)
            btnDaily.disabled = true
            btnWeekly.disabled = false
            rankingUrlToggle()
        });

        btnWeekly.addEventListener('click', e => {
            listDaily.classList.add(dis)
            listWeekly.classList.remove(dis)
            btnDaily.disabled = false
            btnWeekly.disabled = true
            rankingUrlToggle('?l=w')
        });
    </script>
    <script defer src="/js/site_header_footer_6.js"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>