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
    private const HIDDEN_USER_ID = [
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
    { ?>

        <head prefix="og: http://ogp.me/ns#">
            <?php echo gTag(AppConfig::GTM_ID) ?>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>オプチャ検定｜<?php echo $this->examTypeName ?>｜問題投稿ページ</title>
            <meta name="description" content="ここから誰でもオプチャ検定に問題を投稿できます。ぜひご協力を！">
            <meta property="og:locale" content="ja_JP">
            <meta property="og:url" content="<?php echo url(path()) ?>">
            <meta property="og:type" content="website">
            <meta property="og:title" content="オプチャ検定｜<?php echo $this->examTypeName ?>｜問題投稿ページ">
            <meta property="og:description" content="ここから誰でもオプチャ検定に問題を投稿できます。ぜひご協力を！">
            <meta property="og:site_name" content="公認メンター監修・オプチャ検定">
            <meta property="og:image" content="<?php echo fileUrl('assets/ogp-accreditation.png') ?>">
            <meta name="twitter:card" content="summary">
            <link rel="icon" type="image/png" href="<?php echo fileUrl('assets/study_icon.png') ?>">
            <link rel="stylesheet" href="<?php echo fileUrl('style/mvp.css') ?>">
        </head>
    <?php
    }

    function header()
    { ?>
        <header style="padding-bottom: 1rem; margin-bottom: 1rem;">
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
            </style>
            <nav style="margin-bottom: 0">
                <a href="./home" style="text-decoration: none; text-wrap: nowrap;"><b><small>公認メンター監修</small><br />オプチャ検定<br /><span style="color: <?php echo $this->typeColor ?>;"><?php echo $this->examTypeName ?></span> 問題投稿</b></a>
                <ul class="header-nav-ul">
                    <?php if ($this->controller->profileArray) : ?>
                        <li>
                            <small style="font-size: 12px; color: #000;">
                                <?php if ($this->controller->profileArray['is_admin']) : ?>
                                    👑
                                <?php endif ?>
                                <?php echo $this->controller->profileArray['name'] ?>
                            </small>
                        </li>
                    <?php endif ?>
                    <?php foreach ([
                        'home' => 'ホーム',
                        'question' => '問題を投稿',
                        'user' => '投稿した問題',
                        'profile' => 'プロフィール設定',
                        'member' => 'メンバー'
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
                padding: 20px 10px;
            }

            @media screen and (max-width: 511px) {
                .main-tab {
                    gap: 8px;
                }

                .main-tab a i,
                .main-tab a b {
                    padding: 16px 8px;
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
                ['未公開の問題', 'unpublished'],
                ['出題中の問題', 'published'],
                ['投稿者の一覧', 'contributors'],
            ] as $p) : ?>
                <?php if ($this->controller->pageType === $p[1]) : ?>
                    <a><i><?php echo $p[0] ?></i></a>
                <?php else : ?>
                    <a href="./<?php echo $p[1] ?>"><b><?php echo $p[0] ?></b></a>
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
                    <?php $this->adminProfileForm('') ?>
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
                            <?php if ($p['id'] !== $this->controller->myId) : ?>
                                <a style="color: #111;" href="./user?id=<?php echo $p['id'] ?>"><?php echo $p['name'] ?></a>
                            <?php else : ?>
                                <span style="color: #111;"><?php echo $p['name'] ?></span>
                            <?php endif ?>
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
            <small>このサイトでは検定の問題集を管理しています。<br>LINEログインでメンバー登録を行い、誰でも問題文を投稿して作成に協力することができます。</small>
        </p>
        <?php echo $this->profileTerm() ?>
        <p>
            <small>投稿された問題は、LINEオープンチャット公認メンター監修の元、検定に合わせて編集等を行う場合があります。<br>問題数が限られているので、実際に出題されるのは一部の範囲の問題となります。</small>
        </p>
    <?php
    }

    function termHome()
    {
    ?>
        <p>
            <small>このサイトでは検定の問題集を管理しています。</small>
        </p>
        <?php $this->userTerm() ?>
        <?php $this->profileTerm() ?>
    <?php
    }

    function userTerm()
    {
    ?>
        <p>
            <small>投稿された問題は、LINEオープンチャット公認メンター監修の元、検定に合わせて編集等を行う場合があります。<br>問題数が限られているので、実際に出題されるのは一部の範囲の問題となります。</small>
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

    function footer()
    {
    ?>
        <footer>
            <hr style="margin-top: 0;">
            <p>
                <small>「オプチャ検定」はLINEオープンチャット非公式の検定です。LINEヤフー社はこの内容に関与していません。<br>監修しているのは一部のLINEオープンチャット公認メンターです。</small>
            </p>
            <small><a href="/accreditation/privacy" style="color: #b7b7b7; text-decoration: none;">プライバシーポリシー</a></small>
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
        <form style="user-select:none;" id="q-form" onsubmit="return confirm('<?php echo $confirm ?>')" id="user-form" method="POST" action="/accreditation/<?php echo $action . $returnTo ?>">
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

    ?>
        <section>
            <style>
                .question_paper {
                    margin: 0.5rem 0;
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
            </style>
            <?php if (!$editorMode) : ?>
                <small style="margin-right: auto; user-select: none; font-size: 14px;">全 <?php echo $listLen ?> 件</small>
            <?php endif ?>
            <?php foreach ($this->controller->questionList as $el) : ?>
                <?php $edit = AccreditationUtility::isQuestionEditable($el, $this->controller->myId, $this->controller->isAdmin) ?>

                <aside class="question_paper <?php if ($el->publishing) echo 'published' ?>">
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

                    <?php if ($edit) : ?>
                        <p class="question_p"><b>正解: <?php echo strtoupper($el->answersArray['correct'] ?? 'Error: 配列の要素がありません') ?></b></p>
                        <p class="question_p"><?php echo $el->explanationArray['explanation'] ?? 'Error: 配列の要素がありません(explanationArray)' ?></p>
                    <?php endif ?>

                    <?php if (!isset($el->explanationArray['source_title'], $el->explanationArray['source_url'])) : ?>
                        <p class="question_p">Error: 配列の要素がありません(explanationArray)</p>
                    <?php elseif ($el->explanationArray['source_title'] && $el->explanationArray['source_url']) : ?>
                        <div style="font-size: 14px;">
                            <div class="word-wrap">出典URL: <a href="<?php echo $el->explanationArray['source_url'] ?>" target="_blank"><?php echo $el->explanationArray['source_title'] ?> ↗</a></div>
                            <div class="word-wrap"><small style="color: #aaa;"><?php echo $el->explanationArray['source_url'] ?></small></div>
                        </div>
                    <?php elseif ($el->explanationArray['source_url'] === '') : ?>
                        <div style="font-size: 14px;">
                            <div class="word-wrap">出典URL: <a href="https://openchat-jp.line.me/other/guideline" target="_blank">安心・安全ガイドライン | LINEオープンチャット ↗</a></div>
                            <div class="word-wrap"><small style="color: #aaa;">https://openchat-jp.line.me/other/guideline</small></div>
                        </div>
                    <?php endif ?>

                    <div style="display: flex; gap: 6px; padding-top: 1rem;">
                        <small style="word-break: keep-all; text-wrap: nowrap;">作成者</small>

                        <?php if ($el->is_admin_user) : ?>
                            <small style="display: block;">👑</small>
                        <?php endif ?>

                        <?php if ($this->controller->myId !== $el->user_id && $this->controller->currentId !== $el->user_id) : ?>
                            <small style="display: block;"><a href="./user?id=<?php echo $el->user_id ?>"><?php echo $el->user_name ?></a></small>
                        <?php else : ?>
                            <small style="display: block;"><?php echo $el->user_name ?></small>
                        <?php endif ?>
                        <small style="display: block; word-break: keep-all; text-wrap: nowrap;"><?php echo formatDateTimeHourly2($el->created_at, true) ?></small>
                    </div>

                    <?php if ($el->edit_user_id) : ?>
                        <div style="display: flex; gap: 6px; margin-top: 16px;">
                            <small style="word-break: keep-all; text-wrap: nowrap;">最終更新</small>

                            <?php if ($el->is_admin_edit_user) : ?>
                                <small style="display: block;">👑</small>
                            <?php endif ?>

                            <?php if ($this->controller->myId !== $el->edit_user_id && $this->controller->currentId !== $el->edit_user_id) : ?>
                                <small style="display: block;"><a href="./user?id=<?php echo $el->edit_user_id ?>"><?php echo $el->edit_user_name ?></a></small>
                            <?php else : ?>
                                <small style="display: block;"><?php echo $el->edit_user_name ?></small>
                            <?php endif ?>
                            <small style="display: block; word-break: keep-all; text-wrap: nowrap;"><?php echo formatDateTimeHourly2($el->edited_at, true) ?></small>
                        </div>
                    <?php endif ?>

                </aside>
            <?php endforeach ?>
        </section>
<?php
    }
}
