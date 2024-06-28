<?php

declare(strict_types=1);

namespace App\Views\Content\Accreditation;

use App\Config\AdminConfig;
use App\Config\AppConfig;
use App\Controllers\Pages\AccreditationController;
use App\Services\Accreditation\AccreditationUtility;
use App\Services\Accreditation\Enum\ExamType;

class AccreditationAdminViewContent
{
    const HIDDEN_USER_ID = [
        1, 2
    ];

    public string $examTypeName;
    public string $typeColor;

    function __construct(
        public AccreditationController $controller,
    ) {
        $this->examTypeName = match ($this->controller->type) {
            ExamType::Bronze => 'ブロンズ',
            ExamType::Silver => 'シルバー',
            ExamType::Gold => 'ゴールド',
        };

        $this->typeColor = match ($this->controller->type) {
            ExamType::Bronze => '#ac6b25',
            ExamType::Silver => '#808080',
            ExamType::Gold => '#e6b422',
        };
    }

    function head()
    {
        self::headComponent($this->examTypeName);
    }

    static function headComponent(?string $subTitle = null, ?bool $sakuraCss = false)
    { ?>

        <head prefix="og: http://ogp.me/ns#">
            <?php echo gTag(AppConfig::GTM_ID) ?>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>オプチャ検定<?php if ($subTitle) echo '｜' . $subTitle ?>｜問題投稿ページ</title>
            <meta name="description" content="ここから誰でもオプチャ検定に問題を投稿できます。">
            <meta property="og:locale" content="ja_JP">
            <meta property="og:url" content="<?php echo url(path()) ?>">
            <meta property="og:type" content="website">
            <meta property="og:title" content="オプチャ検定<?php if ($subTitle) echo '｜' . $subTitle ?>｜問題投稿ページ">
            <meta property="og:description" content="オプチャ検定は、LINEオープンチャットの利用に関連する知識を深める場所です。ガイドライン、ルール、管理方法などについて楽しく学ぶことができます。問題投稿ページでは誰でも問題を投稿でき、他の人が投稿した問題を閲覧できます。自分の知識を共有して、オープンチャットコミュニティに貢献できます！">
            <meta property="og:site_name" content="オプチャグラフ">
            <meta property="og:image" content="<?php echo fileUrl('assets/ogp-accreditation.png') ?>">
            <meta name="twitter:card" content="summary_large_image">
            <link rel="icon" type="image/png" href="<?php echo fileUrl('assets/study_icon.png') ?>">
            <?php if ($sakuraCss) : ?>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sakura.css/css/sakura-earthly.css" type="text/css">
            <?php else : ?>
                <link rel="stylesheet" href="<?php echo fileUrl('style/mvp.css') ?>">
            <?php endif ?>
            <link rel="canonical" href="<?php echo url(path()) ?>">
            <meta name="thumbnail" content="<?php echo fileUrl('assets/ogp-accreditation.png') ?>" />
        </head>
    <?php
    }

    function header()
    { ?>
        <header style="padding-bottom: 1rem; margin-bottom: 0rem;">
            <style>
                @media screen and (max-width: 511px) {
                    nav ul li {
                        display: list-item;
                    }

                    body header,
                    main,
                    footer {
                        padding-top: 0;
                    }

                    .header-nav-ul li {
                        margin-right: 1rem;
                    }
                }


                nav a b {
                    margin: 0;
                    padding: 4px;
                    border: 1px solid <?php echo $this->typeColor ?>;
                    font-size: 13px;
                    background-color: #fff;
                    color: #000;
                }

                hr {
                    border-bottom: 1px #efefef solid;
                    margin: 2rem 0;
                }

                section aside {
                    width: 100%;
                    margin: 1rem 0;
                }

                .header-nav-ul {
                    margin-bottom: 0;
                }

                .header-nav-ul li {
                    text-wrap: nowrap;
                    white-space: nowrap;
                    padding-bottom: 6px;
                }

                .header-nav-left {
                    margin-right: 1rem;
                    width: 100%;
                    max-width: 240px;
                    padding: 0 1rem;
                }

                .header-nav-a {
                    display: block;
                    text-decoration: none;
                }

                .header-nav-title {
                    color: #000;
                    font-weight: normal;
                    font-size: 14px;
                    text-wrap: nowrap;
                    white-space: nowrap;
                    margin-top: 10px;
                }

                .header-nav-a img {
                    margin: 0;
                }

                .nav-user-name-link {
                    font-size: 13px;
                    color: #000;
                    text-decoration: none;
                }

                .nav-li-user-name {
                    display: none;
                }

                .nav-left-user-name {
                    margin-bottom: 16px;
                    line-height: 1;
                    text-align: right;
                }

                .header-logo {
                    display: block;
                }

                @media screen and (max-width: 360px) {
                    .header-nav-title {
                        font-size: 13px;
                        text-wrap: unset;
                        white-space: unset;
                    }
                }

                @media screen and (min-width: 512px) {
                    .header-nav-left {
                        max-width: 200px;
                        margin: 0;
                    }

                    .nav-li-user-name {
                        display: inline-block;
                    }

                    .nav-left-user-name {
                        display: none;
                    }
                }
            </style>
            <nav style="margin-bottom: 0">
                <div class="header-nav-left">
                    <?php if ($this->controller->profileArray) : ?>
                        <div class="nav-left-user-name">
                            <a class="nav-user-name-link" href="./profile">
                                <?php if ($this->controller->profileArray['is_admin']) : ?>
                                    👑
                                <?php endif ?>
                                <span style="text-decoration: underline;"> <?php echo $this->controller->profileArray['name'] ?></span>
                            </a>
                        </div>
                    <?php endif ?>
                    <a class="header-nav-a" href="./home">
                        <img class="header-logo" src="<?php echo fileUrl('assets/accreditation-log.svg') ?>" />
                        <div class="header-nav-title">
                            <span>問題投稿ページ</span>
                        </div>
                    </a>
                </div>
                <ul class="header-nav-ul">
                    <?php foreach ([
                        'home' => 'ホーム',
                        'question' => '問題を投稿',
                        'user' => '投稿した問題',
                        'member' => 'メンバー',
                    ] as $key => $value) : ?>
                        <?php if ($key === 'user') : ?>
                            <?php if (
                                $this->controller->pageType === 'user'
                                && $this->controller->myId === $this->controller->currentId
                            ) : ?>
                                <li><b><?php echo $value ?></b></li>
                            <?php else : ?>
                                <li><a href="./user?id=<?php echo $this->controller->myId ?>"><?php echo $value ?></a></li>
                            <?php endif ?>
                        <?php elseif ($key !== $this->controller->pageType) : ?>
                            <li><a href="./<?php echo $key ?>"><?php echo $value ?></a></li>
                        <?php else : ?>
                            <li><b><?php echo $value ?></b></li>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if ($this->controller->profileArray) : ?>
                        <li class="nav-li-user-name">
                            <a class="nav-user-name-link" href="./profile">
                                <?php if ($this->controller->profileArray['is_admin']) : ?>
                                    👑
                                <?php endif ?>
                                <span style="text-decoration: underline;"> <?php echo $this->controller->profileArray['name'] ?></span>
                            </a>
                        </li>
                    <?php endif ?>
                </ul>
            </nav>
        </header>
    <?php
    }

    private function tabStyle()
    { ?>
        <style>
            .main-tab {
                display: flex;
                gap: 1rem;
            }

            .main-tab a i,
            .main-tab a b {
                padding: 12px 10px;
                word-break: keep-all
            }

            @media screen and (max-width: 511px) {
                .main-tab {
                    gap: 12px;
                }

                .main-tab a i,
                .main-tab a b {
                    padding: 12px 6px;
                    font-size: 14px;
                }
            }

            @media screen and (max-width: 359px) {
                .main-tab {
                    gap: 8px;
                }

                .main-tab a i,
                .main-tab a b {
                    padding: 12px 6px;
                    font-size: 14px;
                }
            }
        </style>
    <?php
    }

    function mainTab()
    { ?>
        <?php $this->tabStyle() ?>
        <div class="main-tab">
            <?php foreach ([
                ['未公開の<wbr>問題', 'unpublished'],
                ['出題中の<wbr>問題', 'published'],
                ['投稿者の<wbr>一覧', 'contributors'],
            ] as $p) : ?>
                <?php if ($this->controller->pageType === $p[1]) : ?>
                    <a><i><?php echo $p[0] ?></i></a>
                <?php else : ?>
                    <a href="./<?php echo $p[1] ?>"><b><?php echo $p[0] ?></b></a>
                <?php endif ?>
            <?php endforeach ?>
        </div>
        <?php $this->typeTab() ?>
    <?php
    }

    function typeTab()
    { ?>
        <style>
            .type-tab {
                margin: 20px 0 24px 0;
                font-size: 16px;
                font-weight: bold;
                display: flex;
                gap: 20px;
            }

            @media screen and (min-width: 512px) {
                .type-tab {
                    margin: 20px 0 3rem 0;
                    font-size: 18px;
                    font-weight: bold;
                    display: flex;
                    gap: 20px;
                }
            }
        </style>
        <div class="type-tab">
            <?php foreach ([
                'bronze' => 'ブロンズ',
                'silver' => 'シルバー',
                'gold' => 'ゴールド',
            ] as $key => $value) : ?>
                <?php if ($key !== $this->controller->type->value) : ?>
                    <?php if ($this->controller->pageType === 'user') : ?>
                        <a href="./../<?php echo $key . "/user?id=" . $this->controller->currentId ?>"><?php echo $value ?></a>
                    <?php else : ?>
                        <a href="./../<?php echo $key . "/" . $this->controller->pageType ?>"><?php echo $value ?></a>
                    <?php endif ?>
                <?php else : ?>
                    <b><?php echo $value ?></b>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    <?php
    }

    function examTitle()
    {
    ?>
        <span style="margin-left: 6px; font-size: 15px; color: <?php echo $this->typeColor ?>;"><?php echo $this->examTypeName ?></span>
    <?php
    }

    function profile(bool $currentProfile = false)
    {
        $profile = $currentProfile ? $this->controller->currentProfileArray : $this->controller->profileArray;
        if (!$profile) {
            return;
        }

    ?>
        <section style="position: relative;">
            <aside>
                <?php if (
                    in_array($this->controller->myId, AdminConfig::ACCREDITATION_TRUE_ADMIN_USER_ID)
                    && !in_array($profile['id'], AdminConfig::ACCREDITATION_TRUE_ADMIN_USER_ID)
                ) : ?>
                    <?php $this->adminProfileForm() ?>
                <?php endif ?>
                <p><small><b>ニックネーム</b><br><?php echo $profile['name'] ?></small></p>
                <?php if ($profile['url']) : ?>
                    <p>
                        <small>
                            <b>オープンチャット</b><br>
                            <a href="<?php echo $profile['url'] ?>"><?php echo $profile['room_name'] ?></a>
                        </small>
                    </p>
                <?php endif ?>
                <?php if ($profile['is_admin']) : ?>
                    <small>👑サイト管理者</small>
                <?php endif ?>
            </aside>
        </section>
    <?php
    }

    function adminProfileForm()
    {
        $profile = $this->controller->currentProfileArray;
        $isAdmin = $profile['is_admin'];
        $returnTo = "?return_to=/accreditation/{$this->controller->type->value}/user?id=" . $profile['id'];

    ?>
        <form style="all:unset; display: block; position:absolute; top:2.25rem; right:1.25rem;" onsubmit="return confirm('<?php echo $isAdmin ? '管理者を解除しますか？' : '管理者に設定しますか？' ?>')" method="POST" action="/accreditation/set-admin-permission<?php echo $returnTo ?>">
            <input type="hidden" value="<?php echo $profile['id'] ?>" name="id">
            <input type="hidden" value="<?php echo $isAdmin ? 0 : 1 ?>" name="is_admin">
            <input type="submit" value="<?php echo $isAdmin ? '管理者を解除' : '管理者に設定' ?>" style="padding: 3px 6px; font-size: 13px; margin: 0;" />
        </form>
    <?php
    }

    function contributors()
    {
    ?>
        <?php foreach ($this->controller->currentContributorsArray as $p) : ?>
            <?php if (in_array($p['id'], self::HIDDEN_USER_ID)) continue ?>
            <section>
                <aside style="margin: 0.5rem 0;">
                    <div style="margin-bottom: 12px;">
                        <div style="margin-bottom: 4px;"><small>ニックネーム</small></div>
                        <div>
                            <a style="color: #111;" href="./user?id=<?php echo $p['id'] ?>"><?php echo $p['name'] ?></a>
                        </div>
                    </div>
                    <?php if ($p['url']) : ?>
                        <div style="margin-bottom: 12px;">
                            <div style="margin-bottom: 4px;"><small>オープンチャット</small></div>
                            <div> <a style="color: #111;" href="<?php echo $p['url'] ?>"><?php echo $p['room_name'] ?></a></div>
                        </div>
                    <?php endif ?>
                    <?php if ($p['is_admin']) : ?>
                        <small>👑サイト管理者</small>
                    <?php endif ?>
                </aside>
            </section>
        <?php endforeach ?>
    <?php
    }

    function profileTerm()
    {
    ?>
        <p>
            <?php if (!$this->controller->profileArray) : ?>
                <small>プロフィールを作成するとメンバー一覧に表示されます。</small>
                <br>
            <?php endif ?>
            <small>問題が検定に出題された際は、出題者としてニックネームとオプチャ名・オプチャリンクが検定サイト上に掲載されます。</small>
        </p>
    <?php
    }

    function term()
    {
    ?>
        <p>
            <small>このサイトではオプチャ検定の問題集を管理しています。<br>LINEログインでメンバー登録を行い、誰でも問題文を投稿して作成に協力することができます。</small>
        </p>
        <?php echo $this->profileTerm() ?>
        <p>
            <small>投稿された問題は、検定に合わせるためにサイト管理者が編集する場合があります。<br>問題数が限られているので、実際に出題されるのは一部の範囲の問題となります。</small>
        </p>
    <?php
    }

    function termHome()
    {
    ?>
        <p>
            <small>このサイトではオプチャ検定の問題集を管理しています。</small>
        </p>
        <?php $this->userTerm() ?>
        <?php $this->profileTerm() ?>
    <?php
    }

    function userTerm()
    {
    ?>
        <p>
            <small>投稿された問題は、検定に合わせるためにサイト管理者が編集する場合があります。<br>問題数が限られているので、実際に出題されるのは一部の範囲の問題となります。</small>
        </p>
    <?php
    }

    function termQ()
    {
    ?>
        <p>
            <small>投稿した問題は未公開の状態でサイト上に保存されます。<br>「投稿した問題」から編集することができます。<br>サイト管理者により出題中になった場合は、実際の検定で表示される状態になります。</small>
        </p>
    <?php
    }

    function termEditor()
    {
    ?>
        <p>
            <small>サイト管理者が編集した後は、他のメンバーは編集できなくなります。</small>
        </p>
    <?php
    }

    function footer($scrollBtn = true)
    {
    ?>
        <footer>
            <?php if (in_array($this->controller->pageType, ['login', 'home'])) : ?>
                <hr style="margin-top: 0rem;">
                <p>
                    <small>このサイトはLINEオープンチャット非公式です。LINEヤフー社はこの内容に関与していません。<br>監修しているのは一部のLINEオープンチャット公認メンターです。</small>
                </p>
            <?php elseif ($scrollBtn) : ?>
                <small style="text-decoration: underline; color: #111; cursor:pointer; display:block; margin-left: auto; width:fit-content;" onclick="window.scroll({top: 0,behavior:'smooth'})">↑ 一番上までスクロール</small>
                <br>
            <?php endif ?>
            <small><a href="/accreditation/privacy" style="color: #b7b7b7; text-decoration: none;">プライバシーポリシー</a></small>
            <small><a href="/policy/term" style="color: #b7b7b7; text-decoration: none; margin-left: 1rem;">利用規約</a></small>
        </footer>
    <?php
    }

    function questionForm(string $returnTo, bool $editorMode = false)
    {
        $q = null;
        if ($editorMode && isset($this->controller->questionList[0])) {
            $q = $this->controller->questionList[0];
        }

        if ($editorMode) {
            $action = "edit-question";
            $confirm = '変更しますか？';
            $submit = '変更';
        } else {
            $action = "register-question";
            $confirm = '登録しますか？';
            $submit = '登録';
        }
    ?>
        <form style="user-select:none; margin-top: 0.5rem;" id="q-form" onsubmit="return confirm('<?php echo $confirm ?>')" id="user-form" method="POST" action="/accreditation/<?php echo $action . $returnTo ?>">
            <label for="q_text">問題文</label>
            <textarea id="q_text" name="question" maxlength="4000" rows="5" required><?php echo $q->question ?? '' ?></textarea>

            <?php foreach (range('a', 'd') as $key => $el) : ?>
                <div style="display: flex; gap: 1rem;">
                    <label for="answer_<?php echo $key ?>">回答 <?php echo strtoupper($el) ?></label>
                    <div style="user-select: none;">
                        <input id="radio_<?php echo $key ?>" type="radio" name="answers[correct]" value="<?php echo $el ?>" style="transform:scale(1.5); cursor: pointer;" required <?php if (($q->answersArray['correct'] ?? '') === $el) echo 'checked' ?>>
                        <label for="radio_<?php echo $key ?>" style="cursor: pointer;">正解</label>
                    </div>
                </div>
                <textarea id="answer_<?php echo $key ?>" name="answers[<?php echo $el ?>]" maxlength="4000" rows="3" required><?php echo $q->answersArray[$el] ?? '' ?></textarea>
            <?php endforeach ?>

            <label for="explanation">解説（必須）</label>
            <textarea id="explanation" name="explanation" maxlength="4000" rows="5" required><?php echo $q->explanationArray['explanation'] ?? '' ?></textarea>

            <fieldset>
                <legend style="font-weight: bold;">出典URL（必須）</legend>
                <p>回答の根拠になるURLを指定してください</p>
                <div style="margin-bottom: 1rem; display:flex; align-items: center">
                    <div>
                        <input id="radio_url1" type="radio" name="source_url" value="" style="transform:scale(1.5); margin-bottom: 0; cursor: pointer;" required <?php if ($q && !($q->explanationArray['source_url'] ?? '')) echo 'checked' ?>>
                        <label style="display: inline-block; user-select:none; cursor: pointer;" for="radio_url1">安心・安全ガイドライン</label>
                    </div>
                    <a style="text-wrap: nowrap; margin-left:1rem; font-size: 13px;" href="https://openchat-jp.line.me/other/guideline" target="_blank">開く↗</a>
                </div>
                <div>
                    <input id="radio_url2" type="radio" name="source_url" value="<?php if ($q && ($q->explanationArray['source_title'] ?? '')) echo $q->explanationArray['source_url'] ?? '' ?>" style="transform:scale(1.5); cursor: pointer;" required <?php if ($q && ($q->explanationArray['source_title'] ?? '')) echo 'checked' ?>>
                    <label for="radio_url2" style="cursor: pointer;">URLを入力</label>
                </div>
                <small id="url-message" style="display: none;">URLが無効です</small>
                <input style="display: block; margin-bottom: 0;" type="text" id="source_url" maxlength="4000" value="<?php if ($q && ($q->explanationArray['source_title'] ?? '')) echo $q->explanationArray['source_url'] ?? '' ?>">
                <small style="user-select: none;">URLはLINE公式関連のページを入力してください</small>
            </fieldset>

            <input type="hidden" value="<?php echo $this->controller->type->value ?>" name="type">

            <?php if ($editorMode) : ?>
                <input type="hidden" value="<?php echo $this->controller->currentId ?>" name="id">

                <?php if ($this->controller->isAdmin) : ?>
                    <br>
                    <label for="publishing">公開設定</label>
                    <select name="publishing" id="publishing" style="cursor: pointer;">
                        <option value="0" <?php if (($q->publishing ?? '') === 0) echo 'selected' ?>>未公開</option>
                        <option value="1" <?php if (($q->publishing ?? '') === 1) echo 'selected' ?>>出題中</option>
                    </select>
                    <br>
                <?php endif ?>
            <?php endif ?>

            <br>
            <small style="display:block;">個人情報の投稿は禁止です。</small>
            <input id="submit-btn" type="submit" value="<?php echo $submit ?>" />
        </form>
        <script type="module">
            import {
                UrlValidator
            } from '<?php echo fileUrl('/js/UrlValidator.js') ?>';

            const radioUrl1 = document.getElementById('radio_url1')
            const radioUrl2 = document.getElementById('radio_url2')
            const sorceUrl = document.getElementById('source_url')
            const submit = document.getElementById('submit-btn')
            const urlMessage = document.getElementById('url-message')
            const qForm = document.getElementById('q-form')

            const inputValidator = new UrlValidator(
                sorceUrl, urlMessage, submit
            )

            const radioUrlChange = () => {
                sorceUrl.required = radioUrl2.checked
                submit.disabled = radioUrl2.checked
                radioUrl2.checked && inputValidator.handle()
            }

            radioUrl1.addEventListener('change', radioUrlChange)
            radioUrl2.addEventListener('change', radioUrlChange)

            sorceUrl.addEventListener('input', () => {
                const value = sorceUrl.value.trim()
                sorceUrl.value = value
                radioUrl2.value = value
                radioUrl2.checked = true
                inputValidator.handle()
            })

            submit.addEventListener('click', () => {
                ['question', 'answer_0', 'answer_1', 'answer_2', 'answer_3', 'explanation'].map(key => {
                    qForm[key].value = qForm[key].value.trim()
                })

                if (qForm['answers[correct]'].value || !qForm['question'].value)
                    return

                qForm.scrollIntoView({
                    behavior: 'smooth'
                });
            })
        </script>
    <?php
    }

    function deleteQuestionForm(string $returnTo)
    {
        if (!isset($this->controller->questionList[0]))
            return;

        $q = $this->controller->questionList[0];
    ?>
        <form style="display:flex; flex-direction: row-reverse;" onsubmit="return confirm('削除しますか？\nこの操作は元に戻せません。')" method="POST" action="/accreditation/delete-question<?php echo $returnTo ?>">
            <input type="hidden" value="<?php echo $q->id ?>" name="id">
            <input type="submit" value="削除" style="padding: 10px 20px; background-color:#e2326b; border-color:#e2326b;" />
        </form>
    <?php
    }

    function adminQuestionForm(string $returnTo, string $deleteReturnTo)
    {
        if (!isset($this->controller->questionList[0]) || !$this->controller->isAdmin)
            return;

        $q = $this->controller->questionList[0];
    ?>
        <section>
            <aside>
                <b>サイト管理者機能</b>
                <hr>
                <form style="all:unset; display: block;" onsubmit="return confirm('検定レベルを移動しますか？')" method="POST" action="/accreditation/move-question">
                    <input type="hidden" value="<?php echo $q->id ?>" name="id">
                    <fieldset style="display: flex; gap: 1rem;">
                        <legend>検定レベルの移動</legend>
                        <select name="type" style="padding: 1rem; margin: 0; cursor: pointer;">
                            <?php foreach (ExamType::cases() as $type) : ?>
                                <?php if ($type === $this->controller->type) continue ?>
                                <option style="font-size: 21px;" value="<?php echo $type->value ?>"><?php echo $type->value ?></option>
                            <?php endforeach ?>
                        </select>
                        <br>
                        <input type="submit" value="移動する" style="padding: 10px 20px; margin: 0;" />
                    </fieldset>
                </form>
                <hr>
                <?php if (!$q->is_admin_user && $q->edit_user_id && ($q->user_id !== $q->edit_user_id)) : ?>
                    <form style="all:unset; display: block;" onsubmit="return confirm('問題編集権限を投稿者に戻しますか？')" method="POST" action="/accreditation/reset-permission-question<?php echo $returnTo ?>">
                        <input type="hidden" value="<?php echo $q->id ?>" name="id">
                        <input type="submit" value="問題編集権限を投稿者に戻す" style="padding: 10px 20px;" />
                    </form>
                <?php endif ?>
            </aside>
        </section>
    <?php
    }

    function questionList(bool $editorMode = false)
    {
        $listLen = count($this->controller->questionList);
        $shareUrl = url("accreditation?id=");

    ?>
        <section style="flex-direction: column;">
            <style>
                .question_paper {
                    margin: 0.5rem 0;
                    width: unset;
                }

                .question_p,
                .question_li span,
                .word-wrap {
                    overflow-wrap: anywhere;
                    white-space: break-spaces;
                    line-break: anywhere;
                }

                .question_p {
                    font-size: 15px;
                }

                .question_li {
                    font-size: 14px;
                    font-weight: bold;
                }

                .question_paper.published {
                    background: var(--color-secondary-accent);
                }

                .share-menu-item {
                    cursor: pointer;
                    width: 24px;
                    height: 24px;
                    display: flex;
                }

                .share-menu-icon {
                    width: 24px;
                    height: 24px;
                    margin: auto;
                    background-repeat: no-repeat;
                    background-size: contain;
                    display: block;
                }

                .share-menu-icon.copy {
                    width: 20px;
                    height: 20px;
                    margin: auto;
                }

                .share-menu-icon-twitter {
                    background-image: url(/assets/twitter_x.svg);
                    background-color: #000000;
                    border-radius: 6px;
                }

                .share-menu-icon-line {
                    background-image: url(/assets/line.svg);
                    border-radius: 6px;
                }

                .copy-btn-icon {
                    background-image: url(/assets/copy_icon_c.svg);
                }

                .question-link-wrap {
                    max-width: 712px;
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                    gap: 13px;
                    margin-top: 1rem;
                }

                .quession-link-prefix {
                    font-size: 14px;
                    user-select: none;
                    text-wrap: nowrap;
                    font-weight: bold;
                    letter-spacing: -0.3px;
                }

                .question-link {
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 2;
                    overflow: hidden;
                    word-break: break-all;
                    font-size: 14px;
                    line-height: 1.3;
                    letter-spacing: -0.3px;
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    text-decoration: unset;
                    font-weight: bold;
                    color: rgb(39, 85, 172);
                }

                @media screen and (min-width: 512px) {
                    .question-link-wrap {
                        gap: 15px;
                    }

                    .question-link {
                        line-height: 1.4;
                        font-size: 15px;
                        font-weight: bold;
                    }
                }

                .question-link-inner {
                    display: flex;
                    flex-direction: row;
                    color: var(--color-text-secondary);
                    gap: 4px;
                    align-items: stretch;
                }

                details {
                    width: 100%;
                }

                details summary {
                    width: fit-content;
                    user-select: none;
                }

                .scroll-to-top {
                    all: unset;
                    display: none;
                    position: fixed;
                    bottom: 10px;
                    right: 10px;
                    width: 36px;
                    height: 36px;
                    background-color: #fff;
                    border-radius: 6px;
                    text-align: center;
                    line-height: 48px;
                    cursor: pointer;
                    box-shadow: 0px 0px 6px -2px #777777;
                    align-items: center;
                    justify-content: center;
                }

                .source-url {
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 1;
                    overflow: hidden;
                    word-break: break-all;
                }
            </style>
            <script>
                async function copyUrl(text) {
                    try {
                        await navigator.clipboard.writeText(text)
                        alert("リンクをコピーしました");
                    } catch {
                        alert("コピーできませんでした\n(非対応ブラウザ)");
                    }
                }
            </script>
            <small style="margin-right: auto; user-select: none; font-size: 15px; color: #000; font-weight: 700;">全 <?php echo $listLen ?> 件</small>
            <?php if (!$editorMode && $listLen > 1) : ?>
                <details>
                    <summary>目次を開く</summary>
                    <div class="question-link-wrap">
                        <?php foreach ($this->controller->questionList as $el) : ?>
                            <div class="question-link-inner">
                                <div class="quession-link-prefix">・</div>
                                <a class="question-link" href="#id-<?php echo $el->id ?>"><?php echo $el->question ?></a>
                            </div>
                        <?php endforeach ?>
                    </div>
                </details>
            <?php endif ?>
            <?php foreach ($this->controller->questionList as $el) : ?>
                <?php $edit = AccreditationUtility::isQuestionEditable($el, $this->controller->myId, $this->controller->isAdmin) ?>

                <aside class="question_paper <?php if ($el->publishing) echo 'published' ?>" id="id-<?php echo $el->id ?>">
                    <div style="display: flex; justify-content: space-between;">
                        <?php if ($el->publishing) : ?>
                            <small style="display: block; font-weight: bold; font-size: 14px;">問題ID: <?php echo ($el->id) ?>（出題中）</small>
                        <?php else : ?>
                            <small style="display: block; font-size: 14px;">問題ID: <?php echo ($el->id) ?></small>
                        <?php endif ?>
                        <?php if ($edit && !$editorMode) : ?>
                            <a href="./editor?id=<?php echo $el->id ?>">編集</a>
                        <?php endif ?>
                    </div>
                    <p class="question_p"><?php echo $el->question ?></p>

                    <ol>
                        <?php foreach ([...range('a', 'd')] as $key) : ?>
                            <li type="A" class="question_li">
                                <span><?php echo $el->answersArray[$key] ?? 'Error: 配列の要素がありません' ?></span>
                            </li>
                        <?php endforeach ?>
                    </ol>

                    <p class="question_p"><b>正解: <?php echo strtoupper($el->answersArray['correct'] ?? 'Error: 配列の要素がありません') ?></b></p>
                    <p class="question_p"><?php echo $el->explanationArray['explanation'] ?? 'Error: 配列の要素がありません(explanationArray)' ?></p>

                    <?php if (!isset($el->explanationArray['source_title'], $el->explanationArray['source_url'])) : ?>
                        <p class="question_p">Error: 配列の要素がありません(explanationArray)</p>
                    <?php elseif ($el->explanationArray['source_title'] && $el->explanationArray['source_url']) : ?>
                        <div style="font-size: 14px;">
                            <div class="word-wrap">出典URL: <a href="<?php echo $el->explanationArray['source_url'] ?>" target="_blank"><?php echo $el->explanationArray['source_title'] ?> ↗</a></div>
                            <div class="word-wrap"><small style="color: #aaa;" class="source-url"><?php echo $el->explanationArray['source_url'] ?></small></div>
                        </div>
                    <?php elseif ($el->explanationArray['source_url'] === '') : ?>
                        <div style="font-size: 14px;">
                            <div class="word-wrap">出典URL: <a href="https://openchat-jp.line.me/other/guideline" target="_blank">安心・安全ガイドライン | LINEオープンチャット ↗</a></div>
                            <div class="word-wrap"><small style="color: #aaa;" class="source-url">https://openchat-jp.line.me/other/guideline</small></div>
                        </div>
                    <?php endif ?>

                    <div style="display: flex; gap: 6px; padding-top: 1rem;">
                        <small style="word-break: keep-all; text-wrap: nowrap;">作成者</small>

                        <?php if ($el->is_admin_user) : ?>
                            <small style="display: block;">👑</small>
                        <?php endif ?>

                        <small style="display: block;"><a href="./user?id=<?php echo $el->user_id ?>"><?php echo $el->user_name ?></a></small>
                        <small style="display: block; word-break: keep-all; text-wrap: nowrap;"><?php echo formatDateTimeHourly2($el->created_at, true) ?></small>
                    </div>

                    <?php if ($el->edit_user_id) : ?>
                        <div style="display: flex; gap: 6px; margin-top: 16px;">
                            <small style="word-break: keep-all; text-wrap: nowrap;">最終更新</small>

                            <?php if ($el->is_admin_edit_user) : ?>
                                <small style="display: block;">👑</small>
                            <?php endif ?>

                            <small style="display: block;"><a href="./user?id=<?php echo $el->edit_user_id ?>"><?php echo $el->edit_user_name ?></a></small>
                            <small style="display: block; word-break: keep-all; text-wrap: nowrap;"><?php echo formatDateTimeHourly2($el->edited_at, true) ?></small>
                        </div>
                    <?php endif ?>

                    <?php if ($el->isPabulished) : ?>
                        <div style="display: flex; gap: 16px; margin-top: 1rem; flex-wrap: wrap; justify-content: flex-end; align-items: center;">
                            <small style="word-break: keep-all; text-wrap: nowrap;">シェア</small>

                            <div class="share-menu-item unset" onclick="copyUrl('<?php echo ('オプチャ検定｜Q.' . $el->id . '\n' . $shareUrl . $el->id) ?>')">
                                <span class="copy-btn-icon share-menu-icon copy"></span>
                            </div>
                            <a class="share-menu-item unset" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($shareUrl . $el->id) ?>&text=<?php echo urlencode($el->question . "\nオプチャ検定｜Q." . $el->id . "\n") ?>" rel="nofollow noopener" target="_blank" title="ポスト">
                                <span class="share-menu-icon-twitter share-menu-icon"></span>
                            </a>

                            <a class="share-menu-item unset" href="http://line.me/R/msg/text/?<?php echo urlencode('オプチャ検定｜Q.' . $el->id . "\n" . $shareUrl . $el->id) ?>" rel="nofollow noopener" target="_blank" title="LINEで送る">
                                <span class="share-menu-icon-line share-menu-icon"></span>
                            </a>
                            <a href="<?php echo $shareUrl . $el->id ?>" target="_blank">開く</a>
                        </div>
                    <?php endif ?>
                </aside>
            <?php endforeach ?>
            <button class="scroll-to-top" id="scrollToTopBtn">
                <svg width="80%" height="80%" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                    <line x1="16" y1="8" x2="16" y2="24" style="stroke:#000;stroke-width:1.5" />
                    <line x1="16" y1="8" x2="8" y2="16" style="stroke:#000;stroke-width:1.5" />
                    <line x1="16" y1="8" x2="24" y2="16" style="stroke:#000;stroke-width:1.5" />
                </svg>
            </button>
            <script>
                window.onscroll = function() {
                    scrollFunction()
                };

                function scrollFunction() {
                    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
                    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                        scrollToTopBtn.style.display = "flex";
                    } else {
                        scrollToTopBtn.style.display = "none";
                    }
                }

                document.getElementById('scrollToTopBtn').addEventListener('click', function() {
                    window.scroll({
                        top: 0,
                        behavior: 'smooth'
                    })
                });
            </script>
        </section>
<?php
    }
}
