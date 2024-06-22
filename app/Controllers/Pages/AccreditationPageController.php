<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Accreditation\Enum\ExamType;
use App\Services\Accreditation\QuizApi\QuizApiService;

class AccreditationPageController
{
    function privacy()
    {
        return view('accreditation/privacy');
    }

    function index(QuizApiService $quizApiService, int $id)
    {
        $_css = getFilePath('style/quiz', 'main.*.css');
        $_js = getFilePath('js/quiz', 'main.*.js');

        if ($id) {
            $_argDto = $quizApiService->getSingleTopic($id, 30);
            if (!$_argDto)
                return false;

            $title = "オプチャ検定｜Q.{$id}";
            $description = $_argDto->questions[0]->question;
            $ogp = fileUrl("quiz-img/quiz_img_{$id}.webp");

            return view(
                'accreditation/quiz',
                compact('_argDto', '_css', '_js', 'title', 'description', 'ogp')
            );
        } else {
            $_argDto = $quizApiService->getTopic(ExamType::Bronze, 10, 180);

            $title = 'オプチャ検定｜練習問題';
            $description = 'オプチャ検定の練習問題に挑戦しよう！';
            $ogp = fileUrl("assets/quiz-ogp.png");

            return view(
                'accreditation/quiz',
                compact('_argDto', '_css', '_js', 'title', 'description', 'ogp')
            );
        }
    }
}
