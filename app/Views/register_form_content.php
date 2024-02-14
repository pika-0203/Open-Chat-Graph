<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <hr>
        <hr>
        <small class="top-small-desc" style="font-size: 1rem;"><b>オープンチャットを手動で登録する</b></small>

        <small class="top-small-desc" style="font-size: 14px; color: #616161">
            <ul>
                <li><a href="https://openchat.line.me/jp" rel="external" target="_blank" style="font-size: 14px; color: #616161">LINEオープンチャット公式サイト</a>を開く</li>
                <li>登録したいオープンチャットを探す</li>
                <li>ページのURLをアドレスバーからコピーする</li>
                <li>下記フォームに貼り付けて登録する</li>
            </ul>
        </small>
        <small class="top-small-desc" style="font-size: 14px; color: #616161">公式サイトに掲載されていないオープンチャットは登録できません。</small>
        </p>
        <!-- 送信後のレスポンス -->
        <?php if (session()->has('id')) : ?>
            <!-- 登録完了 or 登録済 -->
            <hr class="ht-top-header">
            <sup class="add-openchat-message success" id="add-openchat-describedby"><?php echo h(session('message')) ?></sup>
            <div class="openchat-item add-openchat-form-item">
                <a class="link-overlay unset" href="<?php echo url('/oc/' . $requestOpenChat['id']) ?>" tabindex="-1"></a>
                <img alt class="openchat-item-img" src="<?php echo imgPreviewUrl($requestOpenChat['img_url']) ?>" <?php echo getImgSetErrorTag() ?>>
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
            <hr class="ht-top-header">
            <div style="font-size: 17px; margin: 1rem;" class="add-openchat-message false" id="add-openchat-describedby"><?php echo h(session('message')) ?></div>
        <?php endif ?>
        <?php foreach (session()->getError() as $error) : ?>
            <!-- その他エラーメッセージ -->
            <hr class="ht-top-header">
            <div style="font-size: 17px; margin: 1rem;" class="add-openchat-message false" id="add-openchat-describedby"><?php echo h($error['message']) ?></div>
        <?php endforeach ?>
        <hr>
        <form class="add-openchat-form unset" id="add-openchat-form" action="/oc" method="POST">
            <label for="add-openchat-input-url">公式サイトのURL</label>
            <input name="url" id="add-openchat-input-url" placeholder="https://openchat.line.me/jp/cover/..." spellcheck="false" type="text" aria-describedby="add-openchat-describedby" autocomplete="off">
            <span class="add-openchat-message" id="add-openchat-describedby">正しいURLを入力してください</span>
            <button type="submit" name="submit" class="ellipse-btn add-openchat" disabled>登録する</button>
        </form>
        <hr>
        <hr>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script type="module">
        import {
            OpenChatUrlValidator
        } from '<?php echo fileUrl('/js/OpenChatUrlValidator.js') ?>';

        const addOpenChatForm = document.getElementById('add-openchat-form')
        const inputValidator = new OpenChatUrlValidator(addOpenChatForm)
        addOpenChatForm.addEventListener('input', () => inputValidator.handle())

        // 古いSafariの対策
        addOpenChatForm.addEventListener('submit', e => e.target.elements['submit'].disabled && e.preventDefault())
    </script>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>