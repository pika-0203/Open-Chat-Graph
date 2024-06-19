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

    function index(QuizApiService $quizApiService)
    {
        $_argDto = $quizApiService->getTopic(ExamType::Bronze, 10, 180);

        $_css = getFilePath('style/quiz', 'main.*.css');
        $_js = getFilePath('js/quiz', 'main.*.js');

        return view(
            'accreditation/quiz',
            compact('_argDto', '_css', '_js')
        );
    }
}
