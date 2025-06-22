import Image from 'next/image';
import { OpenChatDetail } from '@/lib/types';
import { formatNumber, formatDate } from '@/lib/utils';

interface OpenChatHeaderProps {
  openChat: OpenChatDetail;
}

export default function OpenChatHeader({ openChat }: OpenChatHeaderProps) {
  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div className="flex flex-col sm:flex-row gap-6">
        {/* OpenChat Image */}
        <div className="flex-shrink-0">
          <div className="relative w-24 h-24 sm:w-32 sm:h-32">
            <Image
              src={openChat.imgUrl || '/placeholder.png'}
              alt={openChat.name}
              fill
              className="rounded-lg object-cover"
              priority
              unoptimized
            />
            {openChat.emblemUrl && (
              <div className="absolute -bottom-2 -right-2 w-8 h-8">
                <Image
                  src={openChat.emblemUrl}
                  alt="Emblem"
                  fill
                  className="rounded-full object-cover"
                />
              </div>
            )}
          </div>
        </div>

        {/* OpenChat Info */}
        <div className="flex-1 min-w-0">
          <h1 className="text-2xl font-bold text-gray-900 mb-2 break-words">
            {openChat.name}
          </h1>
          
          <div className="flex flex-wrap gap-4 mb-4 text-sm text-gray-600">
            <div className="flex items-center">
              <span className="font-medium">メンバー数:</span>
              <span className="ml-1 text-blue-600 font-semibold">
                {formatNumber(openChat.memberCount)}人
              </span>
            </div>
            <div className="flex items-center">
              <span className="font-medium">カテゴリ:</span>
              <span className="ml-1">{openChat.category}</span>
            </div>
            <div className="flex items-center">
              <span className="font-medium">最終更新:</span>
              <span className="ml-1">{formatDate(openChat.lastUpdate)}</span>
            </div>
          </div>

          {openChat.description && (
            <div className="mb-4">
              <h2 className="text-sm font-medium text-gray-900 mb-2">説明</h2>
              <p className="text-gray-700 text-sm leading-relaxed break-words">
                {openChat.description}
              </p>
            </div>
          )}

          {openChat.tags && openChat.tags.length > 0 && (
            <div>
              <h3 className="text-sm font-medium text-gray-900 mb-2">タグ</h3>
              <div className="flex flex-wrap gap-2">
                {openChat.tags.map((tag, index) => (
                  <span
                    key={index}
                    className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {tag}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}