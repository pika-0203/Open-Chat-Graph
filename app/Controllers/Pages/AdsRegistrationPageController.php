<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\AdsRepositories\AdsRepository;
use App\Services\Admin\AdminAuthService;
use App\Views\Dto\AdsDto;
use Shared\Exceptions\NotFoundException;

class AdsRegistrationPageController
{
    function __construct(AdminAuthService $adminAuthService)
    {
        if (!$adminAuthService->auth()) {
            throw new NotFoundException();
        }
    }

    function index(AdsRepository $repo, int $id)
    {
        noStore();

        if ($id)
            $dto = $repo->getAdsById($id);
        else
            $dto = new AdsDto;

        $dtoArray = $repo->getAdsAll();

        return $dto ? view('admin/ads_register', compact('dto', 'dtoArray')) : false;
    }
}
