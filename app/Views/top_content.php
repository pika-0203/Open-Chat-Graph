<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <header>
            <span class="main-header-title">OPENCHAT GRAPH</span>
            <span class="main-header-title-desc">メンバー数の統計をグラフで分析</span>
        </header>
        <!-- 送信後のレスポンス -->
        <?php if (session()->has('id')) : ?>
            <!-- 登録完了 or 登録済 -->
            <hr style="background-color: rgb(239, 243, 244); margin: 0rem -1rem 0rem -1rem;">
            <sup class="add-openchat-message success" id="add-openchat-describedby"><?php echo h(session('message')) ?></sup>
            <div class="openchat-item add-openchat-form-item">
                <a class="link-overlay unset" href="<?php echo url('/oc/' . $requestOpenChat['id']) ?>" tabindex="-1"></a>
                <img alt class="openchat-item-img" src="<?php echo imgPreviewUrl($requestOpenChat['img_url']) ?>">
                <h2 class="unset">
                    <a class="openchat-item-title unset" href="<?php echo url('/oc/' . $requestOpenChat['id']) ?>"><?php echo $requestOpenChat['name'] ?></a>
                </h2>
                <p class="openchat-item-desc unset"><?php echo $requestOpenChat['description'] ?></p>
                <footer class="openchat-item-lower unset">
                    <span>メンバー <?php echo $requestOpenChat['member'] ?></span>
                </footer>
            </div>
            <hr style="background-color: rgb(239, 243, 244); margin: 0rem -1rem 0rem -1rem;">
        <?php elseif (session()->has('message')) : ?>
            <!-- 無効なURLの場合 -->
            <hr style="background-color: rgb(239, 243, 244); margin: 0rem -1rem 0rem -1rem;">
            <div style="font-size: 17px; margin: 1rem;" class="add-openchat-message false" id="add-openchat-describedby"><?php echo h(session('message')) ?></div>
            <hr style="background-color: rgb(239, 243, 244); margin: 0rem -1rem 0rem -1rem;">
        <?php endif ?>
        <?php foreach (session()->getError() as $error) : ?>
            <!-- その他エラーメッセージ -->
            <hr style="background-color: rgb(239, 243, 244); margin: 0rem -1rem 0rem -1rem;">
            <div style="font-size: 17px; margin: 1rem;" class="add-openchat-message false" id="add-openchat-describedby"><?php echo h($error['message']) ?></div>
            <hr style="background-color: rgb(239, 243, 244); margin: 0rem -1rem 0rem -1rem;">
        <?php endforeach ?>
        <hr>
        <?php if ($myList) : ?>
            <article class="top-mylist">
                <?php viewComponent('open_chat_list', ['openChatList' => $myList, 'localUrl' => true]) ?>
            </article>
            <hr style="background-color: rgb(239, 243, 244); margin: 2rem -1rem 1rem -1rem;">
        <?php endif ?>

        <article class="top-ranking" style="margin-bottom: -1rem;">
            <header class="openchat-list-title-area unset">
                <a class="openchat-list-date unset ranking-url" href="<?php echo url('ranking') ?>">
                    <h2 class="unset">
                        <span class="openchat-list-title">ランキング</span>
                    </h2>
                    <div class="refresh-time">
                        <span class="count"><?php echo number_format($recordCount) ?> 件</span>
                        <div class="refresh-icon"></div>
                        <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo convertDatetime($updatedAt, true) ?></time>
                    </div>
                </a>
            </header>
            <nav class="list-btn-nav" id="list-btn-nav">
                <button class="list-btn unset" id="btn-daily" disabled>
                    <div class="btn-text">
                        <span>前日比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="list-btn unset" id="btn-weekly">
                    <div class="btn-text">
                        <span>前週比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
            </nav>
            <div id="list-daily" class="">
                <?php viewComponent('open_chat_list', ['openChatList' => $openChatList, 'isDaily' => true, 'localUrl' => true]) ?>
            </div>
            <div id="list-weekly" class="disabledList">
                <?php viewComponent('open_chat_list', ['openChatList' => $pastWeekOpenChatList, 'isDaily' => false, 'localUrl' => true]) ?>
            </div>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">詳しく見る</span>
            </a>
        </article>
        <hr>
        <hr>
        <p>
            <small style="color:#000; margin-bottom:1rem; display: block;">オプチャグラフは、LINEオープンチャット公式サイトのランキングに掲載中のオープンチャットを収集して自動登録します。</small>
            <small style="color:#000; margin-bottom:1rem; display: block;">ランキング未掲載のオープンチャットを手動で登録する場合は、登録したいオープンチャットが表示されるLINEオープンチャット公式サイトのURLを登録してください。</small>
        </p>
        <form class="add-openchat-form unset" id="add-openchat-form" action="/oc" method="POST">
            <label for="add-openchat-input-url">オープンチャットを登録する</label>
            <input name="url" id="add-openchat-input-url" placeholder="LINEオープンチャット公式サイトのURL" spellcheck="false" type="text" aria-describedby="add-openchat-describedby" autocomplete="off">
            <span class="add-openchat-message" id="add-openchat-describedby">正しいURLを入力してください</span>
            <button type="submit" name="submit" class="ellipse-btn add-openchat" disabled>登録する</button>
        </form>
        <p>
            <small style="color:#000">LINEオープンチャット公式サイトで掲載が終了したオープンチャットは、オプチャグラフから削除されます。</small>
            <br>
            <small><a href="https://openchat.line.me/jp/explore" rel="external" target="_blank">LINEオープンチャット公式サイト</a><span class="line-link-icon"></span></small>
        </p>
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
    <script>
        ;
        (function() {
            const buttons = document.getElementById('list-btn-nav').querySelectorAll('.list-btn')
            const btnDaily = document.getElementById('btn-daily')
            const btnWeekly = document.getElementById('btn-weekly')
            const listDaily = document.getElementById('list-daily')
            const listWeekly = document.getElementById('list-weekly')
            const dis = 'disabledList'
            const rankingUrlToggle = (path) => document.querySelectorAll('.ranking-url').forEach(el => el.setAttribute('href', path))

            btnDaily.addEventListener('click', e => {
                listDaily.classList.remove(dis)
                listWeekly.classList.add(dis)
                btnDaily.disabled = true
                btnWeekly.disabled = false
                rankingUrlToggle('/ranking')
                history.replaceState('', '', '/')
            })

            const changeList = () => {
                listDaily.classList.add(dis)
                listWeekly.classList.remove(dis)
                btnDaily.disabled = false
                btnWeekly.disabled = true
                rankingUrlToggle('/ranking?list=weekly')
                history.replaceState('', '', '/?l=w')
            }

            btnWeekly.addEventListener('click', changeList)

            const urlParams = new URLSearchParams(window.location.search)
            if (urlParams.has('l') && urlParams.get('l') === 'w') {
                changeList()
            }
        })()
    </script>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>