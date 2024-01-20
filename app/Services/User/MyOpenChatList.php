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

    function init(): bool
    {
        return $this->cookieListService->init();
    }

    function get(): array
    {
        $cookieList = $this->cookieListService->getListArray();
        $removeFlag = false;
        $idArray = array_keys($cookieList);

        $myOpenChatList = $this->openChatListRepository->getRankingRecordByMylist($idArray);

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
