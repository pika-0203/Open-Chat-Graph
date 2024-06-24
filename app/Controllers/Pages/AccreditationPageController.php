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
            $_argDto = $quizApiService->getSingleTopic($id, 45);
            if (!$_argDto)
                return false;

            $description = $_argDto->questions[0]->question
                . ' A.' . $_argDto->questions[0]->choices[0]
                . ' B.' . $_argDto->questions[0]->choices[1]
                . ' C.' . $_argDto->questions[0]->choices[2]
                . ' D.' . $_argDto->questions[0]->choices[3];

            $title = "{$_argDto->questions[0]->question}｜オプチャ検定｜Q.{$id}";
            $ogp = fileUrl("quiz-img/quiz_img_{$id}.webp");
            $canonical = url("accreditation?id={$id}");

            return view(
                'accreditation/quiz',
                compact(
                    '_argDto',
                    '_css',
                    '_js',
                    'title',
                    'description',
                    'ogp',
                    'canonical',
                )
            );
        } else {
            $_argDto = $quizApiService->getTopic(ExamType::Bronze, 10, 180);
            $_argDto_silver = $quizApiService->getTopic(ExamType::Silver, 20, 360);
            //$_argDto_gold = $quizApiService->getTopic(ExamType::Gold, 30, 540);

            $title = 'オプチャ検定｜練習問題';
            $description = 'オプチャ検定は、LINEオープンチャットのガイドラインやルール、管理方法などについての知識を深める検定サイトです。LINEオープンチャットを運営する際に必要な情報を楽しく学ぶことができます。';
            $ogp = fileUrl("assets/quiz-ogp.png");
            $canonical = url("accreditation");

            return view(
                'accreditation/quiz',
                compact(
                    '_argDto',
                    '_argDto_silver',
                    /* '_argDto_gold', */
                    '_css',
                    '_js',
                    'title',
                    'description',
                    'ogp',
                    'canonical',
                )
            );
        }
    }
}
