# OpenChat Graph Next.js プロトタイプ要件定義書

## プロジェクト概要

OpenChat Graph（https://openchat-review.me/）のフロントエンドをNext.jsに移行するためのプロトタイプを作成する。**完全に分離された新しいリポジトリ**として開発し、既存のPHP（MimimalCMS）バックエンドとはAPI経由で連携する。

## 技術スタック

### フロントエンド
- **Next.js**: 最新版（App Router使用）
- **React**: 19（最新版）
- **TypeScript**: 最新版
- **Tailwind CSS**: 最新版
- **CSS Framework**: Tailwind CSS + Headless UI
- **Charts**: Chart.js または Recharts（最新版）

### バックエンド
- **既存**: PHP 8.3 + MimimalCMS（別リポジトリで稼働）
- **新規追加**: Next.js用JSON APIコントローラー
- **開発環境**: Docker Compose

## プロトタイプスコープ

### 実装対象ページ
1. **トップページ** (`/`)
   - サイト概要
   - 主要統計情報
   - 最新ランキングサマリー

2. **OpenChat詳細ページ** (`/oc/[id]`)
   - OpenChatの基本情報
   - メンバー数推移グラフ
   - 統計データ表示

### 除外機能（プロトタイプでは実装しない）
- ランキングページ
- 検索・フィルタリング機能
- MyList（お気に入り）機能
- コメント機能
- ユーザー認証
- 管理者機能
- 多言語対応（日本語のみ実装）

## 実装要件

### 1. バックエンドAPI開発

#### 新規APIコントローラー作成
既存のPHPコントローラーはそのままに、以下の新しいJSON APIコントローラーを作成：

```
/app/Controllers/Api/NextJs/
├── HomeApiController.php          # トップページ用データ
└── OpenChatDetailApiController.php # OpenChat詳細用データ
```

#### APIエンドポイント仕様

**1. トップページAPI**
- **URL**: `/api/nextjs/home`
- **Method**: GET
- **Response**: 
```json
{
  "siteStats": {
    "totalOpenChats": number,
    "totalMembers": number,
    "lastUpdated": "ISO8601 timestamp"
  },
  "topRankings": {
    "daily": Array<OpenChatSummary>,
    "weekly": Array<OpenChatSummary>,
    "total": Array<OpenChatSummary>
  },
  "recentlyAdded": Array<OpenChatSummary>
}
```

**2. OpenChat詳細API** ✅ **Enhanced with Real Data**
- **URL**: `/api/nextjs/openchat/{id}`
- **Method**: GET
- **Response**:
```json
{
  "openChat": {
    "id": number,
    "name": string,
    "description": string,
    "memberCount": number,
    "category": string,
    "tags": Array<string>,
    "imgUrl": string,
    "lastUpdate": "ISO8601 timestamp",
    "emblemUrl": string | null,
    "memberDiff": {
      "daily": {
        "difference": number,
        "percentage": number
      },
      "weekly": {
        "difference": number,
        "percentage": number
      }
    }
  },
  "statistics": {
    "memberHistory": Array<{
      "date": "YYYY-MM-DD",
      "memberCount": number
    }>,
    "rankings": {
      "daily": { position: number | null, change: number },
      "weekly": { position: number | null, change: number },
      "total": { position: number | null, change: number }
    },
    "chartMetadata": {
      "startDate": "YYYY-MM-DD",
      "endDate": "YYYY-MM-DD",
      "totalDataPoints": number
    }
  }
}
```

**実際のレスポンス例**:
```json
{
  "openChat": {
    "id": 123,
    "name": "無料スタンプを探そう❣️",
    "memberCount": 391,
    "category": "その他",
    "tags": ["スタンプ", "スタンプ"],
    "memberDiff": {
      "daily": { "difference": 1, "percentage": 0.25641 },
      "weekly": { "difference": 4, "percentage": 1.033591 }
    }
  },
  "statistics": {
    "memberHistory": [/* 615 data points from 2023-10-16 to 2025-06-21 */],
    "chartMetadata": {
      "startDate": "2023-10-16",
      "endDate": "2025-06-21", 
      "totalDataPoints": 615
    }
  }
}
```

#### データ型定義
```typescript
interface OpenChatSummary {
  id: number;
  name: string;
  description: string;
  memberCount: number;
  category: string;
  imgUrl: string;
  dailyGrowth: number;
  weeklyGrowth: number;
}
```

### 2. Next.jsフロントエンド開発

#### プロジェクト構成
```
/oc-graph-nextjs/   # 新しい独立したリポジトリ
├── app/
│   ├── layout.tsx                 # ルートレイアウト
│   ├── page.tsx                   # トップページ
│   ├── oc/
│   │   └── [id]/
│   │       └── page.tsx           # OpenChat詳細ページ
│   └── globals.css                # Tailwind CSS
├── components/
│   ├── ui/                        # 基本UIコンポーネント
│   ├── charts/                    # チャートコンポーネント
│   └── openchat/                  # OpenChat関連コンポーネント
├── lib/
│   ├── api.ts                     # API client
│   ├── types.ts                   # TypeScript型定義
│   └── utils.ts                   # ユーティリティ関数
├── __tests__/                     # テストファイル
│   ├── components/                # コンポーネントテスト
│   ├── pages/                     # ページテスト
│   ├── lib/                       # ライブラリテスト
│   └── __mocks__/                 # Mockデータ
├── jest.config.js                 # Jest設定
├── .eslintrc.js                   # ESLint設定
├── prettier.config.js             # Prettier設定
├── Dockerfile                     # Docker設定
├── docker-compose.yml             # Docker Compose設定
└── package.json
```

#### 必要なコンポーネント

**共通コンポーネント**
- `Header` - サイトヘッダー
- `Footer` - サイトフッター  
- `Layout` - ページレイアウト
- `LoadingSpinner` - ローディング表示

**トップページコンポーネント**
- `SiteStatsCard` - サイト統計カード
- `RankingSection` - ランキングセクション
- `OpenChatCard` - OpenChatカード表示

**詳細ページコンポーネント**
- `OpenChatHeader` - OpenChat基本情報
- `MemberChart` - メンバー数推移グラフ
- `StatsGrid` - 統計情報グリッド

### 3. レスポンシブデザイン要件

- **デスクトップファースト**だが**モバイル対応必須**
- **Tailwind CSS**のレスポンシブクラスを活用
- **グラフ**は画面サイズに応じて適切にリサイズ
- **タッチ操作**対応（モバイル）

### 4. パフォーマンス要件

- **SSR**による初期表示の高速化
- **Next.js Image**最適化の活用
- **APIレスポンス**の適切なキャッシュ
- **Core Web Vitals**の良好なスコア維持

## 開発環境構築

### 1. 新しいリポジトリ作成
```bash
# 親ディレクトリに移動
cd ../

# 新しいNext.jsプロジェクト作成
npx create-next-app@latest oc-graph-nextjs --typescript --tailwind --app --src-dir=false --import-alias="@/*"
cd oc-graph-nextjs

# Gitリポジトリ初期化
git init
git add .
git commit -m "Initial Next.js project setup"
```

### 2. Docker環境設定
```bash
# Dockerfileとdocker-compose.ymlを作成
# Next.js用のDocker開発環境を構築
```

### 3. パッケージインストール ✅ **完了**
```bash
npm install @headlessui/react @heroicons/react
npm install chart.js react-chartjs-2
npm install chartjs-plugin-zoom  # ズーム・パン機能
npm install @types/node
npm install axios  # API通信用

# テスト関連
npm install -D jest @testing-library/react @testing-library/jest-dom
npm install -D @testing-library/user-event jest-environment-jsdom
npm install -D @types/jest

# 開発ツール
npm install -D eslint prettier @typescript-eslint/eslint-plugin
npm install -D @typescript-eslint/parser eslint-config-prettier
npm install -D husky lint-staged  # pre-commit hook
```

**実際にインストール済みのパッケージ** ✅:
- Next.js 15.3.4 (App Router)
- React 19
- TypeScript 5
- Tailwind CSS 3
- Chart.js + react-chartjs-2
- chartjs-plugin-zoom（ズーム・パン機能）
- Axios（API通信）
- @headlessui/react（UIコンポーネント）

### 4. 環境変数設定
```bash
# .env.local作成
NEXT_PUBLIC_API_URL=http://localhost:8000
```

### 5. Docker起動・動作確認
```bash
# Docker環境で開発サーバー起動
docker-compose up -d

# ブラウザで確認
# http://localhost:3000 (Next.js)
# http://localhost:8000 (PHP API)
```

## 実装手順（段階的開発・即座にプレビュー可能）

### ✅ MVP Phase: OpenChat詳細ページ（完了）
**目標**: 素早くプレビュー可能な最小限の機能を実装

#### Step 1: 基盤構築（30分）✅ 完了
- [x] 親ディレクトリに新しいNext.jsリポジトリ作成 → `/oc-graph-nextjs/`
- [x] 基本的なDocker環境構築（最小限） → `Dockerfile.dev` + `docker-compose.yml`
- [x] 必要最小限のパッケージインストール → TypeScript, Tailwind, axios, Chart.js

#### Step 2: PHP APIコントローラー作成（30分）✅ 完了
- [x] **OpenChat詳細API作成** (`/api/nextjs/openchat/{id}`) → `OpenChatDetailApiController.php`
- [x] 既存データベースからのデータ取得実装 → Repository pattern使用
- [x] CORS設定追加 → `Access-Control-Allow-Origin: http://localhost:3000`

#### Step 3: Next.js OpenChat詳細ページ実装（1時間）✅ 完了
- [x] **基本レイアウト作成**（ヘッダー・フッター最小限） → `Header.tsx`, `Footer.tsx`
- [x] **OpenChat詳細ページ実装** (`/oc/[id]/page.tsx`) → SSR対応
- [x] **API接続・データ表示** → `api.ts`, TypeScript型定義
- [x] **基本的なTailwind CSS styling** → レスポンシブデザイン
- [x] **レスポンシブ対応** → モバイル・デスクトップ対応

#### Step 4: 動作確認・プレビュー（15分）✅ 完了
- [x] **Docker環境で起動** (localhost:3000) → 正常動作確認
- [x] **OpenChatページ表示確認** → `/oc/123` でテスト済み
- [x] **API連携動作確認** → PHP ↔ NextJS 通信成功

**🎉 MVP完了条件：すべて達成**
- ✅ localhost:3000/oc/123 でOpenChat詳細ページが表示される
- ✅ localhost:7000のPHP APIと正常に連携する
- ✅ 基本的なレスポンシブデザインが機能する

### ✅ 追加実装完了：Advanced Chart Migration（2024年12月）
**目標**: `oc-review-graph`のMUIベースチャート機能をTailwind CSSに完全移植

#### Step 5: 高度なグラフ機能移植（2時間）✅ 完了
- [x] **oc-review-graph分析** → MUIコンポーネントとChart.js実装を調査
- [x] **PHP API強化** → 実際の統計データ、メンバー増減率、タグ情報を追加
- [x] **ChartControls作成** → 期間選択タブ（24時間、1週間、1ヶ月、全期間）
- [x] **MemberChart強化** → ズーム・パン機能、動的プラグインロード
- [x] **StatsGrid改良** → メンバー増減率表示、カラーコード化
- [x] **SSR対応** → Chart.js zoom pluginの動的ロード実装

#### Step 6: デバッグ・最適化（1時間）✅ 完了
- [x] **SSRエラー解決** → `window is not defined`エラーの修正
- [x] **resetZoom修正** → プラグインロード前の関数呼び出し防止
- [x] **状態管理改善** → zoomPluginLoadedのstate管理
- [x] **パフォーマンス最適化** → visibility change handling追加
- [x] **MCP Testing** → ブラウザでの実際の動作確認とデバッグ

**🚀 Advanced Chart Migration完了条件：すべて達成**
- ✅ 期間選択機能（24時間、1週間、1ヶ月、全期間）が正常動作
- ✅ ズーム・パン機能（全期間選択時のみ）が正常動作
- ✅ 実際のデータベースから615日分のデータを表示
- ✅ メンバー増減率（日次+0.3%、週次+1.0%）の正確な計算・表示
- ✅ SSR環境での完全動作（chart.js dynamic loading）
- ✅ モバイル・デスクトップ両対応のレスポンシブデザイン

**追加実装済み機能：**
- Chart.js によるメンバー数推移グラフ（高度な機能付き）
- 統計情報カード表示（変化率・カラーコード付き）
- 画像表示対応
- メタデータ・OGP対応
- TypeScript完全対応
- **期間選択コントロール**
- **ズーム・パン機能**
- **実データ統合（615+ データポイント）**
- **メンバー増減率計算**
- **タグ情報表示**

### 🔄 後回しフェーズ（MVP完成後）
#### Phase 2: 機能拡張
- [ ] トップページ実装
- [ ] チャート機能追加
- [ ] テスト環境セットアップ

#### Phase 3: 品質向上
- [ ] 包括的テスト追加
- [ ] パフォーマンス最適化
- [ ] CI/CD設定

## 注意事項

### 技術的制約
- **完全分離**: Next.jsは独立したリポジトリ・Docker環境
- 既存のPHPコード・データベース構造は**変更しない**
- 新しいAPIコントローラーのみ追加
- Next.jsは完全に**SSR**で動作（CSRは最小限）
- **自律開発**: Claude Codeが起動から動作確認まで一貫して実行可能
- **TDD方式**: テストファースト開発でコード品質を保証
- **自動化**: CI/CD、テスト、linting、formattingを自動化

### データ整合性
- 既存サイトと**同じデータ**を表示
- APIレスポンスの形式は将来の拡張を考慮
- エラーハンドリングの適切な実装

### SEO対応
- **メタタグ**の適切な設定
- **構造化データ**の実装
- **URL構造**は既存サイトと同じ

## Claude Code実行指示（MVP優先）

```bash
# 🎯 最優先：OpenChat詳細ページのMVP作成

## Step 1: 基盤構築
1. ディレクトリを作成してNext.jsプロジェクト作成（このディレクトリだけ独立してGITは別リポジトリにしたい）
2. 最小限のDocker環境構築
3. 基本パッケージインストール（Tailwind, axios）

## Step 2: PHP APIコントローラー追加
4. 既存PHPプロジェクトにOpenChat詳細API追加
5. CORS設定追加

## Step 3: Next.js実装
6. OpenChat詳細ページ実装 (/oc/[id])
7. 基本レイアウト・スタイリング
8. API連携

## Step 4: 動作確認
9. Docker環境で起動・プレビュー確認

# MVP完了条件：
- localhost:3000/oc/123 でOpenChat詳細ページが表示される
- localhost:8000のPHP APIと正常に連携する
- 基本的なレスポンシブデザインが機能する

# 🔄 後回し要素：
- トップページ
- テスト環境
- CI/CD
- 詳細なスタイリング
```

**まずはOpenChat詳細ページ1つだけを完成させて、すぐにプレビューできる状態にしてください。**