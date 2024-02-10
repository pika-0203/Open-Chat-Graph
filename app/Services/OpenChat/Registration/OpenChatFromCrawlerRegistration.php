<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Registration;

use App\Config\OpenChatCrawlerConfig;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Services\OpenChat\Crawler\OpenChatUrlChecker;
use App\Services\OpenChat\Dto\OpenChatDto;
use Shared\Exceptions\ThrottleRequestsException;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatFromCrawlerRegistration
{
    function __construct(
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
        private OpenChatRepositoryInterface $openChatRepository,
        private OpenChatApiFromEmidDownloader $openChatDtoFetcher,
        private OpenChatUrlChecker $openChatUrlChecker,
        private LogRepositoryInterface $logRepository,
    ) {
    }

    /**
     * 一分間のアクセス制限を確認する
     * 
     * @throws ThrottleRequestsException
     */
    function getNumAddOpenChatPerMinute(int $max = 20)
    {
        $count = $this->logRepository->getNumAddOpenChatPerMinute(getIP());
        if ($count > $max) {
            $this->logAddOpenChatError('アスセス回数の上限');
            throw new ThrottleRequestsException('アスセス回数の上限');
        }
    }

    /**
     * ページからオープンチャットを登録する
     * 
     * @return array `['message' => string, 'id' => int|null]`
     * 
     * @throws \LogicException URLのパターンがマッチしない場合
     */
    function registerOpenChatFromCrawler(string $url): array
    {
        // URLからEmidを抽出する
        $emid = $this->parseEmidFromUrl($url);

        $existingOpenChatId = $this->updateOpenChatRepository->getOpenChatIdByEmid($emid);
        if ($existingOpenChatId !== false) {
            return $this->returnMessage('オープンチャットが既に登録されています', $existingOpenChatId);
        }

        $ocDto = $this->fetchOpenChat($emid);
        if (is_array($ocDto)) {
            // エラーメッセージの場合
            return $ocDto;
        }

        if (OpenChatServicesUtility::containsHashtagNolog($ocDto->desc)) {
            $this->logAddOpenChatError('収集拒否: ' . $emid);
            return $this->returnMessage('拒否: 説明文に「#nolog」が含まれています');
        }

        return $this->registerRecordProcess($ocDto);
    }

    private function registerRecordProcess(OpenChatDto $ocDto): array
    {
        $open_chat_id = $this->openChatRepository->addOpenChatFromDto($ocDto);
        if (!$open_chat_id) {
            return $this->returnMessage('ネットワークエラーが発生しました');
        }

        $this->logRepository->logAddOpenChat($open_chat_id, getIP(), getUA());

        return $this->returnMessage('オープンチャットを登録しました', $open_chat_id);
    }

    private function fetchOpenChat(string $emid): OpenChatDto|array
    {
        try {
            $ocDto = $this->openChatDtoFetcher->fetchOpenChatDto($emid);
        } catch (\RuntimeException $e) {
            $this->logAddOpenChatError($e->getMessage());
            return $this->returnMessage('ネットワークエラーが発生しました');
        }

        if (
            $ocDto === false
            || $ocDto->memberCount < 1
            || !$this->openChatUrlChecker->isOpenChatUrlAvailable($ocDto->getApiDataInvitationTicket())
        ) {
            $this->logAddOpenChatError('404 Not found: ' . $emid);
            return $this->returnMessage('無効なURLです');
        }

        return $ocDto;
    }

    private function parseEmidFromUrl(string $url): string
    {
        if (!preg_match(OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN, $url, $match)) {
            throw new \LogicException('URLのパターンがマッチしませんでした');
        }

        return $match[0];
    }

    private function logAddOpenChatError(string $message)
    {
        $this->logRepository->logAddOpenChatError(getIP(), getUA(), $message);
    }

    private function returnMessage(string $string, ?int $id = null): array
    {
        return [
            'message' => $string,
            'id' => $id
        ];
    }
}
