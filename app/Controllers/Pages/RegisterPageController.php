<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatPageRepositoryInterface;

class RegisterPageController
{
    function index(OpenChatPageRepositoryInterface $openChatRepository)
    {
        $view = [
            '_css' => ['room_list', 'site_header', 'site_footer'],
            '_meta' => meta()->setTitle('オープンチャットを登録'),
        ];

        $viewBeforeRegister = fn () => view('register_form_content', $view);
        $viewAfterRegister = fn ($requestOpenChat) => view('register_form_content', $view + compact('requestOpenChat'));

        if (!session()->has('id')) {
            return $viewBeforeRegister();
        }

        // セッションにIDのリクエストがある場合
        $requestOpenChat = $openChatRepository->getOpenChatById(session('id'));
        if (!$requestOpenChat) {
            return $viewBeforeRegister();
        }

        return $viewAfterRegister($requestOpenChat);
    }
}
