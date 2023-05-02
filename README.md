# Open-Chat-Graph
https://openchat-review.me/

## プロジェクトの概要
主な目的は、オープンチャットのメンバー数を収集し、収集したメンバー数をグラフ化して、サイト上で表示することです。  
ユーザーが投稿したオープンチャットのURLをデータベースに収集し、定期的にクローラーがURLへアクセスして更新内容をデータベースに保存します。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。　　

## クローリングのアルゴリズム
バックグランドジョブでは、最終更新日が8時間以上前のレコードを取得して更新するジョブが実行されます。
このジョブは20分に一度呼び出され、一度のジョブで最大200件までのレコードのクローリングと更新を行います。
1URL毎のクローリングのインターバルは3秒です。

* アップデートのサービスクラス  
https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/UpdateOpenChat.php
