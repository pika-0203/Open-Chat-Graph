<?php

namespace App\Services\OpenChat\Crawler;

use App\Services\OpenChat\Dto\OpenChatDto;

interface OpenChatDtoFetcherInterface
{
    /**
     * @throws \RuntimeException
     */
    function fetchOpenChatDto(string $fetcherArg): OpenChatDto|false;
}