<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Enum;

enum ExamType: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';
}
