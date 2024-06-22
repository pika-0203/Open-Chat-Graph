<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Dto;

class QuizApiQuestionDto
{
    public int $id;
    public string $question;
    public string $answers;
    public string $explanation;
    public string $user_name;
    public string $room_name;
    public string $room_url;
    public string $type;

    /**
     * @var array{ a:string, b:string, c:string, d:string, correct:string }
     */
    public array $answersArray = [];

    /**
     * @var array{ explanation:string,source_url:string,source_title:string }
     */
    public array $explanationArray = [];

    function __construct()
    {
        $answers = json_decode($this->answers, true);
        if (is_array($answers))
            $this->answersArray = $answers;

        $explanation = json_decode($this->explanation, true);
        if (is_array($explanation))
            $this->explanationArray = $explanation;
    }
}
