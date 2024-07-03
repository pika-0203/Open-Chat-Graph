<?php

use App\Views\Content\Accreditation\AccreditationAdminViewContent;
use Shadow\Kernel\Reception;

$view = new AccreditationAdminViewContent($controller);
$returnTo = "?return_to=/accreditation/{$view->controller->type->value}";
$profile = $view->controller->profileArray;
?>

<!DOCTYPE html>
<html lang="ja">
<?php $view->head() ?>

<body>
  <?php $view->header() ?>
  <main>
    <h2>プロフィール設定</h2>
    <?php $view->profileTerm() ?>

    <?php $view->profile() ?>

    <form onsubmit="return confirm('<?php echo $profile ? '変更' : '登録' ?>しますか？')" id="user-form" method="POST" action="/accreditation/register-profile<?php echo $returnTo . ($profile ? '/profile' : '/home')  ?>">
      <label for="user_name">お好きなニックネーム（必須）
        <?php if (!$profile) : ?>
          <br>
          <small style="font-weight: normal;">後から変更できます</small>
        <?php endif ?>
      </label>
      <input type="text" id="user_name" name="name" maxlength="20" required value="<?php echo $profile['name'] ?? '' ?>" />
      <label for="oc_url">運営しているオプチャのURL（任意）
        <?php if (!$profile) : ?>
          <br>
          <small style="font-weight: normal;">後から変更できます</small>
        <?php endif ?>
      </label>
      <small id="url-message" style="display: none;">URLが無効です</small>
      <div style="display: flex; flex-direction: column;">
        <input type="text" id="oc_url" name="url" value="<?php echo $profile['url'] ?? '' ?>" />
        <button type="button" style="padding: 2px 10px; margin-left: auto; margin-top: -8px; margin-bottom: 1rem; font-size: 12px; display:block; text-wrap: nowrap; width: fit-content;" id="clear-url-btn">クリア</button>
      </div>

      <?php if ($profile) : ?>
        <input id="submit-btn" type="submit" value="変更" />
      <?php else : ?>
        <a target="_blank" href="/accreditation/privacy" style="margin-bottom: 12px; display: block; width:fit-content;">プライバシーポリシー</a>
        <a target="_blank" href="/policy/term" style="margin-bottom: 10px; display: block; width:fit-content;">利用規約</a>
        <small>登録時に「プライバシーポリシー」・「利用規約」をお読みの上、同意していただくことが必要です。</small>
        <br>
        <input id="submit-btn" type="submit" value="登録" />
      <?php endif ?>

    </form>

    <p style="text-align:right;"><a href="<?php echo url("auth/logout{$returnTo}/home") ?>">ログアウトする</a></p>
  </main>
  <?php $view->footer() ?>
  <script type="module">
    import {
      OpenChatUrlValidator
    } from '<?php echo fileUrl('/js/OpenChatInternalUrlValidator.js') ?>';

    const addOpenChatForm = document.getElementById('oc_url')
    const inputValidator = new OpenChatUrlValidator(
      addOpenChatForm, document.getElementById('url-message'), document.getElementById('submit-btn')
    )

    addOpenChatForm.addEventListener('input', () => inputValidator.handle())
    document.getElementById('clear-url-btn').addEventListener('click', () => {
      addOpenChatForm.value = ''
      inputValidator.handle()
    })
  </script>
</body>

</html>