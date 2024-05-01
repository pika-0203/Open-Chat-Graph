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
    function validateAndMapToOpenChatApiFromEmidDto(array $square): OpenChatDto
    {
        $exceptionClass = \RuntimeException::class;

        $dto = new OpenChatDto;

        try {
            $dto->invitationTicket = Validator::str($square['invitationTicket'], e: $exceptionClass);
            $dto->emid = Validator::str($square['square']['squareEmid'], e: $exceptionClass);
            $dto->name = Validator::str($square['square']['name'], emptyAble: true, e: $exceptionClass);
            $dto->desc = Validator::str($square['square']['desc'], emptyAble: true, e: $exceptionClass);
            $dto->profileImageObsHash = Validator::str($square['square']['profileImageObsHash'], e: $exceptionClass);
            $dto->memberCount = Validator::num($square['square']['memberCount'], e: InvalidMemberCountException::class, min: 1);
            $dto->joinMethodType = Validator::num($square['square']['joinMethodType'], e: $exceptionClass);
        } catch (InvalidMemberCountException $e) {
            $jsonString = json_encode($square, JSON_UNESCAPED_UNICODE);
            throw new InvalidMemberCountException($e->__toString() . ": {$jsonString}");
        } catch (\Throwable $e) {
            $jsonString = json_encode($square, JSON_UNESCAPED_UNICODE);
            throw new $exceptionClass($e->__toString() . ": {$jsonString}");
        }

        return $dto;
    }
}
