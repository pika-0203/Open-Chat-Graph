<?php

/** @var \App\Services\OpenChatAdmin\Dto\AdminOpenChatDto $_adminDto */
?>
<form onsubmit="return confirm('変更しますか？')" action="/admin-api" method="POST" style="margin: 1rem 0;">
    <b>タグ: <?php echo $_adminDto->recommendTag ?: '無し' ?></b>
    <label>タグ変更</label>
    <input type="text" name="tag">
    <input type="hidden" name="id" value="<?php echo $_adminDto->id ?>">
    <input type="hidden" name="type" value="modifyTag">
    <input type="submit">
</form>
<form onsubmit="return confirm('削除しますか？')" action="/admin-api" method="POST" style="margin: 1rem 0;">
    <b>Modifyタグ: <?php echo $_adminDto->modifyTag ?: '無し' ?></b>
    <label>タグを削除</label>
    <input type="hidden" name="id" value="<?php echo $_adminDto->id ?>">
    <input type="hidden" name="type" value="deleteModifyTag">
    <input type="submit">
</form>