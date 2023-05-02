<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Config\AppConfig;
use App\Config\OpenChatCrawlerConfig;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use App\Services\OpenChat\Crawler\OpenChatImgDownloader;

class UpdateOpenChat
{
    private UpdateOpenChatRepositoryInterface $updateRepository;
    private OpenChatRepositoryInterface $openChatRepository;
    private OpenChatCrawler $crawler;
    private OpenChatImgDownloader $imgDownloader;

    function __construct(
        UpdateOpenChatRepositoryInterface $updateRepository,
        OpenChatRepositoryInterface $openChatRepository,
        OpenChatCrawler $crawler,
        OpenChatImgDownloader $imgDownloader
    ) {
        $this->updateRepository = $updateRepository;
        $this->openChatRepository = $openChatRepository;
        $this->crawler = $crawler;
        $this->imgDownloader = $imgDownloader;
    }

    /**
     * オープンチャットを更新する
     * 
     * @return false|array 404の場合はfalse 
     *         `['updatedData' => ['name' => string|null, 'img_url' => string|null, 'description' => string|null, 'member' => int|null], 'databaseData' => ['name' => string, 'img_url' => string, 'description' => string, 'member' => int], 'isUpdated' => bool]`
     * 
     * @throws \RuntimeException 取得時にエラーが発生した場合
     */
    function update(int $open_chat_id): false|array
    {
        // DBからオープンチャットを取得する
        $existingOpenChat = $this->openChatRepository->getOpenChatById($open_chat_id);
        if ($existingOpenChat === false) {
            throw new \RuntimeException('DBにオープンチャットがありません。');
        }

        // オープンチャットのページからデータを取得
        $openChat = $this->crawler->getOpenChat(AppConfig::LINE_URL . $existingOpenChat['url']);
        if ($openChat === false) {
            // 404の場合は'is_alive'カラムをfalseに更新する
            $this->updateRepository->updateOpenChat($open_chat_id, false);
            return false;
        }

        $databaseData = [
            'name' => $existingOpenChat['name'],
            'img_url' => $existingOpenChat['img_url'],
            'description' => $existingOpenChat['description'],
            'member' => $existingOpenChat['member']
        ];

        // DBのデータと、ページから取得したデータの差分を抽出
        $updatedData = $this->compareArrays(
            $this->prepareOpenChatData($openChat),
            $databaseData
        );

        if (empty(array_filter($updatedData, fn ($value) => !is_null($value)))) {
            // 差分がない場合
            $this->updateRepository->updateOpenChat($open_chat_id);
            return compact('updatedData', 'databaseData') + ['isUpdated' => false];
        }

        // 画像が更新されている場合はダウンロードする
        if ($updatedData['img_url'] !== null) {
            $this->updateImg($existingOpenChat['img_url'], $updatedData['img_url']);
        }

        // DBを更新する
        $this->updateRepository->updateOpenChat($open_chat_id, true, ...$updatedData);
        return compact('updatedData', 'databaseData') + ['isUpdated' => true];
    }

    /**
     * 画像URLから識別子のみを抽出して置き換える
     */
    private function prepareOpenChatData(array $openChat): array
    {
        $openChat['img_url'] = str_replace(OpenChatCrawlerConfig::LINE_IMG_URL, '', $openChat['img_url']);
        return $openChat;
    }

    /**
     * @throws \RuntimeException サーバーエラーなどの場合
     */
    private function updateImg(string $openChatImgIdentifier, string $newOpenChatImgIdentifier)
    {
        // オープンチャットの画像を保存する
        $result = $this->imgDownloader->storeOpenChatImg($newOpenChatImgIdentifier);
        if ($result) {
            deleteFile(OpenChatCrawlerConfig::SOTRE_IMG_DEST_PATH . '/' . $openChatImgIdentifier . '.' . \ImageType::WEBP->value);
            deleteFile(OpenChatCrawlerConfig::SOTRE_IMG_PREVIEW_DEST_PATH . '/' . $openChatImgIdentifier . '.' . \ImageType::WEBP->value);
        } else {
            throw new \RuntimeException('img not found: ' . $newOpenChatImgIdentifier);
        }
    }

    /**
     * 配列を比較して、array1の要素から差分の値のみを残して返す  
     * 同じ値で重複しているキーは、値がnullになる  
     */
    private function compareArrays(array $array1, array $array2): ?array
    {
        $result = [];

        foreach ($array1 as $key => $value) {
            if (isset($array2[$key]) && $array2[$key] === $value) {
                $result[$key] = null;
            } elseif (!isset($array2[$key]) || $array2[$key] !== $value) {
                $result[$key] = $value;
            }
        }

        foreach ($array2 as $key => $value) {
            if (!isset($array1[$key])) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
