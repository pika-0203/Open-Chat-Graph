# Open-Chat-Graph
https://openchat-review.me

## プロジェクトの概要
オープンチャット公式サイトのデータを分析してグラフ等に視覚化された情報を提供するサイトです。  
サイトの目的は、各オープンチャットのメンバー数・ランク順位の推移を記録し、グラフとしてサイト上で表示することです。  
これにより、オープンチャットの成長傾向を把握し、比較することができ、トークルームの運営に役立ちます。  

## ランキングの表示
人数増加のランキングが毎日更新されます。

* 人数増加ランキングの掲載対象となるオープンチャットの条件  
・過去1週間のメンバー数に変動があること  
・現在のメンバー数と前日 or 前週のメンバー数が10人以上であること

元々のメンバー数が少ないオープンチャットであるほど増加率が高くなり、上位に上がりやすくなります。  

* ランク付けの計算式  
増加数: `現在のメンバー数 - 前日 or 前週のメンバー数`  
増加率: `増加数 / 前日 or 前週のメンバー数`  
ランク付け: `増加数 + 増加率 × 10`  

## PHPフレームワーク  
MimimalCMS  
https://github.com/mimimiku778/MimimalCMS

## ランキングUI  
Open-Chat-Graph-Frontend  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend

## グラフUI  
Open-Chat-Graph-Frontend-Stats-Graph  
https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph

## コメント機能  
Open-Chat-Graph-Comments  
https://github.com/mimimiku778/Open-Chat-Graph-Comments

###### mimimiku778 と pika-0203 は同一人物です
