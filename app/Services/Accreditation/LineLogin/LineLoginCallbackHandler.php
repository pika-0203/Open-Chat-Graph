<?php

declare(strict_types=1);

namespace App\Services\Accreditation\LineLogin;

use Shared\Exceptions\UnauthorizedException;
use App\Services\Accreditation\LineLogin\LineLogin;

class LineLoginCallbackHandler
{
    function __construct(
        private LineLogin $line
    ) {
    }

    /**
     * LINEログインのコールバックを処理してレスポンスオブジェクトを返す
     */
    function handle(string $code, string $state): \stdClass
    {
        try {
            $response = $this->line->token($code, $state);
        } catch (UnauthorizedException $e) {
            throw new UnauthorizedException("LINEログインに失敗しました。\nデフォルトに設定されているブラウザから再度ログインしてください。\n" . "(" . $e->getMessage() . ")");
        }

        if (isset($response->error)) {
            throw new UnauthorizedException('LINEログイン エラー: ' . $response->error);
        }

        if (isset($response->access_token) === false) {
            throw new UnauthorizedException('LINEログイン エラー: アクセストークンがありません。');
        }

        $verifyResponse = $this->line->verify($response->access_token);

        if (isset($verifyResponse->error)) {
            throw new UnauthorizedException('LINEログイン 検証エラー: ' . $verifyResponse->error);
        }

        return $this->extractOpenIdFromIdToken($response);
    }

    /**
     * JWTをパースしてレスポンスオブジェクトにopen_idを追加する
     */
    private function extractOpenIdFromIdToken(\stdClass $response): \stdClass
    {
        $idTokenPayload = explode(".", $response->id_token)[1];
        $decodedPayload = json_decode(base64_decode($idTokenPayload));

        $response->open_id = $decodedPayload->sub;

        unset($response->id_token);
        return $response;
    }
}
