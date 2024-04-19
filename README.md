# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
オープンチャットの人数統計ツールを提供する非営利サイトです。  
サイトの目的は、各オープンチャットのメンバー数を自動的に記録し、グラフとしてサイト上で表示することです。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。  

## クローリング  
アクセス可能なオープンチャットを自動収集します。  
自動更新により、メンバー数の統計、オープンチャットのタイトル・説明文・画像が最新の状態になります。  

#### オープンチャットのデータを収集するURL(公開済みの公式サイト)  
- #### ランキングデータの取得
  - オープンチャット公式サイトの取得URL  
  `https://openchat.line.me/api/category/${category}?sort=RANKING&limit=40&ct=${ct}`
  - ランキングデータの取得処理  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/AbstractOpenChatApiRankingDownloaderProcess.php
  - サブカテゴリデータ(カテゴリ内のキーワード)の取得処理  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiSubCategoryDownloader.php

- #### オープンチャットの取得
  - オープンチャット公式サイトの取得URL  
    `https://openchat.line.me/api/square/${emid}?limit=1`
  - オープンチャットの取得処理  
    https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiFromEmidDownloader.php

- #### オープンチャット画像の取得
  - 画像の取得URL  
    `https://obs.line-scdn.net/${profileImageObsHash}`
  - 画像の取得処理  
    https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatImgDownloader.php

オプチャグラフBotのUA: Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)

## ランキング表示のアルゴリズム
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランキングの掲載対象となるオープンチャットの条件  
・過去1週間のメンバー数に変動があること  
・現在のメンバー数と前日 or 前週のメンバー数が10人以上であること

* ランク付けの計算式  
増加数: `現在のメンバー数 - 前日 or 前週のメンバー数`  
増加率: `増加数 / 前日 or 前週のメンバー数`  
ランク付け: `増加数 + 増加率 × 10`  

## 開発フレームワーク
MimimalCMS  
https://github.com/mimimiku778/MimimalCMS

## フロントエンドのリポジトリ  
ランキングページ  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend  

グラフ表示  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph  

コメント機能
https://github.com/mimimiku778/Open-Chat-Graph-Comments  

## 連絡先
[E-MAIL](<mailto:support@openchat-review.me>)  
