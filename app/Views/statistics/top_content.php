<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <?php statisticsComponent('site_header') ?>
    <main>
        <details>
            <summary>オプチャグラフとは？</summary>
            <div class="site-details-inner">
                <p>オープンチャットの人数を日ごとに記録して、グラフで視覚的に表示します。</p>
                <p>このツールを使えば、成長傾向を把握したり、他のオプチャと比較することが簡単になります。</p>
                <p>トークルームの運営には必須のツールです！</p>
                <p>誰でもオプチャのリンクを貼り付けて登録できます。</p>
                <p>
                    <small>ご不明な事がありましたら、<a href="https://line.me/ti/g2/rLT0p-Tz19W7jxHvDDm9ECGNsyymhLQTHmmTkg">こちらのオープンチャット</a>からお問い合わせください。</small>
                </p>
                <p>
                    <small>
                        <a href="https://github.com/pika-0203/Open-Chat-Graph" target="_blank">技術仕様 - GitHub</a>
                    </small>
                </p>
                <p>
                    <small>
                        <a href="<?php echo url('privacy') ?>">プライバシーポリシー</a>
                    </small>
                </p>
                <p>
                    <small>
                        <a href="<?php echo url('terms') ?>">利用規約</a>
                    </small>
                </p>
            </div>
        </details>
        <header>
            <span class="main-header-title">OPENCHAT GRAPH</span>
            <span class="main-header-title-desc">メンバー数の変化をグラフでチェック！</span>
        </header>
        <form id="add-openchat-form" action="/oc" method="POST">
            <label for="add-openchat-input-url">登録はURLを貼り付けるだけ✨</label>
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
            <button type="submit" name="submit" class="ellipse-btn add-openchat" disabled>登録する</button>
        </form>
        <hr>
        <p class="openchat-list-title">急上昇ランキング<small style="font-weight: normal; margin-left: 0.5rem;">(過去１週間)</small></p>
        <!-- オープンチャット一覧 -->
        <?php statisticsComponent('open_chat_list', compact('openChatList')) ?>
        <!-- 次のページ・前のページボタン -->
        <?php statisticsComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => '/ranking']) ?>
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
    <script defer src="/js/site_header_footer_3.js"></script>
</body>

</html>