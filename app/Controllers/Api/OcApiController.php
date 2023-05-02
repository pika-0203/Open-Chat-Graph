<?php

use App\Services\OpenChat\AddOpenChat;

class OcApiController
{
    public function index(AddOpenChat $openChat, string $url)
    {
        $result = $openChat->add($url);

        return redirect()
            ->with($result);
    }
}
