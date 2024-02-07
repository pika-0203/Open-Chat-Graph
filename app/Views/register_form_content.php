<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <hr>
        <hr>
        <small class="top-small-desc" style="font-size: 1rem;"><b>オープンチャットを手動で登録する</b></small>
        <small class="top-small-desc" style="font-size: 14px;">
            <ul>
                <li><a href="https://openchat.line.me/jp" rel="external" target="_blank">LINEオープンチャット公式サイト</a>を開く</li>
                <li>登録したいオープンチャットを探す</li>
                <li>ページのURLをアドレスバーからコピーする</li>
                <li>下記フォームに貼り付けて登録する</li>
            </ul>
        </small>
        <small class="top-small-desc">公式サイトに掲載されていないオープンチャットは登録できません。</small>
        </p>
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