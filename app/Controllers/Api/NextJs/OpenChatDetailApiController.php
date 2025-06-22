<?php

declare(strict_types=1);

namespace App\Controllers\Api\NextJs;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\Statistics\StatisticsChartArrayService;
use App\Services\Statistics\Dto\StatisticsChartDto;
use App\Views\StatisticsViewUtility;
use App\Models\RecommendRepositories\RecommendRankingRepository;
use App\Services\RankingPosition\RankingPositionChartArrayService;
use App\Services\OpenChat\Enum\RankingType;
use Shared\MimimalCmsConfig;

class OpenChatDetailApiController
{
    public function detail(
        OpenChatPageRepositoryInterface $ocRepo,
        StatisticsChartArrayService $statisticsChartArrayService,
        StatisticsViewUtility $statisticsViewUtility,
        RankingPositionChartArrayService $rankingPositionChartService,
        int $open_chat_id
    ) {
        // Set CORS headers
        header('Access-Control-Allow-Origin: http://localhost:3000');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            return '';
        }

        if ($open_chat_id <= 0) {
            http_response_code(400);
            return response(['error' => 'Invalid OpenChat ID']);
        }

        try {
            // Get OpenChat data using repository - use getOpenChatByIdWithTag for tags
            if (MimimalCmsConfig::$urlRoot === '') {
                $oc = $ocRepo->getOpenChatByIdWithTag($open_chat_id);
            } else {
                $oc = $ocRepo->getOpenChatById($open_chat_id);
            }
            
            if (!$oc) {
                http_response_code(404);
                return response(['error' => 'OpenChat not found']);
            }

            // Get real statistics chart data
            $_statsDto = $statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);
            if (!$_statsDto) {
                // Fallback to empty statistics if no data
                $_statsDto = new StatisticsChartDto((new \DateTime('-1day'))->format('Y-m-d'), (new \DateTime('now'))->format('Y-m-d'));
            }

            // Apply member difference calculations
            $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($_statsDto);

            // Build member history from statistics data
            $memberHistory = [];
            if (!empty($_statsDto->date) && !empty($_statsDto->member)) {
                for ($i = 0; $i < count($_statsDto->date); $i++) {
                    if (isset($_statsDto->date[$i]) && isset($_statsDto->member[$i]) && $_statsDto->member[$i] !== null) {
                        $memberHistory[] = [
                            'date' => $_statsDto->date[$i],
                            'memberCount' => (int)$_statsDto->member[$i]
                        ];
                    }
                }
            }

            // Extract tags if available
            $tags = [];
            if (MimimalCmsConfig::$urlRoot === '') {
                // From getOpenChatByIdWithTag result
                if (!empty($oc['tag1'])) $tags[] = $oc['tag1'];
                if (!empty($oc['tag2'])) $tags[] = $oc['tag2'];
                if (!empty($oc['tag3'])) $tags[] = $oc['tag3'];
            } else {
                // For other languages, get tags from recommend repository
                $recommendRankingRepository = app(RecommendRankingRepository::class);
                $tags1 = $recommendRankingRepository->getRecommendTags([$open_chat_id]);
                $tags2 = array_filter($recommendRankingRepository->getOcTags([$open_chat_id]), fn($tag) => !in_array($tag, $tags1));
                $tags = array_merge($tags1, $tags2);
                $tags = array_slice($tags, 0, 3); // Limit to 3 tags
            }

            // Get real ranking position data
            $rankings = [
                'daily' => ['position' => null, 'change' => 0],
                'weekly' => ['position' => null, 'change' => 0],
                'total' => ['position' => null, 'change' => 0]
            ];
            
            // Get ranking history data for chart display
            $rankingHistoryData = [];
            $categoryValue = $oc['category'] ?? 0;
            
            try {
                // Get ranking position history using the same service as original controller
                $rankingPositionData = $rankingPositionChartService->getRankingPositionChartArray(
                    RankingType::Ranking, // Default to ranking type
                    $open_chat_id,
                    $categoryValue,
                    new \DateTime($_statsDto->startDate),
                    new \DateTime($_statsDto->endDate)
                );
                
                // Convert ranking position data to match member history length
                if (property_exists($rankingPositionData, 'position') && is_array($rankingPositionData->position)) {
                    $rankingHistoryData = $rankingPositionData->position;
                }
            } catch (\Exception $e) {
                // Fallback to empty array if ranking data fails
                error_log('Failed to get ranking data: ' . $e->getMessage());
                $rankingHistoryData = [];
            }

            // Format response
            $response = [
                'openChat' => [
                    'id' => (int)$oc['id'],
                    'name' => $oc['name'],
                    'description' => $oc['description_msg'] ?? '',
                    'memberCount' => (int)$oc['member'],
                    'category' => $this->getCategoryName((int)$oc['category']),
                    'tags' => $tags,
                    'imgUrl' => $oc['img_url'] ? "http://localhost:7000/oc-img/preview/default/{$oc['img_url']}_p.webp" : '',
                    'lastUpdate' => $oc['updated_at'],
                    'emblemUrl' => $oc['emblem'],
                    // Add member difference statistics
                    'memberDiff' => [
                        'daily' => [
                            'difference' => $oc['diff_member'] ?? 0,
                            'percentage' => $oc['percent_increase'] ?? 0.0
                        ],
                        'weekly' => [
                            'difference' => $oc['diff_member2'] ?? 0,
                            'percentage' => $oc['percent_increase2'] ?? 0.0
                        ]
                    ]
                ],
                'statistics' => [
                    'memberHistory' => $memberHistory,
                    'rankingHistory' => $rankingHistoryData, // Add ranking history for chart
                    'rankings' => $rankings,
                    'chartMetadata' => [
                        'startDate' => $_statsDto->startDate,
                        'endDate' => $_statsDto->endDate,
                        'totalDataPoints' => count($memberHistory),
                        'rankingDataPoints' => count($rankingHistoryData)
                    ]
                ]
            ];

            return response($response);

        } catch (\Exception $e) {
            http_response_code(500);
            return response(['error' => 'Internal server error', 'message' => $e->getMessage()]);
        }
    }

    private function getCategoryName(int $categoryId): string
    {
        $categories = AppConfig::OPEN_CHAT_CATEGORY[''] ?? [
            0 => 'すべて',
            2 => '友だち・雑談',
            5 => 'スポーツ',
            6 => 'ゲーム',
            7 => 'イベント・パーティー',
            8 => '音楽',
            11 => '地域・旅行',
            12 => '恋愛・結婚',
            16 => '写真・動画',
            17 => 'アニメ・漫画',
            18 => '学習',
            19 => '資格・転職',
            20 => 'ファッション・美容',
            22 => 'グルメ・料理',
            23 => 'ビジネス',
            24 => '動物・ペット',
            26 => '映画・ドラマ',
            27 => '健康・ダイエット',
            28 => '投資・副業',
            29 => 'ニュース・情報',
            30 => 'エンタメ・芸能',
            33 => 'ライフスタイル',
            37 => 'カルチャー',
            40 => 'テクノロジー',
            41 => 'クリエイティブ'
        ];

        return $categories[$categoryId] ?? 'その他';
    }
}