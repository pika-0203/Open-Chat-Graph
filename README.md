# オプチャグラフ
https://openchat-review.me

![Image](/public/assets/image.jpg)

## プロジェクトの概要
オプチャグラフはユーザーがオープンチャットを見つけて、成長傾向をグラフで比較できるWEBサイトです。  
[オプチャグラフとは](https://openchat-review.me/policy)  

このサイトは、オープンチャットの参加者数の推移などの情報を日々収集し、データベースに記録します。この収集は、オプチャグラフ専用のクローラーが[LINEオープンチャット公式サイト](https://openchat.line.me/jp)を定期的に巡回することで行われます。  

オプチャグラフのWEBサイト上では、収集されたデータを様々な形式で表示します。例えば、キーワード検索を用いて特定のオープンチャットを探すことができ、検索結果を参加者数の増加順に並び替えることも可能です。  

また、過去1時間、24時間、1週間という期間での参加者数の増加ランキングを表示する機能もあり、どのオープンチャットが現在人気を集めているのか、またどのようなテーマが注目されているのかを知ることができます。

オプチャグラフは、オープンチャットの動向を簡単に把握できる便利なツールです。

## 人数増加ランキング表示
  ### オプチャグラフのランキング掲載条件
  1. メンバー数の変動: 過去1週間でメンバー数に変動があるオープンチャットのみがランキング対象となります。
  2. 最低メンバー数: 現在のメンバー数と比較基準となる前日または前週のメンバー数が共に10人以上である必要があります。

## 技術概要
- バックエンド:
  - PHP
  - [MimimalCMS](https://github.com/mimimiku778/MimimalCMS): カスタム軽量MVCフレームワーク
  - MySQL/MariaDB & SQLite: MySQLはオープンチャット情報の保存用、SQLiteはランキングと統計の管理用。
  - Symfony/browser-kit & Spatie/schema-org: ウェブクローリングとSEO最適化に使用。

- フロントエンド:
  - TypeScript & React
  - MUI: 一貫したデザインを提供。
  - Chart.js & Swiper.js: グラフ表示とスライド機能に使用。 

- 改善点:
  - パフォーマンスとセキュリティ: カスタムMVCフレームワークでパフォーマンスを最適化し、Raw SQL使用時のSQLインジェクション対策を実施。
  - 技術選定: 開発速度、コスト効率、将来のスケーラビリティを考慮し、フロントエンドはReact/Nextへの移行を検討中。

#### 説明
バックエンドはPHPで書かれ、古典的なLAMPスタックを使用しています。  

データベースは、複雑なクエリと細かなテーブル結合が多用されるため、全てRaw SQLで管理されています。  
オープンチャットの情報は基本的にMySQLに保存され、テーブルのAUTO_INCREMENTで発行されるIDとオープンチャットURLの2カラムのみが日次でバックアップされています。

ランキングの順位と人数の統計データはSQLiteで管理されています。これはMySQLを使用しているサーバーでは容量の大きさが問題となり、稼働が困難になったためです。SQLiteのデータベースファイルも日次でバックアップされています。

フロントエンドはPHPとCSR(クライアントサイドレンダリング)を行うReactが混在しており、PHPのページ内にReactでモジュール化されたコンポーネントが複数組み込まれています。将来的にはReactやNext.jsへの完全移行も検討されています。  

今でもまだMVP(プロトタイプ)の延長線上にあり、開発が容易なPHPを使用しています。  
サイトの発展次第ではサーバーを移行して、バックエンドのコードをGoで書き直すということも視野に入れています。  

## 1時間毎のクローリング時の並行処理
このプロジェクトでは、公式サイトからランキングデータを高速にダウンロードしてデータベースを更新するシステムを構築しました。  
このシステムは、約15万件のオープンチャットを効率的に処理することができます。  
  - 具体的には、以下の特徴を持つ処理を行っています。
    - 全24カテゴリのランキングデータを、24個の並行プロセスで同時にダウンロードします。  
    - 各プロセスは、2つのカテゴリ（ランキングと急上昇）のデータを取得します。  
    - このプロジェクトに最適化された独自のレンダリング技術・データベースの更新技術で高速にデータを更新します。  

- データ取得を並行処理で実行する親プロセス  
[OpenChatApiDbMergerWithParallelDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/OpenChatApiDbMergerWithParallelDownloader.php)  

- execから実行される子プロセス  
[ParallelDownloadOpenChat.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Cron/ParallelDownloadOpenChat.php)  

- 子プロセスで利用する、「ランキングデータの取得処理クラス・ダウンロードデータの検証クラス」を実行するクラス  
[OpenChatApiDataParallelDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/OpenChatApiDataParallelDownloader.php)  

## オプチャグラフBotのUA
  オプチャグラフBotのクローリングは、以下のユーザーエージェント（UA）を使用して統一的に行われます。  
  IPアドレスについては、レンタルサーバーを使用しているため一定ではありませんが、基本的には固定IPを利用しています。  

  - `Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)`  

  このユーザーエージェントは、オプチャグラフBotがLINE公式サイトをクローリングする際に使用されます。  

  LINE公式サイト側では、このユーザーエージェントを識別することで、オプチャグラフBotからのアクセスを確認することができます。  

## フロントエンドのリポジトリ  
ランキングページ  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend  

グラフ表示  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph  

コメント機能  
https://github.com/mimimiku778/Open-Chat-Graph-Comments  

## ライセンスについて
公開しているソースコードはすべて MIT License で公開しています。

## クローラー  
クローラーはウェブサイト上の情報を自動的に収集するプログラムです。このプロジェクトでは、オープンチャットの情報を定期的に収集する独自のクローラーを作成しています。  
これにより、オープンチャットの最新の人数データや説明文といった情報をリアルタイムに反映することができています。  

クローラーによって収集されたデータから特定の情報を抽出し、整理する行為をスクレイピングと言います。  

具体的には、ウェブページからテキストや画像などのデータを取得し、それを解析して有用な情報を抽出するプロセスです。このプロジェクトでのクローラーの使用も、実質的にはスクレイピングの一種と言えます。  

- クローラーやスクレイピングツールを開発する際には、いくつかの重要な注意点があります。以下に主なものを挙げます。

  - 法的な観点: ウェブサイトのコンテンツは著作権で保護されていることが多いため、無断でのデータ収集や利用は著作権侵害にあたる可能性があります。また、特定のサイトはスクレイピングを禁止している場合がありますので、利用規約を事前に確認してください。

  - サイトへの負荷: クローラーやスクレイピングツールは短時間に大量のリクエストを送ることができますが、これが原因で対象サイトに過大な負荷をかけ、サービスの妨げになることがあります。リクエストの間隔を適切に設定し、サイトに負荷をかけ過ぎないようにしましょう。

  - 個人情報の取り扱い: 収集したデータに個人情報が含まれている場合、それをどのように扱うかには特に注意が必要です。個人情報保護法などの関連法規を遵守し、情報の適切な管理と保護に努めてください。

  - robots.txtの尊重: ウェブサイトのルートディレクトリにあるrobots.txtファイルは、クローラーに対してどのページをスクレイピングしてよいか、どのページを避けるべきかの指示を含んでいます。クローラーやスクレイピングツールを開発する際には、このファイルの内容を尊重することが重要です。

  クローラーやスクレイピングツールの開発は、多くの面で実用性が高いものですが、上記のような注意点を守りながら、倫理的かつ法的な枠組みの中で行うことが重要です。

## 連絡先
[E-MAIL](<mailto:support@openchat-review.me>)  
