# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
オープンチャットの人数統計ツールを提供する非営利サイトです。  
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLをデータベースに記録し、そのURLにクローラーが定期アクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。  

## ランキングの表示アルゴリズム
毎日12時頃にデータを更新して、ランク付け点数が高い順に表示されます。  
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランキングの掲載対象となるオープンチャットの条件  
・過去1週間でメンバー数に変動があること  
・現在のメンバー数と前日or前週のメンバー数が10人以上であること

* ランク付け点数の計算式  
増加数: `現在のメンバー数 - 前日or前週のメンバー数`  
増加率: `増加数 / 前日or前週のメンバー数`  
ランク付け点数: `増加数 + 増加率 × 30`  
  
* 処理の内容 - MySQL  
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
