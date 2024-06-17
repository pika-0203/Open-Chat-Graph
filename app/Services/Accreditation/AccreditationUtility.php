<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use App\Services\Accreditation\Dto\QuestionDto;

class AccreditationUtility
{
    static function isQuestionEditable(QuestionDto $q, int $myId, bool $isAdmin): bool
    {
        return ($myId === $q->user_id && !$q->publishing
            && (!$q->edit_user_id || $q->edit_user_id === $myId)
        )
            || $isAdmin;
    }

    /**
     * @return array{ error:bool,title:string,message:string }
     */
    static function getPageTitle($url): array
    {
        $res = fn ($error, $title, $message) => compact('error', 'title', 'message');

        if(filter_var($url, FILTER_VALIDATE_URL) === false || !preg_match('#\Ahttps?://#', $url)) {
            return $res(true, '', 'invalid URL');
        }

        try {
            $html = file_get_contents($url);
        } catch (\Throwable $e) {
            return $res(true, '', $e->getMessage());
        }

        if (preg_match('/<title.*?>(.*?)<\/title>/', $html, $matches)) {
            return $res(false, htmlspecialchars_decode($matches[1]), '');
        } else {
            return $res(true, '', 'invalid title');
        }
    }
}
