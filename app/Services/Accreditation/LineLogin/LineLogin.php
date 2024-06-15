<?php

declare(strict_types=1);

namespace App\Services\Accreditation\LineLogin;

use App\Config\LineLoginConfig;
use App\Exceptions\LineLoginException;

class LineLogin
{
    /**
     * Line Login/Register用のリンクを生成する。
     *
     * @param int $scope Line Login APIのスコープ  
    *             下記の値を足し合わせた値を指定する。  
    *             openid = 1  
    *             profile = 2  
    *             email = 4  
     * * **Example:** open_id, profile, emailのアクセスを要求する場合 `$scope = 7`
     *
     * @return string - Line Login/Register用のリンク
     */
    function getLink(int $scope = 1): string
    {
        $url = LineLoginConfig::AUTH_URL;
        $clientId = LineLoginConfig::CLIENT_ID;
        $redirectUrl = LineLoginConfig::REDIRECT_URL;
        $scope = $this->scope($scope);
        
        $state = hash('sha256', getIP() . time() . rand());;
        session(['state' => $state]);

        return $url . '?response_type=code&client_id=' . $clientId . '&redirect_uri=' . $redirectUrl . '&scope=' . $scope . '&state=' . $state;
    }

    /**
     * トークンを取得する
     *
     * @param string $code User authorization code.
     * @param string $state Randomized hash.
     * 
     * @return \stdClass レスポンスデータ
     * 
     * @throws LineLoginException セッションのstateが一致しない場合
     * @throws LineLoginException Curlリクエストが失敗した場合
     * @throws LineLoginException レスポンスのJSONデータの解析に失敗した場合
     */
    function token(string $code, string $state): \stdClass
    {
        if (session('state') !== $state) {
            throw new LineLoginException('stateが一致しません。');
        }

        session()->remove('state');

        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => LineLoginConfig::REDIRECT_URL,
            "client_id" => LineLoginConfig::CLIENT_ID,
            "client_secret" => LineLoginConfig::CLIENT_SECRET
        ];

        return $this->sendCurl(LineLoginConfig::TOKEN_URL, $header, 'POST', $data);
    }

    /**
     * アクセストークンを検証する
     *
     * @param string $token
     * 
     * @return \stdClass レスポンスデータ
     * 
     * @throws LineLoginException Curlリクエストが失敗した場合
     */
    function verify(string $token): \stdClass
    {
        $url = LineLoginConfig::VERIFYTOKEN_URL . '?access_token=' . $token;
        $response = $this->sendCurl($url, null, 'GET');
        return $response;
    }

    /**
     * アクセストークンを更新する
     *
     * @param string $token
     * 
     * @return \stdClass レスポンスデータ
     * 
     * @throws LineLoginException Curlリクエストが失敗した場合
     */
    function refresh($token)
    {
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "grant_type" => "refresh_token",
            "refresh_token" => $token,
            "client_id" => LineLoginConfig::CLIENT_ID,
            "client_secret" => LineLoginConfig::CLIENT_SECRET
        ];

        $response = $this->sendCURL(LineLoginConfig::TOKEN_URL, $header, 'POST', $data);
        return $response;
    }

    /**
     * アクセストークンを使用して、ユーザーのプロファイル情報を取得する
     *
     * @param string $token
     * 
     * @return \stdClass レスポンスデータ
     *
     * @throws LineLoginException Curlリクエストが失敗した場合。
     */
    function profile(string $token): \stdClass
    {
        $header = ['Authorization: Bearer ' . $token];
        return $this->sendCurl(LineLoginConfig::PROFILE_URL, $header, 'GET');
    }

    /**
     * アクセストークンを取り消す
     * 
     * @param string $token
     * 
     * @return \stdClass レスポンスデータ
     * 
     * @throws LineLoginException Curlリクエストが失敗した場合
     */
    public function revoke(string $token): \stdClass
    {
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "access_token" => $token,
            "client_id" => LineLoginConfig::CLIENT_ID,
            "client_secret" => LineLoginConfig::CLIENT_SECRET
        ];

        return $this->sendCURL(LineLoginConfig::REVOKE_URL, $header, 'POST', $data);
    }

    private function scope(int $scope): string
    {
        $list = ['openid', 'profile', 'email'];

        $scope = decbin($scope);

        while (strlen($scope) < 3) {
            $scope = '0' . $scope;
        }

        $scope = strrev($scope);

        foreach ($list as $key => $value) {
            if ($scope[$key] == 1) {
                if (isset($ret)) {
                    $ret = $ret . '%20' . $value;
                } else {
                    $ret = $value;
                }
            }
        }

        return $ret;
    }

    /**
     * @param string $url リクエストURL
     * @param array|null $header ヘッダー
     * @param string $type リクエストタイプ {POST|GET}
     * @param array|null $data リクエストデータ (GETリクエストを送信する場合はnullを指定可能)
     * 
     * @return \stdClass レスポンスデータ
     * 
     * @throws LineLoginException Curlリクエストが失敗した場合
     */
    private function sendCurl(string $url, ?array $header, string $type, ?array $data = null): \stdClass
    {
        $request = curl_init();

        if ($header !== null) {
            curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt_array($request, [
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => LineLoginConfig::VERIFYHOST,
            CURLOPT_SSL_VERIFYPEER => LineLoginConfig::VERIFYPEER,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1
        ]);

        if (strtoupper($type) === 'POST') {
            curl_setopt($request, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }

        $response = curl_exec($request);
        if ($response === false) {
            throw new LineLoginException(curl_error($request));
        }

        curl_close($request);

        $decoded_response = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new LineLoginException('レスポンスのJSONデータの解析に失敗しました。');
        }

        return $decoded_response;
    }
}
