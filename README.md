# オプチャグラフ
https://openchat-review.me

![Image](/image.jpg)

## プロジェクトの概要
オプチャグラフは興味があるオープンチャットを見つけて、成長傾向をグラフで見ることができる場所です。  
[オプチャグラフとは](https://openchat-review.me/policy)

## クローリング  
クロール可能な公開済みのオープンチャットを自動収集します。  
自動更新によりメンバー数の統計、オープンチャットのタイトル・説明文・画像が最新の状態になります。  

- #### クローラー本体
  - クローラー(symfony/browser-kitのラッパークラス)  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php
  - ファイルダウンローダー(symfony/HttpClientのラッパークラス)  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/FileDownloader.php

- #### ランキングデータの取得
  - オープンチャット公式サイトの取得URL(公開済みの公式サイト)    
  `https://openchat.line.me/api/category/${category}?sort=RANKING&limit=40&ct=${ct}`
  - ランキングデータの取得処理  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/AbstractOpenChatApiRankingDownloaderProcess.php
  - スクロール読み込みを再現して順番にデータを取得する「ランキングデータの取得処理」の上位クラス  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiRankingDownloader.php
  - サブカテゴリデータ(カテゴリ内のキーワード)の取得処理  
  https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiSubCategoryDownloader.php

- #### オープンチャットの取得
  - オープンチャット公式サイトの取得URL(公開済みの公式サイト)    
    `https://openchat.line.me/api/square/${emid}?limit=1`
  - オープンチャットの取得処理  
    https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiFromEmidDownloader.php

- #### オープンチャット画像の取得
  - 画像の取得URL(公開済みの公式サイト)    
    `https://obs.line-scdn.net/${profileImageObsHash}`
  - 画像の取得処理  
    https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatImgDownloader.php

- #### オプチャグラフBotのUA
  - Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)

## ランキング表示のアルゴリズム
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位になりやすくなります。  

* ランキングの掲載対象となるオープンチャットの条件  
・過去1週間のメンバー数に変動があること  
・現在のメンバー数と前日 or 前週のメンバー数が10人以上であること

* ランク付けの計算式  
増加数: `現在のメンバー数 - 前日 or 前週のメンバー数`  
増加率: `増加数 / 前日 or 前週のメンバー数`  
ランク付け: `増加数 + 増加率 × 10`  

## 使用技術
- バックエンドの主要技術
  - PHP
  - [MimimalCMS](https://github.com/mimimiku778/MimimalCMS)
  - Symfony BrowserKit
  - MySQL/MariaDB
  - SQLite

- フロントエンドの主要技術
  - TypeScript
  - React
  - MUI
  - Chart.js
  - Swiper.js

バックエンドはPHPで書かれており、自前の軽量MVCフレームワークとシンプルなリポジトリパターンで実装されています。  
DBは複雑なクエリで細かいテーブル結合などが多いため、全てRaw SQLで書かれています。  
国内の高速レスポンス・安価なLAMPサーバーをつかい非常に低いランニングコストで稼働しています。  

ランキング順位と人数統計を日々記録するDBは容量が大きく、MySQL\MariaDBにすると安いサーバーでは動かせなくなるため、SQLiteを使っています。

## フロントエンドのリポジトリ  
ランキングページ  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend  

グラフ表示  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph  

コメント機能  
https://github.com/mimimiku778/Open-Chat-Graph-Comments  

## ライセンスについて
公開しているソースコードはすべて MIT License で公開しています。

## 連絡先
[E-MAIL](<mailto:support@openchat-review.me>)  
