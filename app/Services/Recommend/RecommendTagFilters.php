<?php

declare(strict_types=1);

namespace App\Services\Recommend;

class RecommendTagFilters
{
    const RecommendPageTagFilter = [
        '営業',
        '対荒らし',
        'スタンプ',
        'Instagram（インスタ）',
        '著作権（知的財産権）',
        "東京",
        "北海道",
        "神奈川",
        "愛知",
        "京都",
        "下ネタ",
        "大阪",
        "兵庫",
        "福岡",
        "新歓",
        "関東",
        "サークル",
        "セミナー",
        "関西",
        "九州",
        "大学",
        "沖縄",
        "即承認",
        "海外",
        "全国 雑談",
        "70代",
        '新入生',
        "60代",
        "50代",
        "加工",
        "フェス",
        "自衛隊",
        "レスバ",
        "unistyle",
        "jobhunt",
        "邦画",
    ];

    const FilteredTagSort = [
        'ガンダム' => ['ガンプラ'],
        'ガンプラ' => ['ガンダム'],
        'しりとり' => ['ゲーム', '雑談'],
        '大人' => ['20代', '30代', '40代', '50代', '60代', '70代', '全国 雑談', '即承認', '雑談', 'ライブトーク', '愚痴', 'スマホ'],
        '20代' => ['大人', '30代', '40代', '50代', '60代', '70代', '全国 雑談', '即承認', '雑談', 'ライブトーク', '宣伝'],
        '30代' => ['大人', '20代', '40代', '50代', '60代', '70代', '全国 雑談', '即承認', '雑談', 'ライブトーク', '宣伝'],
        '40代' => ['大人', '30代', '50代', '60代', '70代', '全国 雑談', '即承認', '雑談', 'ライブトーク', 'オプチャ サポート', '宣伝'],
        '50代' => ['大人', '40代', '60代', '70代', '全国 雑談', '即承認', '雑談', 'ライブトーク', 'オプチャ サポート', '宣伝', 'NISA'],
        '60代' => ['大人', '40代', '50代', '70代', '全国 雑談', '即承認', '雑談', 'ライブトーク', 'オプチャ サポート', '宣伝', 'NISA'],
        '70代' => ['大人', '40代', '50代', '60代', '全国 雑談', '即承認', '雑談', 'ライブトーク', 'オプチャ サポート', '宣伝', 'NISA'],
        '全国 雑談' => ['大人', '20代', '30代', '40代', '50代', '60代', '70代', '即承認', '雑談', '雑談', 'ライブトーク', 'オプチャ サポート', '愚痴', '宣伝'],
        'オフ会' => ['大人', 'リア充', 'ネッ友', '雑談', 'ライブトーク', 'オプチャ サポート', '愚痴', '宣伝'],
        '即承認' => ['大人', '20代', '30代', '40代', '50代', '60代', '70代', '全国 雑談', '雑談', 'ライブトーク', 'オプチャ サポート'],
        'アニメ' => ['アニソン'],
        'ゲーム' => ['マインクラフト（マイクラ）', '原神', 'プロ野球スピリッツA（プロスピA）', 'eスポーツ', 'ONE PIECE バウンティラッシュ', 'ポケットモンスター（ポケモン）', 'フォートナイト（フォトナ）'],
        'ディズニー' => ['ディズニー ツムツム', 'ツイステッドワンダーランド'],
        'ブロスタ' => ['ロブロックス', 'ゲーム', 'カーパーキング', 'マインクラフト（マイクラ）', 'キノコ伝説'],
        'ロブロックス' => ['ブロスタ', 'ゲーム', 'カーパーキング', 'マインクラフト（マイクラ）', 'キノコ伝説'],
        'キノコ伝説' => ['ブロスタ', 'ゲーム', 'カーパーキング', 'マインクラフト（マイクラ）'],
        'マインクラフト（マイクラ）' => ['ゲーム', '妖怪ウォッチ ぷにぷに', 'ONE PIECE バウンティラッシュ', 'カーパーキング', 'ロブロックス', 'ブロスタ'],
        'カーパーキング' => ['ゲーム', 'ロブロックス', 'ブロスタ', 'マインクラフト（マイクラ）'],
        'プロ野球スピリッツA（プロスピA）' => ['ゲーム'],
        'あつまれ どうぶつの森（あつ森）' => ['ゲーム'],
        'フォートナイト（フォトナ）' => ['ゲーム', '原神', 'ディズニー ツムツム', 'マインクラフト（マイクラ）'],
        'ONE PIECE バウンティラッシュ' => ['ゲーム'],
        'ぷにぷに' => ['ゲーム'],
        'モンスターストライク（モンスト）' => ['ゲーム'],
        'ディズニー ツムツム' => ['ディズニー', 'ツイステッドワンダーランド', 'ゲーム', 'スマホ', '原神'],
        'ツイステッドワンダーランド' => ['ディズニー', 'ディズニー ツムツム', 'ゲーム', '原神'],
        '原神' => ['ゲーム', '崩壊スターレイル'],
        '崩壊スターレイル' => ['ゲーム', '原神'],
        'スプラトゥーン' => ['ゲーム'],
        'eスポーツ' => ['ストリートファイター6（スト6）', 'ゲーム'],
        'ストリートファイター6（スト6）' => ['eスポーツ', 'ゲーム'],
        'ポケットモンスター（ポケモン）' => ['ポケモン スカーレット・バイオレット（ポケモンSV）', 'ポケモンGO', 'ポケモンスリープ', 'ポケモンユナイト', 'ポケモンカード（ポケカ）', 'ゲーム'],
        'ポケモンGO' => ['ポケモン スカーレット・バイオレット（ポケモンSV）', 'ポケットモンスター（ポケモン）', 'ポケモンスリープ', 'ポケモンユナイト', 'ポケモンカード（ポケカ）'],
        'ポケモン スカーレット・バイオレット（ポケモンSV）' => ['ポケモンGO', 'ポケットモンスター（ポケモン）', 'ポケモンスリープ', 'ポケモンユナイト', 'ポケモンカード（ポケカ）'],
        'スマホ' => ['SNS', '写真投稿', 'ゲーム', 'パソコン・ガジェット', '編集', 'イラスト', 'ポイ活', 'カメラ', 'オプチャ サポート', 'ONE PIECE バウンティラッシュ'],
        'オプチャ サポート' => ['ゲーム', 'パソコン 相談', 'スマホ', '雑談', '宣伝', '大人'],
        'パソコン・ガジェット' => ['パソコン 相談', 'ゲーム'],
        'ポケモンカード（ポケカ）' => ['トレーディングカード（トレカ）'],
        'トレーディングカード（トレカ）' => ['ポケモンカード（ポケカ）', 'ワンピースカード', 'せどり', 'ゲーム'],
        'にじさんじ' => ['にじさんじなりきり'],
        'プロセカなりきり' => ['プロジェクトセカイ（プロセカ）', 'なりきり'],
        'ボイメで歌（歌リレー）' => ['ライブトーク', '歌ってみた', '歌い手のトークルーム', 'VOCALOID（ボーカロイド／ボカロ）', 'イケボ', 'カラオケ', 'ボイストレーニング（ボイトレ）', 'アニソン'],
        '生成AI・ChatGPT' => ['画像生成AI・AIイラスト'],
        'メンタルヘルス' => ['雑談', '愚痴', '不登校', '発達障害', '精神疾患', 'HSP', '恋愛相談'],
        'HSP' => ['雑談', '愚痴', '不登校', '発達障害', '精神疾患', 'メンタルヘルス', '恋愛相談'],
        'カウンセリング' => ['雑談', '愚痴', '不登校', '発達障害', '精神疾患', 'HSP', 'メンタルヘルス', '恋愛相談'],
        '発達障害' => ['雑談', '愚痴', '不登校', '精神疾患', 'HSP', 'メンタルヘルス', '恋愛相談'],
        'オリキャラ恋愛' => ['オリキャラ', 'オリキャラ BL', 'なりきり'],
        '恋愛相談' => ['恋愛', '垢抜け', '失恋', 'メンタルヘルス', '雑談', '学生限定', '愚痴', '恋バナ', 'ネッ友', 'リア充'],
        '恋バナ' => ['恋愛', '垢抜け', '失恋', '恋愛相談', 'メンタルヘルス', '雑談', '学生限定', '愚痴', 'ネッ友', 'リア充'],
        '恋愛' => ['恋愛', 'メンタルヘルス', '雑談', '学生限定', '愚痴', '恋バナ', 'ネッ友', 'リア充'],
        'ライブトーク' => ['恋愛', '恋バナ', '失恋', 'メンタルヘルス', '雑談', '学生限定', '愚痴', 'ボイメで歌（歌リレー）'],
        '失恋' => ['メンタルヘルス', '雑談', '学生限定', '恋愛', '恋愛相談', '恋話', '愚痴'],
        '学生限定' => ['小学生・中学生限定', '中学生・高校生限定', '中学生限定', '高校生限定', '中学生', '高校生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '小学生・中学生限定' => ['学生限定', '中学生・高校生限定', '中学生限定', '中学生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '中学生・高校生限定' => ['小学生・中学生限定', '学生限定', '中学生限定', '高校生限定', '中学生', '高校生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '中学生限定' => ['小学生・中学生限定', '学生限定', '中学生・高校生限定', '中学生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '高校生限定' => ['中学生・高校生限定', '学生限定', '高校生', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '中学生' => ['小学生・中学生限定', '中学生限定', '中学生・高校生限定', '女子限定', '雑談', '恋バナ', 'レスバ', '下ネタ'],
        '高校生' => ['中学生・高校生限定', '学生限定', '高校生限定', '雑談', 'ボイメで歌（歌リレー）', '恋バナ'],
        '雑談' => ['学生限定', 'ボイメで歌（歌リレー）', '愚痴', '恋バナ', 'メンタルヘルス', '恋愛相談', '大喜利', 'ライブトーク', '大人', '宣伝', 'ネッ友', 'リア充', 'ゲーム'],
        '宣伝' => ['学生限定', 'ボイメで歌（歌リレー）', '愚痴', '恋バナ', 'メンタルヘルス', '恋愛相談', 'ライブトーク', '大人', '雑談', 'オプチャ サポート'],
        'ライブトーク' => ['学生限定', 'ボイメで歌（歌リレー）', '愚痴', 'メンタルヘルス', '恋愛', '宣伝', '大人', '雑談'],
        'ボイメで歌（歌リレー）' => ['学生限定', '恋愛', '雑談', 'ゲーム'],
        '不登校' => ['メンタルヘルス', 'ネッ友', 'うつ病', '発達障害', 'HSP'],
        'メルカリ' => ['せどり', '副業', 'ポイ活', 'お金', 'TEMU', 'SHEIN', 'クーポン・無料配布', 'スタバ', 'KAUCHE（カウシェ）', '仮想通貨', 'FX', '億り人'],
        'KAUCHE（カウシェ）' => ['せどり', '副業', 'ポイ活', 'お金', 'TEMU', 'SHEIN', 'クーポン・無料配布', 'スタバ', 'メルカリ', '仮想通貨', 'FX', '億り人'],
        'TEMU' => ['せどり', '副業', 'ポイ活', 'お金', 'KAUCHE（カウシェ）', 'SHEIN', 'クーポン・無料配布', 'スタバ', 'メルカリ', '仮想通貨', 'FX', '億り人'],
        'SHEIN' => ['せどり', '副業', 'ポイ活', 'お金', 'KAUCHE（カウシェ）', 'TEMU', 'クーポン・無料配布', 'スタバ', 'メルカリ', '仮想通貨', 'FX', '億り人'],
        'せどり' => ['副業', 'お金', 'ポイ活', 'メルカリ', '仮想通貨', '億り人', 'ふるさと納税', 'TEMU', 'SHEIN', 'KAUCHE（カウシェ）', 'トレーディングカード（トレカ）'],
        '副業' => ['せどり', 'お金', 'ポイ活', 'クーポン・無料配布', 'メルカリ', '仮想通貨', '投資', 'NISA', '億り人', 'FX', 'KAUCHE（カウシェ）'],
        'お金' => ['せどり', '副業', 'ポイ活', 'クーポン・無料配布', 'TEMU', 'SHEIN', 'メルカリ', '仮想通貨', '投資', 'NISA', '億り人', 'KAUCHE（カウシェ）', 'FX'],
        '節約' => ['ふるさと納税', '節税', 'TEMU', 'SHEIN', 'メルカリ', 'KAUCHE（カウシェ）', 'ポイ活', 'クーポン・無料配布', 'NISA', '億り人', 'お金'],
        'クーポン・無料配布' => ['TEMU', 'SHEIN', 'メルカリ', 'KAUCHE（カウシェ）', 'ポイ活', 'ふるさと納税', 'NISA', '億り人', 'スタバ', 'お金'],
        'ポイ活' => ['せどり', '副業', 'ふるさと納税', 'クーポン・無料配布', 'TEMU', 'SHEIN', 'メルカリ', 'NISA', '億り人', 'KAUCHE（カウシェ）', 'お金', '投資'],
        '億り人' => ['お金', '仮想通貨', 'Coin', '投資', 'NISA', '株式投資', 'FX', '副業', 'せどり'],
        '投資' => ['お金', '仮想通貨', 'Coin', 'せどり', 'NISA', '株式投資', 'FX', '副業', '億り人'],
        '株式投資' => ['お金', '仮想通貨', 'Coin', 'せどり', 'NISA', '投資', 'FX', '副業', '億り人'],
        '仮想通貨' => ['お金', '株式投資', 'Coin', 'せどり', 'NISA', '投資', 'FX', '副業', '億り人'],
        'FX' => ['お金', '仮想通貨', 'Coin', '投資', 'NISA', '株式投資', '億り人', '副業', 'せどり'],
        'Coin' => ['お金', '仮想通貨', 'FX', '投資', 'NISA', '株式投資', '億り人', '副業', 'せどり'],
        'NISA' => ['お金', '仮想通貨', 'FX', '投資', 'Coin', '株式投資', '億り人', '副業', 'せどり'],
        '競艇予想' => ['お金', '仮想通貨', '投資', 'FX', '株式投資', '億り人', 'Coin', '副業', '競馬予想', 'パチンコ・スロット（パチスロ）'],
        '競馬予想' => ['お金', '仮想通貨', '投資', 'FX', '株式投資', '億り人', 'Coin', '副業', '競艇予想', 'パチンコ・スロット（パチスロ）'],
        'パチンコ・スロット（パチスロ）' => ['お金', '仮想通貨', 'Coin', '投資', 'FX', '株式投資', '億り人', '副業', '競艇予想', '競馬予想'],
        'ITエンジニア' => ['プログラミング', 'Webエンジニア・プログラミング', 'マーケティング', 'WEBデザイナー・デザイン', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'プログラミング' => ['ITエンジニア', 'Webエンジニア・プログラミング', 'マーケティング', 'WEBデザイナー・デザイン', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'Webエンジニア・プログラミング' => ['ITエンジニア', 'プログラミング', 'マーケティング', 'WEBデザイナー・デザイン', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'WEBデザイナー・デザイン' => ['ITエンジニア', 'プログラミング', 'マーケティング', 'Webエンジニア・プログラミング', 'フリーランス', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'フリーランス' => ['ITエンジニア', 'プログラミング', 'マーケティング', 'Webエンジニア・プログラミング', 'WEBデザイナー・デザイン', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'マーケティング' => ['ITエンジニア', 'プログラミング', 'フリーランス', 'Webエンジニア・プログラミング', 'WEBデザイナー・デザイン', 'デザイナー', '生成AI・ChatGPT', 'SNS', 'YouTuber'],
        'SNS' => ['ITエンジニア', 'プログラミング', 'フリーランス', 'Webエンジニア・プログラミング', 'WEBデザイナー・デザイン', 'Instagram（インスタ）', '生成AI・ChatGPT', 'マーケティング', 'YouTuber'],
    ];

    private const TopPageTagFilter = [
        '大学新入生同士の情報交換',
        '大学 新入生',
        '大学',
        '就活生情報・選考対策・企業研究',
        '新入生',
        '経済',
        'ヘア',
        '競馬予想',
        '競艇予想',
        'サークル',
        '新歓',
        'パチンコ・スロット（パチスロ）',
    ];

    static function getTopPageTagFilter(): array
    {
        return array_merge(self::RecommendPageTagFilter, self::TopPageTagFilter);
    }
}
