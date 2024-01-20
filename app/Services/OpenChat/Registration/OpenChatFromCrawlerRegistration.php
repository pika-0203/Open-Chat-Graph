<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Registration;

use App\Config\OpenChatCrawlerConfig;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use App\Services\OpenChat\Store\OpenChatImageStore;
use App\Services\OpenChat\Dto\OpenChatDto;
use Shared\Exceptions\ThrottleRequestsException;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatFromCrawlerRegistration
{
    function __construct(
        private OpenChatRepositoryInterface $openChatRepository,
        private LogRepositoryInterface $logRepository,
        private OpenChatCrawler $crawler,
        private OpenChatImageStore $openChatImageStore
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
        // オープンチャットのURLからInvitationTicketを抽出する
        $invitationTicket = $this->parseInvitationTicketFromUrl($url);

        $existingOpenChatId = $this->openChatRepository->getOpenChatIdByUrl($invitationTicket);
        if ($existingOpenChatId !== false) {
            return $this->returnMessage('オープンチャットが既に登録されています', $existingOpenChatId);
        }

        $ocDto = $this->fetchOpenChat($invitationTicket);
        if (is_array($ocDto)) {
            // エラーメッセージの場合
            return $ocDto;
        }

        if (OpenChatServicesUtility::containsHashtagNolog($ocDto->desc)) {
            $this->logAddOpenChatError('収集拒否: ' . $invitationTicket);

            return $this->returnMessage('拒否: 説明文に「#nolog」が含まれています');
        }

        $existingOpenChatId = $this->openChatRepository->findDuplicateOpenChat($ocDto);

        if ($existingOpenChatId !== false) {
            return $this->returnMessage('オープンチャットが既に登録されています', $existingOpenChatId);
        }

        return $this->registerRecordProcess($ocDto);
    }

    private function registerRecordProcess(OpenChatDto $ocDto): array
    {
        $open_chat_id = $this->openChatRepository->addOpenChatFromDto($ocDto);

        $this->openChatRepository->markAsRegistrationByUser($open_chat_id);

        if (!$this->openChatImageStore->downloadAndStoreOpenChatImage($ocDto->profileImageObsHash, $open_chat_id)) {
            // 画像のダウンロードに失敗した場合
            $this->openChatRepository->markAsNoImage($open_chat_id);
        }

        $this->logRepository->logAddOpenChat($open_chat_id, getIP(), getUA());

        return $this->returnMessage('オープンチャットを登録しました', $open_chat_id);
    }

    private function fetchOpenChat(string $invitationTicket): OpenChatDto|array
    {
        try {
            $result = $this->crawler->fetchOpenChatDto($invitationTicket);
            if (!$result) {
                $this->logAddOpenChatError('404 Not found: ' . $invitationTicket);

                return $this->returnMessage('無効なURLです');
            }
        } catch (\RuntimeException $e) {
            $this->logAddOpenChatError($e->getMessage());

            return $this->returnMessage('ネットワークエラーが発生しました');
        }

        return $result;
    }

    private function parseInvitationTicketFromUrl(string $url): string
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
