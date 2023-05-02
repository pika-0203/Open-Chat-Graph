# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLがデータベースに記録され、そのURLにクローラーがアクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。　　

## クローリングのアルゴリズム
データベース内の最終更新日から8時間以上経過したレコードを更新するためのバックグラウンドジョブを実行します。  
ジョブは、20分ごとに呼び出され、最大200件のレコードを一度に処理します。  
各URLへのアクセス間隔は3秒で設定されています。  

* レコードの更新をするクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
* クローラー本体のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatCrawler.php
* Symfony Crawlerラッパークラス
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php
