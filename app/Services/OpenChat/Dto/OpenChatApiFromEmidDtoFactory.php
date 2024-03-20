<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

use App\Exceptions\InvalidMemberCountException;
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
            $dto->setApiDataInvitationTicket(Validator::str($response['invitationTicket'], e: $exceptionClass));
            $dto->emid = Validator::str($response['square']['squareEmid'], e: $exceptionClass);
            $dto->name = Validator::str($response['square']['name'], emptyAble: true, e: $exceptionClass);
            $dto->desc = Validator::str($response['square']['desc'], emptyAble: true, e: $exceptionClass);
            $dto->profileImageObsHash = Validator::str($response['square']['profileImageObsHash'], e: $exceptionClass);
            $dto->memberCount = Validator::num($response['square']['memberCount'], e: InvalidMemberCountException::class, min: 1);
        } catch (InvalidMemberCountException $e) {
            $jsonString = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new InvalidMemberCountException($e->__toString() . ": {$jsonString}");
        } catch (\Throwable $e) {
            $jsonString = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new $exceptionClass($e->__toString() . ": {$jsonString}");
        }

        return $dto;
    }
}
