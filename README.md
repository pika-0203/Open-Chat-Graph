# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLがデータベースに記録され、そのURLにクローラーがアクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。　　

## トップページのランキング表示アルゴリズム
1日ごとにランキングを更新して、ランク付け点数が高い順に表示されます。  
メンバー数10人以上のオープンチャットが表示対象となります。  
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランキングの順位を決める計算式  
増加数: `現在のメンバー数 - 昨日のメンバー数`  
増加率: `増加数 / 昨日のメンバー数`  
ランク付け点数: `増加数 + 増加率 × 5`
 ** ただし、一日でメンバーが1.75倍以上になった場合は `増加数 + 増加率 × 7.5`となります。

* ランキング更新処理  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Models/Repositories/sql/StatisticsRankingUpdaterRepository_updateCreateRankingTable.sql

## クローリングのアルゴリズム
バックグラウンドジョブでは、データベース内のレコードを毎日12時頃に更新します。  
各URLへのアクセス間隔は1秒で設定されています。  

* バックグラウンドジョブクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Cron.php
* レコード更新処理のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
* クローラー本体のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatCrawler.php
* Symfony Crawlerラッパークラス
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php
