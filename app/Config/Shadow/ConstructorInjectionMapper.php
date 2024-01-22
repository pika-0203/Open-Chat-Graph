<?php

namespace App\Config\Shadow;

class ConstructorInjectionMapper
{
    static $map = [
        \Shadow\StringCryptorInterface::class => \Shadow\StringCryptor::class,
        \Shadow\JsonStorageInterface::class => \Shadow\JsonStorage::class,
        \Shadow\DBInterface::class => \Shadow\DB::class,
        \Shadow\Kernel\ViewInterface::class => \Shadow\Kernel\View::class,
        \Shadow\File\FileValidatorInterface::class => \Shadow\File\FileValidator::class,
        \Shadow\File\Image\ImageStoreInterface::class => \Shadow\File\Image\ImageStore::class,
        \Shadow\File\Image\GdImageFactoryInterface::class => \Shadow\File\Image\GdImageFactory::class,

        \App\Models\Repositories\Log\LogRepositoryInterface::class => \App\Models\Repositories\Log\LogRepository::class,

        \App\Models\Repositories\OpenChatRepositoryInterface::class => \App\Models\Repositories\OpenChatRepository::class,
        \App\Models\Repositories\UpdateOpenChatRepositoryInterface::class => \App\Models\Repositories\UpdateOpenChatRepository::class,
        \App\Models\Repositories\DeleteOpenChatRepositoryInterface::class => \App\Models\Repositories\DeleteOpenChatRepository::class,
        \App\Models\Repositories\DuplicateOpenChatRepositoryInterface::class => \App\Models\Repositories\DuplicateOpenChatRepository::class,

        \App\Models\Repositories\Statistics\StatisticsRepositoryInterface::class => \App\Models\SQLite\Repositories\Statistics\SqliteStatisticsRepository::class,
        \App\Models\Repositories\Statistics\StatisticsRankingUpdaterRepositoryInterface::class => \App\Models\SQLite\Repositories\Statistics\SqliteStatisticsRankingUpdaterRepository::class,
        \App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface::class => \App\Models\SQLite\Repositories\Statistics\SqliteStatisticsPageRepository::class,

        \App\Models\Repositories\RankingPosition\RankingPositionHourRepositoryInterface::class => \App\Models\SQLite\Repositories\RankingPosition\SqliteRankingPositionHourRepository::class,
        \App\Models\Repositories\RankingPosition\RankingPositionRepositoryInterface::class => \App\Models\SQLite\Repositories\RankingPosition\SqliteRankingPositionRepository::class,
        \App\Models\Repositories\RankingPosition\RankingPositionHourApiRepositoryInterface::class => \App\Models\SQLite\Repositories\RankingPosition\SqliteRankingPositionHourApiRepository::class,
        \App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface::class => \App\Models\SQLite\Repositories\RankingPosition\SqliteRankingPositionPageRepository::class,
        
        \App\Models\Repositories\OpenChatListRepositoryInterface::class => \App\Models\Repositories\OpenChatListRepository::class,
        \App\Models\Repositories\OpenChatPageRepositoryInterface::class => \App\Models\Repositories\OpenChatPageRepository::class,

        \App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface::class => \App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepository::class,

        \App\Services\OpenChat\Updater\OpenChatUpdaterInterface::class => \App\Services\OpenChat\Updater\OpenChatUpdater::class,
    ];
}
