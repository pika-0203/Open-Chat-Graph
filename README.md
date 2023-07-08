# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
オープンチャットの人数統計ツールを提供する非営利サイトです。  
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLがデータベースに記録され、そのURLにクローラーがアクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。  

## ランキングの表示アルゴリズム
毎日12時頃にデータを更新して、ランク付け点数が高い順に表示されます。  
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランキングの順位を決める計算式  
増加数: `現在のメンバー数 - 昨日のメンバー数`  
増加率: `増加数 / 昨日のメンバー数`  
ランク付け点数: `増加数 + 増加率 × 20`  
***2023.05.27 試験的にランク付け点数を`増加数 + 増加率 × 30`にしました。***

* ランキング掲載条件  
過去1週間でメンバー数に変動がある  
メンバー数が10人以上のオープンチャット  
  
* ランキング更新処理  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Models/Repositories/sql/StatisticsRankingUpdaterRepository_updateCreateDailyRankingTable.sql

## クローリングのアルゴリズム
サイトに登録されているオープンチャットを毎日12時頃に更新します。  
メンバー数の統計、オープンチャットのタイトル・説明文・画像が最新の状態になります。  
過去1週間メンバー数に動きがない場合、次の更新は1週間後になります。  

* バックグラウンドジョブクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Cron.php
* レコード更新処理のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
* クローラー本体のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatCrawler.php
* Symfony Crawlerラッパークラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php

## 開発フレームワーク
MimimalCMS  
https://github.com/mimimiku778/MimimalCMS

## 連絡先
[E-MAIL](<mailto:support@openchat-review.me>)  
[OPENCHAT](<https://line.me/ti/g2/rLT0p-Tz19W7jxHvDDm9ECGNsyymhLQTHmmTkg>)
