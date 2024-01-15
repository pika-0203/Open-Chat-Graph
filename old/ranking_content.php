<!DOCTYPE html>
<html lang="ja">
<?php
/**
 * @var \App\Views\RankingViewDto $dto
 */
viewComponent('head', [
    '_meta' => $dto->_meta,
    '_css' => $dto->_css,
    'noindex' => $dto->noindex
])
?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date">
                    <h2 class="unset">
                        <span class="openchat-list-title">ランキング</span>
                    </h2>

                    <div class="count">
                        <?php if ($dto->disabledBtnName === 'daily') : ?>
                            <small>過去１週間で変動があったルーム (10人以上)</small>
                        <?php elseif ($dto->disabledBtnName === 'weekly') : ?>
                            <small>過去１週間以上で変動があったルーム (10人以上)</small>
                        <?php else : ?>
                            <small>アクセス可能な全てのルーム</small>
                        <?php endif ?>
                        <div style="display: flex; gap: 1rem;">
                            <?php if ($dto->disabledBtnName === 'daily') : ?>
                                <?php echo number_format($dto->rankingRowCount) ?> 件
                            <?php elseif ($dto->disabledBtnName === 'weekly') : ?>
                                <?php echo number_format($dto->pastWeekRowCount) ?> 件
                            <?php else : ?>
                                <?php echo number_format($dto->recordCount) ?> 件
                            <?php endif ?>
                            <div class="refresh-time">
                                <div class="refresh-icon"></div>
                                <time datetime="<?php echo dateTimeAttr($dto->rankingUpdatedAt) ?>"><?php echo convertDatetime($dto->rankingUpdatedAt, true) ?></time>
                            </div>
                        </div>
                    </div>

                </div>
            </header>
            <nav class="list-btn-nav" id="list-btn-nav">
                <button class="list-btn unset" id="btn-daily" <?php echo $dto->isDisabledBtn('daily') ?>>
                    <div class="btn-text">
                        <span>前日比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="list-btn unset" id="btn-weekly" <?php echo $dto->isDisabledBtn('weekly') ?>>
                    <div class="btn-text">
                        <span>前週比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="list-btn unset" id="btn-member" <?php echo $dto->isDisabledBtn('member') ?>>
                    <div class="btn-text">
                        <span>メンバー数</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
            </nav>
            <!-- select要素ページネーション -->
            <nav class="page-select unset">
                <form class="unset">
                    <select id="page-selector" class="unset">
                        <?php echo $dto->_select ?>
                    </select>
                    <label for="page-selector" class="unset"><span><?php echo $dto->_label ?></span></label>
                </form>
            </nav>
            <?php viewComponent('open_chat_list', [
                'openChatList' => $dto->openChatList,
                'isDaily' => $dto->disabledBtnName === 'daily'
            ]) ?>
            <!-- 次のページ・前のページボタン -->
            <?php viewComponent('pager_nav', [
                'pageNumber' => $dto->pageNumber,
                'maxPageNumber' => $dto->maxPageNumber,
                'path' => '/ranking' . ($dto->disabledBtnName === 'member' ? '' : ('/' . $dto->disabledBtnName))
            ]) ?>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <script>
        ;
        (function(el) {
            if (!el) return

            el.addEventListener('change', () => {
                el.value && (location.href = el.value)
            })
        })(document.getElementById('page-selector'))

        ;
        (function() {
            const buttons = document.getElementById('list-btn-nav').querySelectorAll('.list-btn')

            buttons.forEach(el => el.addEventListener('click', e => {
                if (e.target.closest("#btn-daily")) {
                    location.href = '/ranking/daily'
                } else if (e.target.closest("#btn-weekly")) {
                    location.href = '/ranking/weekly'
                } else if (e.target.closest("#btn-member")) {
                    location.href = '/ranking'
                }
            }))
        })()
    </script>
    <?php echo $dto->_schema ?>
</body>

</html>