<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Services\User\CookieListService;
use App\Models\Repositories\OpenChatListRepositoryInterface;

class MyOpenChatList
{
    function __construct(
        private OpenChatListRepositoryInterface $openChatListRepository,
        private CookieListService $cookieListService
    ) {
    }

    function init(): array
    {
        if (!$this->cookieListService->init()) return [0, [], []];

        $cookieList = $this->cookieListService->getListArray();
        $idArray = array_keys($cookieList);

        return [
            $this->cookieListService->getExpires(),
            $idArray,
            $this->getMyOpenChatList($cookieList, $idArray)
        ];
    }

    function getMyOpenChatList(array $cookieList, array $idArray): array
    {
        if (!$cookieList || !$idArray) return [];

        $myOpenChatList = $this->openChatListRepository->getRankingRecordByMylist($idArray);
        $removeFlag = false;

        if (count($cookieList) !== count($myOpenChatList)) {
            foreach ($idArray as $id) {
                $result = array_filter($myOpenChatList, fn ($oc) => $oc['id'] === $id);

                if (!$result) {
                    $removeFlag = true;
                    unset($cookieList[$id]);
                    continue;
                }
            }
        }

        if ($removeFlag) {
            $this->cookieListService->setListArrayCookie($cookieList);
        }

        usort($myOpenChatList, fn ($a, $b) => ($a['ranking_id'] ?? 9999999999) - ($b['ranking_id'] ?? 9999999999));
        return $myOpenChatList;
    }
}
