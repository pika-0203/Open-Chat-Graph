<header class="site_header">
    <div class="header_inner">
        <div class="header_site_title">
            <img src="https://openchat-review.me/assets/icon-192x192.webp" alt="">
            <h1>LINEオープンチャット グラフ</h1>
        </div>
    </div>
</header>
<main>
    <header>
        <h1>OPENCHAT GRAPH</h1>
        <p>メンバー数の変化をグラフでチェック！</p>
    </header>
    <article class="site-description">
        <details>
            <summary>
                LINEオープンチャット グラフとは？
            </summary>
            <p>メンバー数の変化をグラフで掲載しています。</p>
            <p>オプチャの成長傾向を確認し、他のオプチャとの比較も簡単にできます。</p>
            <p>トークルームの運営に必須のツールです！</p>
            <p>どなたでもオープンチャットのリンクを貼り付けて登録することが出来ます。</p>
            <p><small>こちらは個人的に開発したLINE非公式サービスです。<br>
            ご不明な事がありましたら、<a href="line://ti/g2/rLT0p-Tz19W7jxHvDDm9ECGNsyymhLQTHmmTkg">こちらのオープンチャット</a>からお尋ねください。</small></p>
        </details>
    </article>
    <section>
        <!-- オープンチャット登録フォーム -->
        <form id="add-openchat-form" action="/oc" method="POST">
            <div class="form-inner">
                <label for="add-openchat-input-url">登録はURLを貼り付けるだけ✨</label>
                <input name="url" id="add-openchat-input-url" placeholder="オープンチャットのURL" spellcheck="false" type="text" aria-describedby="add-openchat-describedby" autocomplete="off">
                <div class="add-openchat-message" id="add-openchat-describedby">正しいURLを入力してください</div>
                <!-- 送信後のレスポンス -->
                <?php if (session()->has('message') && $requestOpenChat) : ?>
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
                <button type="submit" name="submit" class="ellipse-btn add-openchat" disabled>
                    登録する
                </button>
            </div>
        </form>
    </section>
    <hr>
    <!-- オープンチャット一覧 -->
    <?php foreach ($openChatList as $oc) : ?>
        <section>
            <aside class="openchat-item">
                <a href="<?php echo url('/oc/' . $oc['id']) ?>">
                    <div class="openchat-item-img">
                        <img src="<?php echo url(\App\Config\AppConfig::OPENCHAT_IMG_PREVIEW_PATH . $oc['img_url'] . \App\Config\AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp') ?>" alt="オープンチャット「<?php echo $oc['name'] ?>」" />
                    </div>
                    <div class="openchat-item-info">
                        <span class="openchat-item-title"><?php echo $oc['name'] ?></span>
                        <span class="openchat-item-desc"><?php echo $oc['description'] ?></span>
                        <div class="openchat-item-lower">
                            <span>メンバー<?php echo $oc['member'] ?></span>
                        </div>
                    </div>
                </a>
            </aside>
        </section>
    <?php endforeach; ?>
</main>
<script>
    class OpenChatUrlValidator {
        #urlPattern = /((https?:\/\/line.me\/ti\/g2\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+)(.*)(?=\?)|(https?:\/\/line.me\/ti\/g2\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+))/g

        constructor(form) {
            this.form = form
            this.submitBtn = form.elements['submit']
            this.input = form.elements['url']
        }

        handle() {
            if (this.input.value === '') {
                this.#toggleErrorMessage(false)
                this.#toggleBtnDisabled(true)
                return
            }

            const matchURL = this.input.value.match(this.#urlPattern)
            if (matchURL) {
                this.#insertTextAndFocusOnStart(matchURL[0])
                this.#toggleErrorMessage(false)
                this.#toggleBtnDisabled(false)
            } else {
                this.#toggleErrorMessage(true)
                this.#toggleBtnDisabled(true)
            }
        }

        #toggleErrorMessage(bool) {
            const cl = this.form.classList
            if (bool) {
                !cl.contains('false') && cl.add('false')
            } else {
                cl.contains('false') && cl.remove('false')
            }
        }

        #toggleBtnDisabled(bool) {
            this.submitBtn.disabled = bool
        }

        #insertTextAndFocusOnStart(string) {
            this.input.value = string
            this.input.focus()
            this.input.setSelectionRange(1, 0)
        }
    }

    const addOpenChatForm = document.getElementById('add-openchat-form')
    const inputValidator = new OpenChatUrlValidator(addOpenChatForm)
    addOpenChatForm.addEventListener('input', () => inputValidator.handle())
    // 古いSafariの対策
    addOpenChatForm.addEventListener('submit', e => e.target.elements['submit'].disabled && e.preventDefault())
</script>