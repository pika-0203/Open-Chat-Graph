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
    <hr>

    <?php $view->profile() ?>

    <form onsubmit="return confirm('<?php echo $profile ? '変更' : '登録' ?>しますか？')" id="user-form" method="POST" action="/accreditation/register-profile<?php echo $returnTo . ($profile ? '/profile' : '/home')  ?>">
      <label for="user_name">お好きなニックネーム（必須）</label>
      <input type="text" id="user_name" name="name" maxlength="20" required value="<?php echo $profile['name'] ?? '' ?>" />
      <label for="oc_url">運営しているオプチャのURL（任意）</label>
      <small id="url-message" style="display: none;">URLが無効です</small>
      <div style="display: flex; flex-direction: column;">
        <input type="text" id="oc_url" name="url" value="<?php echo $profile['url'] ?? '' ?>" />
        <button type="button" style="padding: 2px; margin-left: auto; margin-top: -8px; margin-bottom: 1rem; font-size: 12px; display:block; text-wrap: nowrap; width: fit-content;" id="clear-url-btn">クリア</button>
      </div>

      <?php if (Reception::has('admin') && !($profile['is_admin'] ?? null)) : ?>
        <label for="admin_key">管理者登録パスワード</label>
        <input type="text" id="admin_key" name="admin_key" maxlength="20" />
      <?php endif ?>

      <?php if ($profile) : ?>
        <input id="submit-btn" type="submit" value="変更" />
      <?php else : ?>
        <small>後から変更できます</small>
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
    document.getElementById('clear-url-btn').addEventListener('click', () => addOpenChatForm.value = '')
  </script>
</body>

</html>