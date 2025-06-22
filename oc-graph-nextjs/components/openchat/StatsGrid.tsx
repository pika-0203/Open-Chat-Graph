import { Rankings, MemberDiffStats } from '@/lib/types';
import { formatNumber } from '@/lib/utils';

interface StatsGridProps {
  memberCount: number;
  rankings: Rankings;
  memberDiff?: MemberDiffStats;
}

export default function StatsGrid({ memberCount, rankings, memberDiff }: StatsGridProps) {
  const formatChange = (difference: number, percentage: number) => {
    const sign = difference > 0 ? '+' : '';
    const diffText = `${sign}${formatNumber(difference)}`;
    const percentText = percentage !== 0 ? ` (${sign}${percentage.toFixed(1)}%)` : '';
    return `${diffText}${percentText}`;
  };

  const getChangeColor = (difference: number) => {
    if (difference > 0) return 'text-green-600';
    if (difference < 0) return 'text-red-600';
    return 'text-gray-600';
  };

  const stats = [
    {
      label: 'メンバー数',
      value: formatNumber(memberCount),
      suffix: '人',
      color: 'text-blue-600',
      change: memberDiff ? formatChange(memberDiff.daily.difference, memberDiff.daily.percentage) : null,
      changeColor: memberDiff ? getChangeColor(memberDiff.daily.difference) : 'text-gray-600',
    },
    {
      label: '7日間の変化',
      value: memberDiff ? formatChange(memberDiff.weekly.difference, memberDiff.weekly.percentage) : '-',
      suffix: '',
      color: memberDiff ? getChangeColor(memberDiff.weekly.difference) : 'text-gray-600',
    },
    {
      label: 'デイリーランキング',
      value: rankings.daily.position ? `${rankings.daily.position}位` : '-',
      suffix: '',
      color: 'text-green-600',
      change: rankings.daily.change !== 0 ? `${rankings.daily.change > 0 ? '+' : ''}${rankings.daily.change}` : null,
      changeColor: rankings.daily.change > 0 ? 'text-red-600' : rankings.daily.change < 0 ? 'text-green-600' : 'text-gray-600',
    },
    {
      label: 'ウィークリーランキング', 
      value: rankings.weekly.position ? `${rankings.weekly.position}位` : '-',
      suffix: '',
      color: 'text-purple-600',
      change: rankings.weekly.change !== 0 ? `${rankings.weekly.change > 0 ? '+' : ''}${rankings.weekly.change}` : null,
      changeColor: rankings.weekly.change > 0 ? 'text-red-600' : rankings.weekly.change < 0 ? 'text-green-600' : 'text-gray-600',
    },
  ];

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <h2 className="text-lg font-semibold text-gray-900 mb-4">統計情報</h2>
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((stat, index) => (
          <div key={index} className="text-center p-4 bg-gray-50 rounded-lg">
            <div className={`text-2xl font-bold ${stat.color} mb-1`}>
              {stat.value}
              {stat.suffix && <span className="text-sm ml-1">{stat.suffix}</span>}
            </div>
            <div className="text-sm text-gray-600 mb-1">{stat.label}</div>
            {stat.change && (
              <div className={`text-xs ${stat.changeColor} font-medium`}>
                {stat.change}
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}