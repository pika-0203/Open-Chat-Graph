<!-- 固定ヘッダー -->
<header class="site_header">
    <div class="header_inner">
<!--         <a class="unset header_site_title" href="<?php echo url() ?>">
            <img src="<?php echo url('assets/icon-192x192.webp') ?>" alt="">
            <h1>LINEオープンチャット グラフ</h1>
        </a> -->
        <form class="search-form" id="id_searchForm" method="GET" action="/">
            <label for="input-search-form">
            </label>
            <input type="text" name="" placeholder="" value="" maxlength="40" id="input-search-form">
        </form>
    </div>
</header>
<main>
    <!-- メインヘッダー -->
    <div class="main-header">
        <header>
            <span class="main-header-title">OPENCHAT GRAPH</span>
            <h2>メンバー数の変化をグラフでチェック！</h2>
        </header>
        <article class="site-description">
            <details>
                <summary>LINEオープンチャット グラフについて</summary>
                <p>メンバー数の変化をグラフで掲載しています。</p>
                <p>オプチャの成長傾向を確認し、他のオプチャとの比較も簡単にできます。</p>
                <p>トークルームの運営に必須のツールです！</p>
                <p>どなたでもオープンチャットのリンクを貼り付けて登録することが出来ます。</p>
                <p><small>こちらは個人的に開発したLINE非公式サービスです。<br>ご不明な事がありましたら、<a href="https://line.me/ti/g2/rLT0p-Tz19W7jxHvDDm9ECGNsyymhLQTHmmTkg">こちらのオープンチャット</a>からお尋ねください。</small></p>
            </details>
        </article>
        <section>
            <form id="add-openchat-form" action="/oc" method="POST">
                <div class="form-inner">
                    <h2><label for="add-openchat-input-url">登録はURLを貼り付けるだけ✨</label></h2>
                    <input name="url" id="add-openchat-input-url" placeholder="オープンチャットのURL" spellcheck="false" type="text" aria-describedby="add-openchat-describedby" autocomplete="off">
                    <div class="add-openchat-message" id="add-openchat-describedby">正しいURLを入力してください</div>
                    <!-- 送信後のレスポンス -->
                    <?php if (session()->has('id')) : ?>
                        <!-- 登録完了 or 登録済 -->
                        <sup class="add-openchat-message success" id="add-openchat-describedby"><?php h(session('message')) ?></sup>
                        <div class="openchat-item add-openchat-form-item">
                            <a href="<?php echo url('/oc/' . $requestOpenChat['id']) ?>">
                                <div class="openchat-item-img">
                                    <img src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $requestOpenChat['img_url'] . \App\Config\AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp') ?>" alt="オープンチャットのメイン画像" />
                                </div>
                                <div class="openchat-item-info">
                                    <span class="openchat-item-title"><?php echo $requestOpenChat['name'] ?></span>
                                    <span class="openchat-item-desc"><?php echo $requestOpenChat['description'] ?></span>
                                    <div class="openchat-item-lower">
                                        <span>メンバー<?php echo $requestOpenChat['member'] ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php elseif (session()->has('message')) : ?>
                        <!-- 無効なURLの場合 -->
                        <div class="add-openchat-message false" id="add-openchat-describedby"><?php h(session('message')) ?></div>
                    <?php endif ?>
                    <?php foreach (session()->getError() as $error) : ?>
                        <!-- その他エラーメッセージ -->
                        <div class="add-openchat-message false" id="add-openchat-describedby"><?php h($error['message']) ?></div>
                    <?php endforeach ?>
                    <br>
                    <button type="submit" name="submit" class="ellipse-btn add-openchat" disabled>登録する</button>
                </div>
            </form>
        </section>
    </div>
    <hr>
    <h2 class="ranking-title">急上昇ランキング</h2>
    <!-- オープンチャット一覧 -->
    <?php statisticsComponent('open_chat_list', compact('openChatList')) ?>
    <!-- 次のページ・前のページボタン -->
    <?php statisticsComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => '/ranking']) ?>
</main>
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