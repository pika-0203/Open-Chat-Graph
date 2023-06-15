<?php

namespace App\Config;

class ConstructorInjectionClassMap
{
    const MAP = [
        \Shadow\StringCryptorInterface::class => \Shadow\StringCryptor::class,
        \Shadow\File\FileValidatorInterface::class => \Shadow\File\FileValidator::class,
        \Shadow\File\Image\ImageStoreInterface::class => \Shadow\File\Image\ImageStore::class,
        \Shadow\File\Image\GdImageFactoryInterface::class => \Shadow\File\Image\GdImageFactory::class,

        \App\Models\Repositories\LogRepositoryInterface::class => \App\Models\Repositories\LogRepository::class,
        
        \App\Models\Repositories\OpenChatRepositoryInterface::class => \App\Models\Repositories\OpenChatRepository::class,
        \App\Models\Repositories\OpenChatListRepositoryInterface::class => \App\Models\Repositories\OpenChatListRepository::class,
        \App\Models\Repositories\UpdateOpenChatRepositoryInterface::class => \App\Models\Repositories\UpdateOpenChatRepository::class,
        \App\Models\Repositories\StatisticsRepositoryInterface::class => \App\Models\Repositories\StatisticsRepository::class,
        \App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface::class => \App\Models\Repositories\StatisticsRankingUpdaterRepository::class,
    ];
}
