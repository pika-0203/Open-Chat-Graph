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
     * 指定したunixtime以前に更新されたIDを取得する
     */
    public function getOpenChatIdByPeriod(int $before_at, int $limit): array;

    /**
     * 同じ画像を使用する別のオープンチャットがあるかを調べる
     */
    public function existsRecordByImgUrlExcludingId(int $open_chat_id, string $img_url): bool;

    public function deleteOpenChat(int $id): bool;
}
