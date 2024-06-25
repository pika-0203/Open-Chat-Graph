<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Accreditation\AccreditationUserModel;
use App\Services\Accreditation\Enum\ExamType;
use App\Services\Accreditation\QuizApi\QuizApiService;
use DateTime;

class AccreditationPageController
{
    private const SINGLE_TIME = 45;

    function privacy()
    {
        return view('accreditation/privacy');
    }

    function index(QuizApiService $quizApiService, int $id)
    {
        $_css = getFilePath('style/quiz', 'main.*.css');
        $_js = getFilePath('js/quiz', 'main.*.js');

        if ($id) {
            $_argDto = $quizApiService->getSingleTopic($id, self::SINGLE_TIME);
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
            $description = 'オプチャ検定は、ガイドラインやルール、管理方法などについての知識を深める場所です。LINEオープンチャットを運営する際に必要な情報を楽しく学ぶことができます。';
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

    function today(
        QuizApiService $quizApiService,
        AccreditationUserModel $accreditationUserModel,
    ) {
        $_css = getFilePath('style/quiz', 'main.*.css');
        $_js = getFilePath('js/quiz', 'main.*.js');
        
        $fileName = 'accreditation/today_question.dat';
        $date = (new DateTime())->format('Y-m-d');

        $question = getUnserializedFile($fileName);
        if (!$question || $question['date'] !== $date) {
            $ids = $accreditationUserModel->getQuestionIdsAll();
            if (!$ids)
                return false;

            $_argDto = $quizApiService->getSingleTopic($ids[array_rand($ids)], self::SINGLE_TIME);
            $_argDto->questions[0]->question = "【今日の１問】 " . $_argDto->questions[0]->question;

            saveSerializedFile('accreditation/today_question.dat', compact('date', '_argDto'));
        } else {
            $_argDto = $question['_argDto'];
        }

        $title = '今日の一問｜オプチャ検定';
        $description = 'オプチャ検定は、ガイドラインやルール、管理方法などについての知識を深める場所です。LINEオープンチャットを運営する際に必要な情報を楽しく学ぶことができます。';
        $ogp = fileUrl("assets/quiz-today.png");
        $canonical = url("accreditation/today");

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
    }
}
