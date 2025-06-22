import Link from "next/link";

export default function Home() {
  return (
    <div className="bg-gray-50 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Hero Section */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            OpenChat Graph
          </h1>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            LINE OpenChatのメンバー数統計とランキングを提供するサービスです。
            コミュニティの成長を可視化し、新しい出会いをサポートします。
          </p>
        </div>

        {/* Demo Section */}
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
          <h2 className="text-2xl font-semibold text-gray-900 mb-4">
            プロトタイプ版
          </h2>
          <p className="text-gray-600 mb-6">
            現在はOpenChat詳細ページのプロトタイプを公開しています。
            サンプルデータで動作を確認できます。
          </p>
          
          <div className="space-y-4">
            <div>
              <h3 className="font-medium text-gray-900 mb-2">サンプルページ</h3>
              <Link 
                href="/oc/123"
                className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
              >
                OpenChat詳細ページを見る
              </Link>
            </div>
            
            <p className="text-sm text-gray-500">
              ※ このプロトタイプは開発中のものです。実際のデータとは異なる場合があります。
            </p>
          </div>
        </div>

        {/* Features Section */}
        <div className="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="text-center">
            <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">統計情報</h3>
            <p className="text-gray-600">メンバー数の推移やランキング情報を詳細に確認できます</p>
          </div>
          
          <div className="text-center">
            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">成長の可視化</h3>
            <p className="text-gray-600">グラフで分かりやすくコミュニティの成長を確認できます</p>
          </div>
          
          <div className="text-center">
            <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">簡単検索</h3>
            <p className="text-gray-600">興味のあるトピックのOpenChatを素早く見つけられます</p>
          </div>
        </div>
      </div>
    </div>
  );
}
