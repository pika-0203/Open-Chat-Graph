# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLがデータベースに記録され、そのURLにクローラーがアクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。　　

## トップページのランキング表示アルゴリズム
120分ごとにランキングを更新して、ランク付け点数が高い順に表示されます。  
メンバー数10人以上のオープンチャットが表示対象となります。  
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランキングの順位を決める計算式  
増加数: `現在のメンバー数 - 昨日〜7日前の最小メンバー数`  
増加率: `増加数 / 昨日〜7日前の最小メンバー数`  
ランク付け点数: `増加数 + 増加率 × 10`  

* ランキング更新処理  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Models/Repositories/sql/StatisticsRankingUpdaterRepository_updateCreateRankingTable.sql

## クローリングのアルゴリズム
バックグラウンドジョブでは、データベース内の最終更新日時から8時間以上経過したレコードを更新します。  
ジョブは60分ごとに呼び出され、最大200件のレコードを一度に処理します。  
各URLへのアクセス間隔は3秒で設定されています。  

* バックグラウンドジョブクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Cron.php
* レコード更新処理のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
* クローラー本体のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatCrawler.php
* Symfony Crawlerラッパークラス
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php
