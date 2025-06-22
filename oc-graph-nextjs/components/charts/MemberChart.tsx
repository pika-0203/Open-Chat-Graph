'use client';

import { useRef, useEffect, useState } from 'react';
import { Line } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ChartOptions,
  Filler,
} from 'chart.js';
import { MemberHistoryPoint } from '@/lib/types';
import { formatChartDate, formatNumber } from '@/lib/utils';
import ChartControls, { ChartPeriod } from './ChartControls';

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

// Dynamically import zoom plugin only on client-side

interface MemberChartProps {
  data: MemberHistoryPoint[];
  title?: string;
  className?: string;
}

export default function MemberChart({ data, title = 'メンバー数推移', className = '' }: MemberChartProps) {
  const chartRef = useRef<ChartJS<'line'>>(null);
  const [currentPeriod, setCurrentPeriod] = useState<ChartPeriod>('1m');
  const [enableZoom, setEnableZoom] = useState(false);
  const [isVisible, setIsVisible] = useState(true);
  const [zoomPluginLoaded, setZoomPluginLoaded] = useState(false);

  // Filter data based on selected period
  const getFilteredData = (period: ChartPeriod): MemberHistoryPoint[] => {
    if (period === 'all' || data.length === 0) return data;
    
    const now = new Date();
    let filterDate: Date;
    
    switch (period) {
      case '24h':
        filterDate = new Date(now.getTime() - 24 * 60 * 60 * 1000);
        break;
      case '1w':
        filterDate = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
        break;
      case '1m':
        filterDate = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
        break;
      default:
        return data;
    }
    
    return data.filter(point => new Date(point.date) >= filterDate);
  };

  const filteredData = getFilteredData(currentPeriod);

  // Load zoom plugin on client-side only
  useEffect(() => {
    const loadZoomPlugin = async () => {
      if (!zoomPluginLoaded && typeof window !== 'undefined') {
        try {
          const zoomPlugin = await import('chartjs-plugin-zoom');
          ChartJS.register(zoomPlugin.default);
          setZoomPluginLoaded(true);
        } catch (error) {
          console.warn('Failed to load zoom plugin:', error);
        }
      }
    };
    
    loadZoomPlugin();
  }, [zoomPluginLoaded]);

  // Handle browser visibility change for performance
  useEffect(() => {
    const handleVisibilityChange = () => {
      if (document.visibilityState === 'visible') {
        setIsVisible(true);
        // Re-render chart when page becomes visible
        if (chartRef.current) {
          chartRef.current.update('none');
        }
      } else {
        setIsVisible(false);
      }
    };

    document.addEventListener('visibilitychange', handleVisibilityChange);
    return () => document.removeEventListener('visibilitychange', handleVisibilityChange);
  }, []);

  // Reset zoom when period changes
  useEffect(() => {
    if (chartRef.current && zoomPluginLoaded && chartRef.current.resetZoom) {
      chartRef.current.resetZoom();
    }
  }, [currentPeriod]);

  const chartData = {
    labels: filteredData.map(point => {
      const date = new Date(point.date);
      if (currentPeriod === '24h') {
        return date.toLocaleString('ja-JP', { 
          month: 'numeric', 
          day: 'numeric', 
          hour: '2-digit', 
          minute: '2-digit' 
        });
      }
      return formatChartDate(point.date);
    }),
    datasets: [
      {
        label: 'メンバー数',
        data: filteredData.map(point => point.memberCount),
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        borderWidth: 2,
        pointRadius: filteredData.length > 100 ? 1 : 3,
        pointHoverRadius: 5,
        tension: 0.1,
        fill: true,
        pointBackgroundColor: 'rgb(59, 130, 246)',
        pointBorderColor: 'white',
        pointBorderWidth: 1,
      },
    ],
  };

  const options: ChartOptions<'line'> = {
    responsive: true,
    maintainAspectRatio: false,
    animation: {
      duration: isVisible ? 1000 : 0,
      easing: 'easeInOutQuart',
    },
    plugins: {
      legend: {
        display: false,
      },
      title: {
        display: false,
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return `メンバー数: ${formatNumber(context.parsed.y)}人`;
          },
          title: function(tooltipItems) {
            if (tooltipItems.length > 0) {
              const date = filteredData[tooltipItems[0].dataIndex]?.date;
              if (date) {
                return new Date(date).toLocaleDateString('ja-JP', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
                });
              }
            }
            return '';
          },
        },
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: 'white',
        bodyColor: 'white',
        borderColor: 'rgba(59, 130, 246, 0.5)',
        borderWidth: 1,
      },
      ...(zoomPluginLoaded && {
        zoom: {
          zoom: {
            wheel: {
              enabled: enableZoom,
            },
            pinch: {
              enabled: enableZoom,
            },
            mode: 'x',
          },
          pan: {
            enabled: enableZoom,
            mode: 'x',
          },
        },
      }),
    },
    scales: {
      x: {
        grid: {
          display: false,
        },
        border: {
          display: false,
        },
        ticks: {
          maxTicksLimit: currentPeriod === '24h' ? 6 : 8,
          color: 'rgba(107, 114, 128, 0.8)',
        },
      },
      y: {
        beginAtZero: false,
        grid: {
          color: 'rgba(0, 0, 0, 0.05)',
          drawBorder: false,
        },
        border: {
          display: false,
        },
        ticks: {
          callback: function(value) {
            return formatNumber(Number(value));
          },
          color: 'rgba(107, 114, 128, 0.8)',
          maxTicksLimit: 6,
        },
      },
    },
    elements: {
      point: {
        hoverBackgroundColor: 'rgb(59, 130, 246)',
        hoverBorderColor: 'white',
        hoverBorderWidth: 2,
      },
    },
    interaction: {
      intersect: false,
      mode: 'index',
    },
  };

  if (data.length === 0) {
    return (
      <div className={`bg-white rounded-lg shadow-sm border border-gray-200 ${className}`}>
        <div className="p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">{title}</h2>
          <div className="h-64 flex items-center justify-center text-gray-500">
            データがありません
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className={`bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden ${className}`}>
      <div className="p-6 pb-0">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">{title}</h2>
      </div>
      
      <ChartControls
        currentPeriod={currentPeriod}
        onPeriodChange={setCurrentPeriod}
        enableZoom={enableZoom}
        onZoomToggle={setEnableZoom}
        showZoomControls={currentPeriod === 'all'}
      />
      
      <div className="p-6 pt-4">
        <div className="h-64 sm:h-80 relative">
          {filteredData.length === 0 ? (
            <div className="h-full flex items-center justify-center text-gray-500">
              選択された期間にデータがありません
            </div>
          ) : (
            <Line ref={chartRef} data={chartData} options={options} />
          )}
        </div>
        
        {enableZoom && currentPeriod === 'all' && zoomPluginLoaded && (
          <div className="mt-4 text-center">
            <button
              onClick={() => chartRef.current?.resetZoom && chartRef.current.resetZoom()}
              className="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded text-gray-600 transition-colors"
            >
              ズームリセット
            </button>
          </div>
        )}
      </div>
    </div>
  );
}