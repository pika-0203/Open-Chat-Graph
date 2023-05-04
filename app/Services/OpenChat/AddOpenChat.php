<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Config\AppConfig;
use App\Config\OpenChatCrawlerConfig;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use App\Services\OpenChat\Crawler\OpenChatImgDownloader;
use App\Services\Auth;

class AddOpenChat
{
    private OpenChatRepositoryInterface $openChatRepository;
    private LogRepositoryInterface $logRepository;
    private OpenChatCrawler $crawler;
    private OpenChatImgDownloader $imgDownloader;

    function __construct(
        OpenChatRepositoryInterface $openChatRepository,
        LogRepositoryInterface $logRepository,
        OpenChatCrawler $crawler,
        OpenChatImgDownloader $imgDownloader
    ) {
        $this->openChatRepository = $openChatRepository;
        $this->logRepository = $logRepository;
        $this->crawler = $crawler;
        $this->imgDownloader = $imgDownloader;
    }

    /**
     * DBにオープンチャットを登録する
     * 
     * @return array `['message' => string, 'id' => int|null]`
     * 
     * @throws \LogicException URLのパターンがマッチしない場合
     */
    function add(string $url): array
    {
        // オープンチャットのURLから識別子を抽出する
        $openChatIdentifier = $this->parseOpenChatIdentifierFromUrl($url);

        $existingOpenChatId = $this->openChatRepository->getOpenChatIdByUrl($openChatIdentifier);
        if ($existingOpenChatId !== false) {
            // オープンチャットが登録済みの場合
            return $this->exitingOpenChatMessage($existingOpenChatId);
        }

        // オープンチャットのページからデータを取得
        $openChat = $this->fetchOpenChat(AppConfig::LINE_URL . $openChatIdentifier);
        if ($openChat === false) {
            // 404の場合
            return $this->failMessage();
        }

        // URL、画像URLに含まれる識別子のみを抽出して上書きする
        $openChat = $this->prepareOpenChatData($openChat, $openChatIdentifier);

        $existingOpenChatId = $this->openChatRepository->getOpenChatIdByImgUrl($openChat['img_url']);
        if ($existingOpenChatId !== false) {
            // 同じ画像URLのオープンチャットが登録済み場合（いずれかがサブトークルーム）
            $this->logRepository->logAddOpenChatDuplicationError(Auth::id(), $existingOpenChatId, $openChatIdentifier, getIP(), getUA());
            return $this->exitingOpenChatMessage($existingOpenChatId);
        }

        // オープンチャットの画像をダウンロードする
        if (!$this->downloadImg($openChat['img_url'])) {
            return $this->failMessage();
        }

        // オープンチャットを登録する
        $openChatId = $this->openChatRepository->addOpenChat(...$openChat);
        $this->logRepository->logAddOpenChat(Auth::id(), $openChatId, getIP(), getUA());

        return $this->successMessage($openChatId);
    }

    private function parseOpenChatIdentifierFromUrl(string $url): string
    {
        if (!preg_match(OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN, $url, $match)) {
            throw new \LogicException('URLのパターンがマッチしませんでした。');
        }
        return $match[0];
    }

    /**
     * @return array|false `['name' => string, 'img_url' => string, 'description' => string, 'member' => int]`
     * 
     * @throws \RuntimeException
     */
    private function fetchOpenChat(string $url): array|false
    {
        // オープンチャットを取得する
        try {
            $response = $this->crawler->getOpenChat($url);
            if ($response === false) {
                $this->logAddOpenChatError('404 Not found');
                return false;
            }
            return $response;
        } catch (\RuntimeException $e) {
            $this->logAddOpenChatError($e->getMessage());
            return false;
        }
    }

    private function logAddOpenChatError(string $message)
    {
        $this->logRepository->logAddOpenChatError(Auth::id(), getIP(), getUA(), $message);
    }

    private function prepareOpenChatData(array $openChat, string $openChatIdentifier): array
    {
        // URL、画像URLに含まれる識別子のみを抽出して上書きする
        $openChat['url'] = $openChatIdentifier;
        $openChat['img_url'] = str_replace(OpenChatCrawlerConfig::LINE_IMG_URL, '', $openChat['img_url']);
        return $openChat;
    }

    private function downloadImg(string $openChatImdIdentifier): bool
    {
        // オープンチャットの画像を保存する
        try {
            $result = $this->imgDownloader->storeOpenChatImg($openChatImdIdentifier);
            if ($result) {
                return true;
            }
            $this->logAddOpenChatError('img not found: ' . $openChatImdIdentifier);
            return false;
        } catch (\RuntimeException $e) {
            $this->logAddOpenChatError($e->getMessage());
            return false;
        }
    }

    private function exitingOpenChatMessage(int $id): array
    {
        return [
            'message' => 'オープンチャットが既に登録されています',
            'id' => $id
        ];
    }

    private function failMessage(): array
    {
        return [
            'message' => '無効なURLです。',
            'id' => null
        ];
    }

    private function successMessage(int $id): array
    {
        return [
            'message' => 'オープンチャットを登録しました',
            'id' => $id
        ];
    }
}
