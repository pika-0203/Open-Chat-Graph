<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Dto;

class QuestionDto
{
    public int $id;
    public string $question;
    public string $answers;
    public string $explanation;
    public string $created_at;
    public int $user_id;
    public int $edit_user_id;
    public string $edited_at;
    public int $publishing;
    public string $type;
    public string $user_name;
    public int $is_admin_user;
    public string $edit_user_name;
    public int $is_admin_edit_user;
    public int $isPabulished;

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
