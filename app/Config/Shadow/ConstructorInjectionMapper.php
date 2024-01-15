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

        \App\Models\Repositories\LogRepositoryInterface::class => \App\Models\Repositories\LogRepository::class,

        \App\Models\Repositories\OpenChatRepositoryInterface::class => \App\Models\Repositories\OpenChatRepository::class,
        \App\Models\Repositories\StatisticsRepositoryInterface::class => \App\Models\SQLite\SqliteStatisticsRepository::class,
        \App\Models\Repositories\UpdateOpenChatRepositoryInterface::class => \App\Models\Repositories\UpdateOpenChatRepository::class,
        \App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface::class => \App\Models\SQLite\SqliteStatisticsRankingUpdaterRepository::class,
        
        \App\Models\Repositories\OpenChatListRepositoryInterface::class => \App\Models\Repositories\OpenChatListRepository::class,
        \App\Models\Repositories\OpenChatPageRepositoryInterface::class => \App\Models\Repositories\OpenChatPageRepository::class,
        \App\Models\Repositories\StatisticsPageRepositoryInterface::class => \App\Models\SQLite\SqliteStatisticsPageRepository::class,

        \App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface::class => \App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepository::class,

        \App\Services\OpenChat\Updater\OpenChatUpdaterInterface::class => \App\Services\OpenChat\Updater\OpenChatUpdater::class,
    ];
}
