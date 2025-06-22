<?php

use App\Services\AiTrend\AiTrendDataDto;

/** @var AiTrendDataDto $aiTrendData */
$risingChats = $aiTrendData->risingChats;
$categoryTrends = $aiTrendData->categoryTrends;
$tagTrends = $aiTrendData->tagTrends;
$aiAnalysis = $aiTrendData->aiAnalysis;

?>

<!-- Tailwind CSS CDN - スコープ付きでこのコンポーネントのみに適用 -->
<link rel="stylesheet" href="https://cdn.tailwindcss.com" />

<style>
    /* このコンポーネント専用のCSS分離スコープ */
    .ai-trend-isolated {
        /* 外部CSSからの影響をリセット */
        all: initial;
        font-family: "Helvetica Neue",
            Arial,
            "Hiragino Kaku Gothic ProN",
            "Hiragino Sans",
            sans-serif;
        line-height: 1.5;
        -webkit-text-size-adjust: 100%;
        -moz-tab-size: 4;
        tab-size: 4;

        /* Tailwind CSSのベースリセットを適用 */
        box-sizing: border-box;
        border: 0 solid #e5e7eb;
    }

    .ai-trend-isolated *,
    .ai-trend-isolated ::before,
    .ai-trend-isolated ::after {
        box-sizing: border-box;
        border: 0 solid #e5e7eb;
    }

    .ai-trend-isolated a {
        color: inherit;
        text-decoration: inherit;
    }

    .ai-trend-isolated h1,
    .ai-trend-isolated h2,
    .ai-trend-isolated h3,
    .ai-trend-isolated h4,
    .ai-trend-isolated h5,
    .ai-trend-isolated h6 {
        font-size: inherit;
        font-weight: inherit;
        margin: 0;
    }

    .ai-trend-isolated p {
        margin: 0;
    }

    .ai-trend-isolated button {
        background-color: transparent;
        background-image: none;
        padding: 0;
        line-height: inherit;
        color: inherit;
        border: 0;
        font-family: inherit;
        font-size: 100%;
        font-weight: inherit;
        margin: 0;
    }

    /* このコンポーネント内でTailwindクラスが正常に動作するようにする */
    .ai-trend-isolated .bg-white {
        background-color: rgb(255 255 255);
    }

    .ai-trend-isolated .border {
        border-width: 1px;
    }

    .ai-trend-isolated .border-gray-100 {
        border-color: rgb(243 244 246);
    }

    .ai-trend-isolated .border-gray-200 {
        border-color: rgb(229 231 235);
    }

    .ai-trend-isolated .border-blue-100 {
        border-color: rgb(219 234 254);
    }

    .ai-trend-isolated .border-blue-600 {
        border-color: rgb(37 99 235);
    }

    .ai-trend-isolated .border-2 {
        border-width: 2px;
    }

    .ai-trend-isolated .hover\:bg-blue-600:hover {
        background-color: rgb(37 99 235);
    }

    .ai-trend-isolated .hover\:text-white:hover {
        color: rgb(255 255 255);
    }

    .ai-trend-isolated .rounded-xl {
        border-radius: 0.75rem;
    }

    .ai-trend-isolated .rounded-lg {
        border-radius: 0.5rem;
    }

    .ai-trend-isolated .rounded-full {
        border-radius: 9999px;
    }

    .ai-trend-isolated .shadow-sm {
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .ai-trend-isolated .shadow-md {
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .ai-trend-isolated .shadow-lg {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .ai-trend-isolated .border-blue-600 {
        border-color: rgb(37 99 235);
    }

    .ai-trend-isolated .border-2 {
        border-width: 2px;
    }

    .ai-trend-isolated .hover\:bg-blue-600:hover {
        background-color: rgb(37 99 235);
    }

    .ai-trend-isolated .hover\:text-white:hover {
        color: rgb(255 255 255);
    }

    .ai-trend-isolated .hover\:shadow-md:hover {
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .ai-trend-isolated .hover\:shadow-lg:hover {
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .ai-trend-isolated .transition-shadow {
        transition-property: box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .ai-trend-isolated .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .ai-trend-isolated .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .ai-trend-isolated .transition-transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .ai-trend-isolated .duration-200 {
        transition-duration: 200ms;
    }

    .ai-trend-isolated .duration-300 {
        transition-duration: 300ms;
    }

    .ai-trend-isolated .p-2 {
        padding: 0.5rem;
    }

    .ai-trend-isolated .p-4 {
        padding: 1rem;
    }

    .ai-trend-isolated .p-5 {
        padding: 1.25rem;
    }

    .ai-trend-isolated .p-6 {
        padding: 1.5rem;
    }

    .ai-trend-isolated .px-3 {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .ai-trend-isolated .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .ai-trend-isolated .py-1 {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    .ai-trend-isolated .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .ai-trend-isolated .mb-2 {
        margin-bottom: 0.5rem;
    }

    .ai-trend-isolated .mb-5 {
        margin-bottom: 1.25rem;
    }

    .ai-trend-isolated .mb-6 {
        margin-bottom: 1.5rem;
    }

    .ai-trend-isolated .mb-8 {
        margin-bottom: 2rem;
    }

    .ai-trend-isolated .mr-2 {
        margin-right: 0.5rem;
    }

    .ai-trend-isolated .mt-1 {
        margin-top: 0.25rem;
    }

    .ai-trend-isolated .flex {
        display: flex;
    }

    .ai-trend-isolated .inline-block {
        display: inline-block;
    }

    .ai-trend-isolated .grid {
        display: grid;
    }

    .ai-trend-isolated .flex-col {
        flex-direction: column;
    }

    .ai-trend-isolated .flex-wrap {
        flex-wrap: wrap;
    }

    .ai-trend-isolated .items-start {
        align-items: flex-start;
    }

    .ai-trend-isolated .items-center {
        align-items: center;
    }

    .ai-trend-isolated .justify-between {
        justify-content: space-between;
    }

    .ai-trend-isolated .gap-2 {
        gap: 0.5rem;
    }

    .ai-trend-isolated .gap-3 {
        gap: 0.75rem;
    }

    .ai-trend-isolated .gap-4 {
        gap: 1rem;
    }

    .ai-trend-isolated .gap-5 {
        gap: 1.25rem;
    }

    .ai-trend-isolated .space-y-3> :not([hidden])~ :not([hidden]) {
        margin-top: 0.75rem;
    }

    .ai-trend-isolated .space-y-4> :not([hidden])~ :not([hidden]) {
        margin-top: 1rem;
    }

    .ai-trend-isolated .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .ai-trend-isolated .w-2 {
        width: 0.5rem;
    }

    .ai-trend-isolated .w-3 {
        width: 0.75rem;
    }

    .ai-trend-isolated .w-10 {
        width: 2.5rem;
    }

    .ai-trend-isolated .h-2 {
        height: 0.5rem;
    }

    .ai-trend-isolated .h-3 {
        height: 0.75rem;
    }

    .ai-trend-isolated .h-10 {
        height: 2.5rem;
    }

    .ai-trend-isolated .min-w-0 {
        min-width: 0px;
    }

    .ai-trend-isolated .flex-1 {
        flex: 1 1 0%;
    }

    .ai-trend-isolated .flex-shrink-0 {
        flex-shrink: 0;
    }

    .ai-trend-isolated .transform {
        transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
    }

    .ai-trend-isolated .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ai-trend-isolated .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }

    .ai-trend-isolated .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }

    .ai-trend-isolated .text-lg {
        font-size: 1.125rem;
        line-height: 1.75rem;
    }

    .ai-trend-isolated .text-xl {
        font-size: 1.25rem;
        line-height: 1.75rem;
    }

    .ai-trend-isolated .text-2xl {
        font-size: 1.5rem;
        line-height: 2rem;
    }

    .ai-trend-isolated .font-medium {
        font-weight: 500;
    }

    .ai-trend-isolated .font-semibold {
        font-weight: 600;
    }

    .ai-trend-isolated .font-bold {
        font-weight: 700;
    }

    .ai-trend-isolated .uppercase {
        text-transform: uppercase;
    }

    .ai-trend-isolated .tracking-wide {
        letter-spacing: 0.025em;
    }

    .ai-trend-isolated .leading-relaxed {
        line-height: 1.625;
    }

    .ai-trend-isolated .text-white {
        color: rgb(255 255 255);
    }

    .ai-trend-isolated .text-gray-500 {
        color: rgb(107 114 128);
    }

    .ai-trend-isolated .text-gray-600 {
        color: rgb(75 85 99);
    }

    .ai-trend-isolated .text-gray-700 {
        color: rgb(55 65 81);
    }

    .ai-trend-isolated .text-gray-900 {
        color: rgb(17 24 39);
    }

    .ai-trend-isolated .text-blue-600 {
        color: rgb(37 99 235);
    }

    .ai-trend-isolated .text-blue-700 {
        color: rgb(29 78 216);
    }

    .ai-trend-isolated .text-green-600 {
        color: rgb(22 163 74);
    }

    .ai-trend-isolated .text-green-700 {
        color: rgb(21 128 61);
    }

    .ai-trend-isolated .text-orange-600 {
        color: rgb(234 88 12);
    }

    .ai-trend-isolated .text-purple-600 {
        color: rgb(147 51 234);
    }

    .ai-trend-isolated .text-indigo-600 {
        color: rgb(79 70 229);
    }

    .ai-trend-isolated .bg-blue-50 {
        background-color: rgb(239 246 255);
    }

    .ai-trend-isolated .bg-blue-100 {
        background-color: rgb(219 234 254);
    }

    .ai-trend-isolated .bg-cyan-50 {
        background-color: rgb(236 254 255);
    }

    .ai-trend-isolated .bg-gray-50 {
        background-color: rgb(249 250 251);
    }

    .ai-trend-isolated .bg-green-100 {
        background-color: rgb(220 252 231);
    }

    .ai-trend-isolated .bg-green-400 {
        background-color: rgb(74 222 128);
    }

    .ai-trend-isolated .bg-green-500 {
        background-color: rgb(34 197 94);
    }

    .ai-trend-isolated .bg-orange-100 {
        background-color: rgb(255 237 213);
    }

    .ai-trend-isolated .bg-purple-100 {
        background-color: rgb(243 232 255);
    }

    .ai-trend-isolated .bg-indigo-50 {
        background-color: rgb(238 242 255);
    }

    .ai-trend-isolated .bg-indigo-100 {
        background-color: rgb(224 231 255);
    }

    .ai-trend-isolated .bg-yellow-500 {
        background-color: rgb(234 179 8);
    }

    .ai-trend-isolated .bg-red-500 {
        background-color: rgb(239 68 68);
    }

    .ai-trend-isolated .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops));
    }

    .ai-trend-isolated .bg-gradient-to-br {
        background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
    }

    .ai-trend-isolated .from-blue-50 {
        --tw-gradient-from: rgb(239 246 255) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(239 246 255 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .ai-trend-isolated .from-blue-500 {
        --tw-gradient-from: rgb(59 130 246) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(59 130 246 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .ai-trend-isolated .from-blue-600 {
        --tw-gradient-from: rgb(37 99 235) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(37 99 235 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .ai-trend-isolated .to-indigo-50 {
        --tw-gradient-to: rgb(238 242 255) var(--tw-gradient-to-position);
    }

    .ai-trend-isolated .to-cyan-50 {
        --tw-gradient-to: rgb(236 254 255) var(--tw-gradient-to-position);
    }

    .ai-trend-isolated .to-purple-600 {
        --tw-gradient-to: rgb(147 51 234) var(--tw-gradient-to-position);
    }

    .ai-trend-isolated .to-purple-700 {
        --tw-gradient-to: rgb(126 34 206) var(--tw-gradient-to-position);
    }

    .ai-trend-isolated .hover\:bg-blue-50:hover {
        background-color: rgb(239 246 255);
    }

    .ai-trend-isolated .hover\:border-gray-200:hover {
        border-color: rgb(229 231 235);
    }

    .ai-trend-isolated .hover\:border-blue-200:hover {
        border-color: rgb(191 219 254);
    }

    .ai-trend-isolated .hover\:text-blue-600:hover {
        color: rgb(37 99 235);
    }

    .ai-trend-isolated .hover\:text-gray-900:hover {
        color: rgb(17 24 39);
    }

    .ai-trend-isolated .hover\:from-blue-600:hover {
        --tw-gradient-from: rgb(37 99 235) var(--tw-gradient-from-position);
        --tw-gradient-to: rgb(37 99 235 / 0) var(--tw-gradient-to-position);
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
    }

    .ai-trend-isolated .hover\:to-purple-700:hover {
        --tw-gradient-to: rgb(126 34 206) var(--tw-gradient-to-position);
    }

    .ai-trend-isolated .hover\:scale-105:hover {
        --tw-scale-x: 1.05;
        --tw-scale-y: 1.05;
        transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
    }

    .ai-trend-isolated .group:hover .group-hover\:bg-blue-50 {
        background-color: rgb(239 246 255);
    }

    .ai-trend-isolated .group:hover .group-hover\:text-blue-600 {
        color: rgb(37 99 235);
    }

    .ai-trend-isolated .group:hover .group-hover\:text-gray-900 {
        color: rgb(17 24 39);
    }

    .ai-trend-isolated .group:hover .group-hover\:scale-105 {
        --tw-scale-x: 1.05;
        --tw-scale-y: 1.05;
        transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
    }

    .ai-trend-isolated .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    /* レスポンシブ対応 */
    @media (min-width: 640px) {
        .ai-trend-isolated .sm\:flex-row {
            flex-direction: row;
        }

        .ai-trend-isolated .sm\:items-center {
            align-items: center;
        }
    }

    @media (min-width: 1024px) {
        .ai-trend-isolated .lg\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<section class="ai-trend-isolated bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-3 group">
                <h2 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">AIトレンド分析</h2>
                <span class="text-2xl animate-pulse">🤖</span>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 px-4 py-2 rounded-full text-sm font-medium border border-blue-100 shadow-sm">
            <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
            リアルタイム分析
        </div>
    </div>

    <?php if (!empty($aiAnalysis->summary)): ?>
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-100 rounded-xl p-5 mb-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-start gap-4">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <span class="text-blue-600 text-xl">💡</span>
                </div>
                <p class="text-gray-700 leading-relaxed font-medium"><?php echo htmlspecialchars($aiAnalysis->summary) ?></p>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($aiAnalysis->insights)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-orange-100 p-2 rounded-lg">
                    <span class="text-orange-600">📊</span>
                </div>
                分析結果
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <?php foreach ($aiAnalysis->insights as $insight): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-5 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="flex items-start gap-4">
                            <div class="bg-gray-50 group-hover:bg-blue-50 p-2 rounded-lg transition-colors duration-200">
                                <span class="text-xl"><?php echo $insight['icon'] ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-200"><?php echo htmlspecialchars($insight['title']) ?></h4>
                                <p class="text-gray-600 text-sm leading-relaxed"><?php echo htmlspecialchars($insight['content']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($risingChats)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-green-100 p-2 rounded-lg">
                    <span class="text-green-600">🚀</span>
                </div>
                急成長チャット
            </h3>
            <div class="space-y-3">
                <?php foreach (array_slice($risingChats, 0, 5) as $index => $chat): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-4 hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-lg group-hover:scale-105 transition-transform duration-200">
                                <?php echo $index + 1 ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="<?php echo url('oc/' . $chat['id']) ?>" class="text-gray-900 font-medium hover:text-blue-600 transition-colors duration-200 block mb-2 truncate group-hover:text-blue-600">
                                    <?php echo htmlspecialchars($chat['name']) ?>
                                </a>
                                <div class="flex items-center gap-2">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                                        +<?php echo number_format($chat['diff_member']) ?>人
                                    </span>
                                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($tagTrends)): ?>
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-purple-100 p-2 rounded-lg">
                    <span class="text-purple-600">🏷️</span>
                </div>
                トレンドタグ
            </h3>
            <div class="flex flex-wrap gap-3">
                <?php foreach (array_slice($tagTrends, 0, 12) as $tag): ?>
                    <a href="<?php echo url('recommend?tag=' . urlencode($tag['tag'])) ?>" class="bg-white border-2 border-blue-600 hover:bg-blue-600 text-blue-600 hover:text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 transform">
                        #<?php echo htmlspecialchars($tag['tag']) ?>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (!empty($aiAnalysis->predictions)): ?>
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-3">
                <div class="bg-indigo-100 p-2 rounded-lg">
                    <span class="text-indigo-600">🔮</span>
                </div>
                予測
            </h3>
            <div class="space-y-4">
                <?php foreach ($aiAnalysis->predictions as $prediction): ?>
                    <div class="bg-white border border-gray-100 rounded-xl p-5 hover:shadow-md transition-all duration-200 group">
                        <div class="flex items-start gap-4">
                            <div class="mt-1 flex-shrink-0">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full inline-block <?php
                                                                                    if ($prediction['confidence'] === 'high') echo 'bg-green-500 animate-pulse';
                                                                                    elseif ($prediction['confidence'] === 'medium') echo 'bg-yellow-500';
                                                                                    else echo 'bg-red-500';
                                                                                    ?>"></span>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                        <?php
                                        if ($prediction['confidence'] === 'high') echo '高信頼度';
                                        elseif ($prediction['confidence'] === 'medium') echo '中信頼度';
                                        else echo '低信頼度';
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <p class="text-gray-700 leading-relaxed group-hover:text-gray-900 transition-colors duration-200"><?php echo htmlspecialchars($prediction['content']) ?></p>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>
</section>