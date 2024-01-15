<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

use Shadow\Kernel\Validator;

class OpenChatApiFromEmidDtoFactory
{
    /**
     * @throws \RuntimeException
     */
    function validateAndMapToOpenChatApiFromEmidDto(array $response): OpenChatDto
    {
        $exceptionClass = \RuntimeException::class;

        $dto = new OpenChatDto;

        try {
            $dto->emid = Validator::str($response['square']['squareEmid'], e: $exceptionClass);
            $dto->name = Validator::str($response['square']['name'], emptyAble: true, e: $exceptionClass);
            $dto->desc = Validator::str($response['square']['desc'], emptyAble: true, e: $exceptionClass);
            $dto->profileImageObsHash = Validator::str($response['square']['profileImageObsHash'], e: $exceptionClass);
            $dto->memberCount = Validator::num($response['square']['memberCount'], e: $exceptionClass);
            $dto->invitationTicket = Validator::str($response['invitationTicket'], e: $exceptionClass);
            //$dto->noteCount = Validator::num($response['noteCount'], emptyAble: true, e: $exceptionClass);
            //$dto->recommendedSquaresEmidArray = $this->varidateRecommendedSquaresEmidArray($response['recommendedSquares'] ?? []);
        } catch (\Throwable $e) {
            $jsonString = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new $exceptionClass($e->__toString() . ": {$jsonString}");
        }

        return $dto;
    }

    /**
     * @return array `['invitationTicket' => string]`  
     * 
     * @throws \RuntimeException
     */
    function validateAndMapToOpenChatApiFromEmidDtoElementArray(array $response): array
    {
        $exceptionClass = \RuntimeException::class;

        try {
            $invitationTicket = Validator::str($response['invitationTicket'], e: $exceptionClass);
        } catch (\Throwable $e) {
            $jsonString = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new $exceptionClass($e->__toString() . ": {$jsonString}");
        }

        return compact('invitationTicket');
    }

    private function varidateRecommendedSquaresEmidArray(?array $recommendedSquares): array
    {
        $recommendedSquaresEmidArray = [];
        foreach ($recommendedSquares as $element) {
            $recommendedSquaresEmidArray[] = Validator::str($element['square']['emid'], e: \RuntimeException::class);
        }

        return $recommendedSquaresEmidArray;
    }
}
