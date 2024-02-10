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
            <hr class="ht-top-header">
        <?php elseif (session()->has('message')) : ?>
            <!-- 無効なURLの場合 -->
            <hr class="ht-top-header">
            <div style="font-size: 17px; margin: 1rem;" class="add-openchat-message false" id="add-openchat-describedby"><?php echo h(session('message')) ?></div>
            <hr class="ht-top-header">
        <?php endif ?>
        <?php foreach (session()->getError() as $error) : ?>
            <!-- その他エラーメッセージ -->
            <hr class="ht-top-header">
            <div style="font-size: 17px; margin: 1rem;" class="add-openchat-message false" id="add-openchat-describedby"><?php echo h($error['message']) ?></div>
            <hr class="ht-top-header">
        <?php endforeach ?>
        <hr>
        <?php if ($myList) : ?>
            <article class="top-mylist">
                <?php viewComponent('open_chat_list', ['openChatList' => $myList, 'localUrl' => true]) ?>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <article class="top-ranking" style="margin-bottom: -1rem;">
            <header class="openchat-list-title-area unset">
                <a class="openchat-list-date unset ranking-url" href="<?php echo url('ranking') ?>">
                    <h2 class="unset">
                        <span class="openchat-list-title">メンバー増加ランキング</span>
                    </h2>
                    <div class="refresh-time">
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
            <div id="list-daily">
                <?php viewComponent('open_chat_list', ['openChatList' => $openChatList, 'isDaily' => true, 'localUrl' => true]) ?>
            </div>
            <div id="list-weekly" class="disabledList">
                <?php viewComponent('open_chat_list', ['openChatList' => $pastWeekOpenChatList, 'isDaily' => false, 'localUrl' => true]) ?>
            </div>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">カテゴリーから探す</span>
            </a>
        </article>
        <p style="padding-top:2rem;">
            <small class="top-small-desc" style="display: block;">オプチャグラフは<a href="https://openchat.line.me/jp/explore?sort=RANKING" rel="external" target="_blank">公式ランキング</a>に掲載中のオープンチャットを自動的に登録して集計します。</small>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('recent') ?>">最近登録されたオープンチャット</a>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('register') ?>">オープンチャットを手動で登録する</a>
        </p>
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