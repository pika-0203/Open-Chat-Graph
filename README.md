# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
オープンチャットの人数統計ツールを提供する非営利サイトです。  
サイトの目的は、各オープンチャットのメンバー数を自動的に記録し、グラフとしてサイト上で表示することです。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。  

## クローリング  
アクセス可能なオープンチャットを毎日自動収集します。  
日次の自動更新により、メンバー数の統計、オープンチャットのタイトル・説明文・画像が最新の状態になります。  

## ランキングの表示
毎日12時頃にランキングが更新されます。
元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランキングの掲載対象となるオープンチャットの条件  
・過去1週間のメンバー数に変動があること  
・現在のメンバー数と前日 or 前週のメンバー数が10人以上であること

* ランク付けの計算式  
増加数: `現在のメンバー数 - 前日 or 前週のメンバー数`  
増加率: `増加数 / 前日 or 前週のメンバー数`  
ランク付け: `増加数 + 増加率 × 20`  

## 開発フレームワーク
MimimalCMS  
https://github.com/mimimiku778/MimimalCMS

## ランキングUI  
Open-Chat-Graph-Frontend  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend

## 連絡先
[E-MAIL](<mailto:support@openchat-review.me>)  
[OPENCHAT](<https://line.me/ti/g2/rLT0p-Tz19W7jxHvDDm9ECGNsyymhLQTHmmTkg>)
