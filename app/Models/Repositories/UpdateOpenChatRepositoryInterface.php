<?php

declare(strict_types=1);

namespace App\Models\Repositories;

interface UpdateOpenChatRepositoryInterface
{
    /**
     * @throws \RuntimeException レコードが更新されなかった場合
     */
    public function updateOpenChat(
        int $id,
        ?bool $is_alive = null,
        ?string $name = null,
        ?string $img_url = null,
        ?string $description = null,
        ?int $member = null,
    ): void;

    /**
     * @param int $next_update 次に更新する日をunixtimeで指定
     * 
     * @throws \RuntimeException レコードが更新されなかった場合
     */
    public function updateNextUpdate(int $id, int $next_update);

    /**
     * 指定したunixtime以前に更新されたIDを取得する
     */
    public function getOpenChatIdByPeriod(int $limit): array;

    /**
     * 同じ画像を使用する別のオープンチャットがあるかを調べる
     */
    public function existsRecordByImgUrlExcludingId(int $open_chat_id, string $img_url): bool;

    /**
     * 過去一週間でメンバー数に変化があったかを調べる
     */
    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool;

    public function deleteOpenChat(int $id): bool;
}
