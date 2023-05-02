# Open-Chat-Graph
https://openchat-review.me/

## プロジェクトの概要
サイトの目的は、各オープンチャットのメンバー数を収集し、グラフとしてサイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLがデータベースに記録され、定期的にクローラーがURLへアクセスして更新内容を収集します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。　　

## クローリングのアルゴリズム
バックグランドジョブでは、最終更新日が8時間以上前のレコードを取得して更新するジョブが実行されます。  
このジョブは20分に一度呼び出され、一度のジョブで最大200件までのレコードのクローリングと更新を行います。  
1URL毎のアクセス間隔は3秒です。

* Cronジョブクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Cron.php
* アップデートのサービスクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
* クローラー本体のクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatCrawler.php
* Symfony Crawlerラッパークラス
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php
