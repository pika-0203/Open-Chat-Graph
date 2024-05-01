<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface;
use App\Services\OpenChat\Updater\MemberColumnUpdater;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class UpdateHourlyMemberColumnService
{
    private \DateTime $time;

    function __construct(
        private RankingPositionHourRepositoryInterface $rankingPositionHourRepository,
        private MemberColumnUpdater $memberColumnUpdater,
    ) {
        $this->time = OpenChatServicesUtility::getModifiedCronTime('now');
    }

    function update(): void
    {
        $inRankIdMember = $this->rankingPositionHourRepository->getHourlyMemberColumn($this->time);

        $this->memberColumnUpdater->updateMemberColumn($inRankIdMember);
    }
}
