<!DOCTYPE html>
<html lang="ja">
<?php

use Shadow\Kernel\Reception as R;

viewComponent('head', compact('_css', '_meta')) ?>

<body class="body">
    <style>
        hr {
            border-bottom: solid 1px var(--border-color);
            margin: 12px 0;
        }

        .list-title {
            color: #111;
            all: unset;
            font-size: 20px;
            font-weight: bold;
        }

        .page-select {
            margin-top: 1.75rem;
            padding-bottom: 0.85rem;
        }

        .p-small {
            font-size: 13px;
            color: #777;

        }

        .recommend-desc {
            font-size: 14px;
        }

        .openchat-item-lower {
            line-height: 1.2rem;
        }

        .member-count::before {
            content: '';
            position: absolute;
            top: 7.5px;
            right: -7px;
            width: 2px;
            height: 2px;
            background-color: #aaa;
        }

        .member-count {
            margin-right: 10px;
            position: relative;
        }
    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header', compact('_updatedAt')) ?>
    <main class="ranking-page-main" style="margin-top: 8px; padding-top: 0;">
        <article>
            <header class="openchat-list-title-area unset">
                <div style="flex-direction: column;">
                    <h2 class="list-title">
                        オプチャ公式ランキング掲載の分析
                    </h2>
                    <p>
                        オプチャ公式ランキングへの掲載・未掲載の状況と、ルーム内容の更新履歴を一覧表示します。
                    </p>
                </div>
            </header>
            <aside class="list-aside ranking-desc" style="margin: 1rem 0;">
                <details class="icon-desc">
                    <summary>分析機能の説明と使い方</summary>
                    <ul style="padding-left: 1.25rem;">
                        <li>
                            <p class="recommend-desc">
                                「ルーム内容の変更:　あり・なし」は、ランキング未掲載になった理由が、ルーム管理者によるルーム内容の変更によるものか、それ以外の理由かを選択し、別けて表示します。
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                「掲載状況: 再掲載済み」を選択している場合、ランキング掲載中のルームが未掲載に変わり、再び掲載中になったルームの一覧を表示します。<br>
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                再掲載済み一覧の場合、「〇〇時間」には、未掲載の状態から、どのぐらいで再び掲載されたが表示されています。
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                「掲載状況: 再掲載済み」と「ルーム内容の変更: あり」を選択している場合、ルーム管理者がルームの設定を変更して、一旦ランキング・検索に載らなくなり、その後また載るようになったルームの一覧が表示されます。<br>
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                「最終ランキング順位」はランキング順位の上位何％まで表示するかが選べます。<br>
                                この分析機能では、単純なランク圏外ではなく、ルーム内容変更による未掲載や、それ以外の理由で未掲載となったルームに焦点を当てることができます。<br>
                                表示対象を上位に絞ることで、例外的な未掲載にはどのような特徴があるのかが分かりやすくなります。
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                「掲載状況: 未掲載」を選択している場合、ランキング掲載中のルームが未掲載に変わり、現在も未掲載の状態が続いているルームの一覧を表示します。<br>
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                未掲載一覧の場合、「〇〇時間前」には、載らなくなってから、今までどのぐらい経過したかが表示されています。
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                「掲載状況: 未掲載」・「ルーム内容の変更: なし」・「最終ランキング順位: 下位50%以下」を選択している場合、ルームの内容変更・活動量以外の理由で未掲載となった可能性が高いルームの一覧になります。<br>
                            </p>
                        </li>
                        <li>
                            <p class="recommend-desc">
                                メンバー数の表示は、ランキング未掲載になった時点のメンバー数です。<br>メンバー数の隣にあるカッコに括られた数字は、ランキング未掲載になった時点のメンバー数と、いま現在のメンバー数の差です。<br>順位の％は、そのルームの平均的な順位（同一カテゴリー内でのランキング順位）です。
                            </p>
                        </li>
                    </ul>
                </details>
            </aside>
            <aside class="list-aside ranking-desc" style="margin: 1rem 0;">
                <details class="icon-desc">
                    <summary>オプチャ公式ランキングの掲載を分析する考え方</summary>
                    <p class="recommend-desc">
                        公式の検索機能で検索できないルーム(検索落ち)は、ランキングにも未掲載と考えられます。
                    </p>
                    <p class="recommend-desc">
                        例外として、年齢認証されていない端末による検索・WEB版による検索は、強いフィルターがかかるため、これが当てはまらない場合があります。
                        <br>また、年齢認証済であっても、特別に検索ができないキーワードなどがあります。
                    </p>
                    <p class="recommend-desc">
                        端末により検索結果が変わるという公式見解がありますが、それによってフィルターがかかり検索非表示になるというのは、例外と考えることができます。
                    </p>
                    <p class="recommend-desc">
                        これらを踏まえて、ランキング掲載・検索機能(年齢認証有り)・検索機能(年齢認証無し・WEB版)はそれぞれ別々のロジックですが、一部共通する部分があると考える事ができます。
                    </p>
                    <p class="recommend-desc">
                        例外を除くと、例えば、普段はランキングに掲載されていて、検索が可能なルームの場合、そのルームの設定を変更した後に再びランキングに掲載された時が、同時に検索も可能になる時と考えることができます。
                    </p>
                    <p class="recommend-desc">
                        なお、ランキングの掲載が途切れる原因の半数以上は、単純に活動量が低く圏外になるためです。また、例外的にランキングから除外されていると考えられるルームでも、検索は可能なパターンがあります。
                    </p>
                </details>
            </aside>
            <form id="value-form" style="position: relative;">
                <label for="pet-select0">掲載状況:</label>
                <select id="pet-select0" name="publish">
                    <option value="0" <?php if (R::input('publish') === 0) echo 'selected' ?>>再掲載済み</option>
                    <option value="1" <?php if (R::input('publish') === 1) echo 'selected' ?>>現在未掲載</option>
                </select>
                <label for="pet-select1">ルーム内容の変更:</label>
                <select id="pet-select1" name="change">
                    <option value="0" <?php if (R::input('change') === 0) echo 'selected' ?>>あり</option>
                    <option value="1" <?php if (R::input('change') === 1) echo 'selected' ?>>なし</option>
                </select>
                <label for="pet-select2">最終ランキング順位:</label>
                <select id="pet-select2" name="percent">
                    <option value="50" <?php if (R::input('percent') === 50) echo 'selected' ?>>下位50%以下を除く</option>
                    <option value="80" <?php if (R::input('percent') === 80) echo 'selected' ?>>下位20%以下を除く</option>
                    <option value="100" <?php if (R::input('percent') === 100) echo 'selected' ?>>すべて表示</option>
                </select>
                <label for="keyword">キーワードで絞り込み:</label>
                <input name="keyword" id="keyword" type="text" placeholder="ルーム名・説明文" value="<?php echo R::has('keyword') ? h(R::input('keyword')) : '' ?>">
                <button type="button" style="padding: 4px 8px; position: absolute; top: 0; right: 0; margin: 1.5rem" onclick="location.href = '<?php echo url('labs/publication-analytics') ?>'">リセット</button>
            </form>
            <!-- select要素ページネーション -->
            <?php if (isset($_select)) : ?>
                <nav class="page-select unset">
                    <form class="unset" style="width: 100%;">
                        <select id="page-selector" class="unset">
                            <?php echo $_select ?>
                        </select>
                        <label for="page-selector" class="unset"><span><?php echo $_label ?></span></label>
                    </form>
                </nav>
                <small>1ページあたり100件の表示</small>
            <?php endif ?>
            <?php if (isset($openChatList)) : ?>
                <?php viewComponent('open_chat_list_ranking_ban', compact('openChatList', '_now')) ?>
            <?php else : ?>
                <p style="text-align: center;">0件の結果</p>
            <?php endif ?>
            <!-- 次のページ・前のページボタン -->
            <?php if (isset($_pagerNavArg)) : ?>
                <?php viewComponent('pager_nav_ranking_ban', $_pagerNavArg) ?>
            <?php endif ?>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <script>
        ((form) => {
            form.querySelectorAll('select').forEach((el) => el.addEventListener('change', () => form.submit()))
        })(document.getElementById('value-form'));

        ((el) => {
            if (!el) return

            el.addEventListener('change', () => {
                el.value && (location.href = el.value)
            })
        })(document.getElementById('page-selector'));
    </script>
</body>

</html>