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
        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <h2 class="unset">
                    <span class="openchat-list-title">参加人数の急上昇ランキング</span>
                    <span class="openchat-list-subtitle">(毎日更新)</span>
                </h2>
                <div class="openchat-list-date">
                    <div class="refresh-icon"></div>
                    <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo getDailyRankingDateTime($updatedAt) ?></time>
                </div>
            </header>
            <?php statisticsComponent('open_chat_list', compact('openChatList')) ?>
            <a class="top-ranking-readMore" href="<?php echo url('ranking') ?>">ランキングをすべて見る</a>
        </article>
    </main>
    <?php statisticsComponent('footer') ?>
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
    <script defer src="/js/site_header_footer_4.js"></script>
</body>

</html>