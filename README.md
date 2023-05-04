# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLがデータベースに記録され、そのURLにクローラーがアクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。　　

## ランキング表示のアルゴリズム
トップページには、登録されているオープンチャットのメンバー数上昇率ランキングが表示されます。
**ランキングの順位を決める計算式**  
`(現在のメンバー数 - 過去7日間で最小のメンバー数) + `

## クローリングのアルゴリズム
バックグラウンドジョブでは、データベース内の最終更新日時から8時間以上経過したレコードを更新します。  
ジョブは60分ごとに呼び出され、最大200件のレコードを一度に処理します。  
各URLへのアクセス間隔は3秒で設定されています。  

* バックグラウンドジョブクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Cron.php
* レコードの更新をするクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
* クローラー本体のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatCrawler.php
* Symfony Crawlerラッパークラス
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php
