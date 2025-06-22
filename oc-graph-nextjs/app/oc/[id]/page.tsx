import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { api } from '@/lib/api';
import OpenChatHeader from '@/components/openchat/OpenChatHeader';
import StatsGrid from '@/components/openchat/StatsGrid';
import MemberChart from '@/components/charts/MemberChart';

interface PageProps {
  params: Promise<{ id: string }>;
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { id } = await params;
  const openChatId = parseInt(id);

  if (isNaN(openChatId) || openChatId <= 0) {
    return {
      title: 'OpenChat not found',
    };
  }

  try {
    const data = await api.getOpenChatDetail(openChatId);
    
    return {
      title: `${data.openChat.name} - OpenChat Graph`,
      description: data.openChat.description || `${data.openChat.name}の統計情報とメンバー数推移を確認できます。`,
      openGraph: {
        title: data.openChat.name,
        description: data.openChat.description || `メンバー数: ${data.openChat.memberCount}人`,
        images: data.openChat.imgUrl ? [data.openChat.imgUrl] : [],
      },
    };
  } catch {
    return {
      title: 'OpenChat not found',
    };
  }
}

export default async function OpenChatDetailPage({ params }: PageProps) {
  const { id } = await params;
  const openChatId = parseInt(id);

  // Validate ID
  if (isNaN(openChatId) || openChatId <= 0) {
    notFound();
  }

  try {
    const data = await api.getOpenChatDetail(openChatId);
    
    return (
      <div className="min-h-screen bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* OpenChat Header */}
          <OpenChatHeader openChat={data.openChat} />
          
          {/* Statistics Grid */}
          <StatsGrid 
            memberCount={data.openChat.memberCount}
            rankings={data.statistics.rankings}
            memberDiff={data.openChat.memberDiff}
          />
          
          {/* Member Chart */}
          <MemberChart 
            data={data.statistics.memberHistory}
            rankings={data.statistics.rankings}
            rankingData={data.statistics.rankingHistory}
            title="メンバー数推移"
            className="mb-6"
          />
        </div>
      </div>
    );
  } catch (error) {
    // Handle different types of errors
    if (error instanceof Error && error.message === 'OpenChat not found') {
      notFound();
    }
    
    // For other errors, show a generic error page
    throw error;
  }
}