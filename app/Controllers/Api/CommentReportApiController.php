<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\CommentRepositories\CommentListRepositoryInterface;
use App\Models\CommentRepositories\CommentLogRepositoryInterface;
use App\Models\CommentRepositories\Enum\CommentLogType;
use App\Services\Admin\AdminTool;
use App\Services\Auth\AuthInterface;
use App\Services\Auth\GoogleReCaptcha;

class CommentReportApiController
{
    function index(
        CommentListRepositoryInterface $commentListRepository,
        CommentLogRepositoryInterface $commentLogRepository,
        AuthInterface $auth,
        GoogleReCaptcha $googleReCaptcha,
        string $token,
        int $comment_id
    ) {
        $score = $googleReCaptcha->validate($token, 0.3);
        $report_user_id = $auth->loginCookieUserId();

        $comment = $commentListRepository->findCommentById($comment_id);
        if (!$comment) {
            return false;
        }

        $existsReport = $commentLogRepository->findReportLog(
            $comment_id,
            CommentLogType::Report,
            json_encode(compact('report_user_id'))
        );

        if ($existsReport) {
            return response(['success' => false]);
        }

        $logId = $commentLogRepository->addLog(
            $comment_id,
            CommentLogType::Report,
            getIP(),
            getUA(),
            json_encode(compact('report_user_id'))
        );

        $comment['report_user_hash'] = base62Hash($report_user_id, 'fnv132');
        $comment['report_log_id'] = $logId;
        $comment['google_recaptcha_score'] = $score;

        AdminTool::sendLineNofity("通報: " . json_encode($comment, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return response(['success' => true]);
    }
}
