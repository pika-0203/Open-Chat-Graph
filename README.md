# オプチャグラフ
https://openchat-review.me

![Image](/image.jpg)

## プロジェクトの概要
オプチャグラフは興味があるオープンチャットを見つけて、成長傾向をグラフで見ることができる場所です。  
[オプチャグラフとは](https://openchat-review.me/policy)

## クローリング  
クロール可能な公開済みのオープンチャットを自動収集します。  
自動更新によりメンバー数の統計、オープンチャットのタイトル・説明文・画像が最新の状態になります。  

クローラーはウェブサイト上の情報を自動的に収集するプログラムです。  
このプロジェクトでは、オープンチャットの情報を定期的に収集する独自のクローラーを作成しています。  
これにより、オープンチャットの最新の人数データや説明文といった情報をリアルタイムに反映することができています。  

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
- バックエンドの主要技術とその改善点(By ChatGPT):

  - PHP: 幅広いサポートと豊富なライブラリにより、迅速な開発が可能です。
  - [MimimalCMS](https://github.com/mimimiku778/MimimalCMS): 必要な機能を提供するカスタムの軽量MVCフレームワークを採用しています。
  - MySQL/MariaDB & SQLite: MySQLはオープンチャット情報の保存に使用され、SQLiteはランキング順位と人数統計の記録に使われています。これにより、データ管理を最適化し、コストを削減しています。
  - Symfony/browser-kit: サーバーサイドのアプリケーションでクローリング機能を実装し、動的なコンテンツのテストやクローリングに利用しています。
  - Spatie/schema-org: SEO最適化を強化するために使用しています。構造化データの生成を簡単にし、検索エンジンによるサイトの理解を深めます。

- フロントエンドの主要技術(By ChatGPT):
  - TypeScript & React: 型安全性とコンポーネントベースのアプローチにより、効率的なフロントエンド開発を可能にします。
  - MUI: 一貫したデザインとユーザーエクスペリエンスを提供します。
  - Chart.js & Swiper.js: グラフ表示とスライド機能の実装に利用しています。 

- その他の改善点(By ChatGPT):
  - パフォーマンスとセキュリティ: カスタムMVCフレームワークの採用により、必要な機能のみを実装しパフォーマンスを最適化しています。Raw SQLの使用時にはSQLインジェクションを防ぐための徹底した対策を講じています。
  - 技術選定の背景: 各技術の選定は、開発速度、コスト効率、将来のスケーラビリティを考慮して行われています。フロントエンドの完全な移行をReact/Nextに検討しているのは、より現代的でスケーラブルなフロントエンド開発を目指すためです。
  
##### 説明
バックエンドはPHPで書かれており、LAMPスタックによる古典的なものです。  
自前の軽量MVCフレームワークとシンプルなリポジトリパターンで実装されています。  
国内の高レスポンス・安価なLAMPサーバーにより低コストで稼働しています。  

DBは複雑なクエリで細かいテーブル結合などが多いため、全てRaw SQLで書かれています。  
基本的にオープンチャットの情報などはMySQLに保存しています。  
テーブルのAUTO_INCREMENTで発行されるIDとオープンチャットURLの2カラムだけを日次バックアップしています。  

ランキング順位と人数統計を日々記録するDBはSQLiteです。容量が大きく、安いサーバーのMySQLでは動かせなくなったためです。  
SQLiteのDBファイルを日次バックアップしています。  

フロントエンドはPHPとCSRのReactが混在しています。  
PHPのページにモジュール化したReactが複数埋め込まれている状況です。  
いずれReact/Nextに全移行するかもしれないです。  

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
