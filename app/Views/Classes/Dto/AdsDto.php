<?php

declare(strict_types=1);

namespace App\Views\Dto;

class AdsDto
{
    public int $id = 0;
    public string $ads_title = '';
    public string $ads_sponsor_name = '';
    public string $ads_paragraph = '';
    public string $ads_href = '';
    public string $ads_img_url = '';
    public string $ads_tracking_url = '';
    public string $ads_title_button = '';
    public string $updated_at = '';

    function echoAdsElement()
    {
        viewComponent('ads/ads_element', ['dto' => $this]);
    }
}
