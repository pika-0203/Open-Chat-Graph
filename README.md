# オプチャグラフ
https://openchat-review.me

![Image](/image.jpg)

## プロジェクトの概要
オプチャグラフはユーザーがオープンチャットを見つけて、成長傾向をグラフで比較できるWEBサイトです。  
[オプチャグラフとは](https://openchat-review.me/policy)  

このサイトは、オープンチャットの参加者数の推移などの情報を日々収集し、データベースに記録します。この収集は、オプチャグラフ専用のクローラーが[LINEオープンチャット公式サイト](https://openchat.line.me/jp)を定期的に巡回することで行われます。  

オプチャグラフのWEBサイト上では、収集されたデータを様々な形式で表示します。例えば、キーワード検索を用いて特定のオープンチャットを探すことができ、検索結果を参加者数の増加順に並び替えることも可能です。  

また、過去1時間、24時間、1週間という期間での参加者数の増加ランキングを表示する機能もあり、どのオープンチャットが現在人気を集めているのか、またどのようなテーマが注目されているのかを知ることができます。

オプチャグラフは、オープンチャットの動向を簡単に把握できる便利なツールです。

## クローラー  
クローラーはウェブサイト上の情報を自動的に収集するプログラムです。  
このプロジェクトでは、オープンチャットの情報を定期的に収集する独自のクローラーを作成しています。  
これにより、オープンチャットの最新の人数データや説明文といった情報をリアルタイムに反映することができています。  

- 余談ですが、クローラーやスクレイピングツールの開発は、プログラミングをこれから始める人にとって、非常に良い練習題材となります。  

  例えば、あるウェブサイトから最新のニュース記事のタイトルを自動で集めたい場合、スクレイピングツールを使ってこの作業を自動化できます。  
  これは、手動でページを開いて、必要な情報をコピー＆ペーストする代わりに、プログラムが自動でその作業を行ってくれるというわけです。  

  プログラミング初心者にとって、クローラーやスクレイピングツールの開発は、以下のような理由で学びやすいと言えます。
  - 実用性が高い: インターネット上の情報を自動で収集・分析できるため、様々なプロジェクトやアイデアの実現に役立ちます。  

  - 基本的なプログラミング技術を学べる: 文字列処理、ループ、条件分岐、関数などの基礎的なプログラミングの概念を実践的に学べます。  

  - 結果がすぐに見える: コードを書いた後、すぐにウェブサイトから情報が抽出されるのを見ることができるため、達成感を感じやすいです。  

  - 初めてプログラミングを学ぶ人にとって、このようなプロジェクトを通じて、基本的なコードの書き方やプログラミングの流れを理解し、実際に何かを作り出す楽しさを感じることができるでしょう。

    - クローラーやスクレイピングツールを開発する際には、いくつかの重要な注意点があります。以下に主なものを挙げます。

      - 法的な観点: ウェブサイトのコンテンツは著作権で保護されていることが多いため、無断でのデータ収集や利用は著作権侵害にあたる可能性があります。また、特定のサイトはスクレイピングを禁止している場合がありますので、利用規約を事前に確認してください。

      - サイトへの負荷: クローラーやスクレイピングツールは短時間に大量のリクエストを送ることができますが、これが原因で対象サイトに過大な負荷をかけ、サービスの妨げになることがあります。リクエストの間隔を適切に設定し、サイトに負荷をかけ過ぎないようにしましょう。

      - 個人情報の取り扱い: 収集したデータに個人情報が含まれている場合、それをどのように扱うかには特に注意が必要です。個人情報保護法などの関連法規を遵守し、情報の適切な管理と保護に努めてください。

      - robots.txtの尊重: ウェブサイトのルートディレクトリにあるrobots.txtファイルは、クローラーに対してどのページをスクレイピングしてよいか、どのページを避けるべきかの指示を含んでいます。クローラーやスクレイピングツールを開発する際には、このファイルの内容を尊重することが重要です。

  クローラーやスクレイピングツールの開発は、プログラミングの学習に非常に有効であり、多くの面で実用性が高いものですが、上記のような注意点を守りながら、倫理的かつ法的な枠組みの中で行うことが重要です。


## クローラー本体
このクローラーは、PHPでのウェブページの自動巡回とデータ収集を目的としています。  
SymfonyのBrowserKitを使用しており、特定のウェブページへのアクセスを試み、エラー応答に応じて再試行します。  
404エラー（ページが見つからない）以外のエラーの場合、設定された回数まで再試行を行い、それを超えるとエラーを報告します。  

LINEのサーバーからは、稀に400系エラーが返されることがありますが、このクローラーはそのような一時的なエラーに対しても効率的に対応します。  

- クローラー: SymfonyのBrowserKitを基にしたラッパークラス。エラーハンドリングと再試行機構を備えています。  
[CrawlerFactory.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/CrawlerFactory.php)  

- ファイルダウンローダー: SymfonyのHttpClientを基にしたラッパークラス。効率的なファイルダウンロードを実現します。  
[FileDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Crawler/FileDownloader.php)


## ランキングデータの取得
LINEオープンチャットのランキングデータを、フロントエンドのAPIを通じて取得します。  

  #### APIエンドポイントの構造:
  - 取得URL: `https://openchat.line.me/api/category/${category}?sort=RANKING&limit=40&ct=${ct}`
    - ${category}: 取得したいカテゴリを指定します。
    - ${ct} (continuation token): ページングを管理するためのトークンです。最初は0から始め、取得したデータに含まれる次のctを用いて、続くページを順に取得します。

  #### 実装コード:
  - ランキングデータの取得処理: 無限スクロールを模倣してランキングデータを順に取得します。
  [AbstractOpenChatApiRankingDownloaderProcess.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/AbstractOpenChatApiRankingDownloaderProcess.php)

  - ランキングデータ取得の上位クラス:  
  [OpenChatApiRankingDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiRankingDownloader.php)

  - ダウンロードデータの検証クラス: APIからのデータを検証し、オブジェクト形式にマッピングする役割を持ちます。  
  [OpenChatApiDtoFactory.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Dto/OpenChatApiDtoFactory.php)

  - サブカテゴリデータの取得: カテゴリ内のキーワード、すなわちサブカテゴリデータを取得するための処理です。  
  [OpenChatApiSubCategoryDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiSubCategoryDownloader.php)

## 1時間毎のクローリング時の並行処理
このプロジェクトでは、公式サイトからランキングデータを高速にダウンロードしてデータベースを更新するシステムを構築しました。  
  - 具体的には、以下の特徴を持つ処理を行っています。
    - 全24カテゴリのランキングデータを、24個の並行プロセスで同時にダウンロードします。  
    - 各プロセスは、2つのカテゴリ（ランキングと急上昇）のデータを取得します。  
    - ダウンロードが完了するごとに、SQLのフラグを用いて処理進行を管理します。  
    - 全プロセスが終了し、全カテゴリのデータ更新が完了すると、全体の処理が終了します。  

- データ取得を並行処理で実行する親プロセス  
[OpenChatApiDbMergerWithParallelDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/OpenChatApiDbMergerWithParallelDownloader.php)  

- execから実行される子プロセス  
[ParallelDownloadOpenChat.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/Cron/ParallelDownloadOpenChat.php)  

- 子プロセスのクラスで利用する、「ランキングデータの取得処理クラス・ダウンロードデータの検証クラス」を実行するクラス  
[OpenChatApiDataParallelDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/OpenChatApiDataParallelDownloader.php)  

エラーが発生した場合は、共有されているエラーフラグファイルを通じて全プロセスを停止させることができます。  
このシステムにより、10万件のデータを約2分で処理できるようになりました。  

## オープンチャットの取得
このセクションでは、LINEオープンチャットの情報を取得するプロセスについて説明します。  
ランキングからダウンロードしたデータにはオープンチャットの情報が含まれていますが、新しいオープンチャットを登録する場合や、ランキングに掲載されていないオープンチャットの情報を更新する場合には、追加のデータ取得が必要です。

  #### APIエンドポイントの構造:
  - 取得URL: `https://openchat.line.me/api/square/${emid}?limit=1`
    - ${emid}: オープンチャットを特定するためのID。
    - limit=1: オープンチャットのページに表示されるおすすめの数を指定します。（最低値は1）

  #### 実装コード:
  - オープンチャットの取得処理: 特定のオープンチャットの追加データを取得するための処理です。  
    これは、新規にオープンチャットを登録する際や、日次処理でランキングに掲載されていない既存のオープンチャットの情報を更新する際に使用されます。   
    [OpenChatApiFromEmidDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatApiFromEmidDownloader.php)

## オープンチャット画像の取得
  #### URLの構造:
  - 取得URL: `https://obs.line-scdn.net/${profileImageObsHash}`
    - ${profileImageObsHash}: オープンチャットを画像を特定するためのハッシュ。

  #### 実装コード:
  - 画像の取得処理: オープンチャットの画像を取得するための処理です。  
  [OpenChatImgDownloader.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Services/OpenChat/Crawler/OpenChatImgDownloader.php)  

## オプチャグラフBotのUA
  オプチャグラフBotのクローリングは、以下のユーザーエージェント（UA）を使用して統一的に行われます。  
  IPアドレスについては、レンタルサーバーを使用しているため一定ではありませんが、基本的には固定IPを利用しています。  

  - `Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)`  

  このユーザーエージェントは、オプチャグラフBotがLINE公式サイトをクローリングする際に使用されます。  

  LINE公式サイト側では、このユーザーエージェントを識別することで、オプチャグラフBotからのアクセスを確認することができます。  

## 人数増加ランキング表示
  ### オプチャグラフのランキング掲載条件
  1. メンバー数の変動: 過去1週間でメンバー数に変動があるオープンチャットのみがランキング対象となります。
  2. 最低メンバー数: 現在のメンバー数と比較基準となる前日または前週のメンバー数が共に10人以上である必要があります。

  ### ランク付けの計算方法
  ランキングは以下のステップで計算されます。
  1. 増加数の計算:
     - 計算式: 現在のメンバー数 - 比較基準のメンバー数（前日または前週）
  2. 増加率の計算:
     - 計算式: 増加数 ÷ 比較基準のメンバー数  
  3. ランク付けのスコア計算:
     - 計算式: 増加数 + (増加率 × 10)  

  このアルゴリズムにより、メンバー数の増加が顕著なルーム、特に元々メンバー数が少ないルームがランキング上位に来やすくなるよう設計されています。  
  これにより、新規または小規模なオープンチャットの発展を促進することが期待されます。


## 技術概要
- バックエンド:
  - PHP: 幅広い用途に対応し、迅速な開発を可能にする言語。
  - [MimimalCMS](https://github.com/mimimiku778/MimimalCMS): 効率的な開発のためのカスタム軽量MVCフレームワーク。
  - MySQL/MariaDB & SQLite: MySQLはオープンチャット情報の保存用、SQLiteはランキングと統計の管理用。
  - Symfony/browser-kit & Spatie/schema-org: ウェブクローリングとSEO最適化に使用。

- フロントエンド:
  - TypeScript & React: 型安全性とコンポーネントベースの開発を実現。
  - MUI: 一貫したデザインを提供。
  - Chart.js & Swiper.js: グラフ表示とスライド機能に使用。 

- 改善点:
  - パフォーマンスとセキュリティ: カスタムMVCフレームワークでパフォーマンスを最適化し、Raw SQL使用時のSQLインジェクション対策を実施。
  - 技術選定: 開発速度、コスト効率、将来のスケーラビリティを考慮し、フロントエンドはReact/Nextへの移行を検討中。

#### 説明
バックエンドはPHPで書かれ、古典的なLAMPスタックを使用しています。  
独自に開発された軽量MVCフレームワークとシンプルなリポジトリパターンを採用し、国内の高レスポンスかつコストパフォーマンスに優れたLAMPサーバーで低コスト運用を実現しています。  

データベースは、複雑なクエリと細かなテーブル結合が多用されるため、全てRaw SQLで管理されています。  
オープンチャットの情報は基本的にMySQLに保存され、テーブルのAUTO_INCREMENTで発行されるIDとオープンチャットURLの2カラムのみが日次でバックアップされています。
  
ランキングの順位と人数の統計データはSQLiteで管理されています。これはMySQLを使用しているサーバーでは容量の大きさが問題となり、稼働が困難になったためです。SQLiteのデータベースファイルも日次でバックアップされています。

フロントエンドはPHPとCSR(クライアントサイドレンダリング)を行うReactが混在しており、PHPのページ内にReactでモジュール化されたコンポーネントが複数組み込まれています。将来的にはReactやNext.jsへの完全移行も検討されています。

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
