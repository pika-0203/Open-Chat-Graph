'use client';

import { useRef, useEffect, useState } from 'react';
import { Chart } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ChartOptions,
  Filler,
  ChartData,
  ChartArea,
} from 'chart.js';
import { MemberHistoryPoint, Rankings } from '@/lib/types';
import { formatChartDate, formatNumber } from '@/lib/utils';
import ChartControls, { ChartPeriod } from './ChartControls';

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

// Dynamically import zoom plugin only on client-side

interface MemberChartProps {
  data: MemberHistoryPoint[];
  rankings?: Rankings;
  rankingData?: number[]; // Same length as data array, null for no ranking
  title?: string;
  className?: string;
}

// Custom gradient functions
function createMemberGradient(ctx: CanvasRenderingContext2D, chartArea: ChartArea) {
  const gradient = ctx.createLinearGradient(0, chartArea.height / 2, chartArea.width, 0);
  gradient.addColorStop(1, 'rgba(0, 183, 96, 1.0)');
  gradient.addColorStop(0.8, 'rgba(17, 216, 113, 1.0)');
  gradient.addColorStop(0.5, 'rgba(17, 213, 147, 1.0)');
  gradient.addColorStop(0.3, 'rgba(18, 207, 205, 1.0)');
  gradient.addColorStop(0, 'rgba(22, 194, 193, 1.0)');
  return gradient;
}

function createRankingGradient(ctx: CanvasRenderingContext2D, chartArea: ChartArea) {
  const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
  gradient.addColorStop(1, 'rgba(0, 183, 96, 0.2)');
  gradient.addColorStop(0.7, 'rgba(17, 216, 113, 0.2)');
  gradient.addColorStop(0.5, 'rgba(17, 213, 147, 0.2)');
  gradient.addColorStop(0.3, 'rgba(18, 207, 205, 0.2)');
  gradient.addColorStop(0, 'rgba(22, 194, 193, 0.2)');
  return gradient;
}

// Utility functions for label ranges
function getVerticalLabelRange(data: number[]) {
  const validData = data.filter(v => v !== null && v !== undefined);
  if (validData.length === 0) return { dataMin: 0, dataMax: 100, stepSize: 10 };
  
  const dataMin = Math.min(...validData);
  const dataMax = Math.max(...validData);
  const range = dataMax - dataMin;
  const stepSize = Math.ceil(range / 6);
  
  return {
    dataMin: Math.max(0, dataMin - stepSize),
    dataMax: dataMax + stepSize,
    stepSize,
  };
}

function getRankingLabelRange(data: (number | null)[]) {
  const validData = data.filter(v => v !== null && v !== undefined) as number[];
  if (validData.length === 0) return { dataMin: 0, dataMax: 100, stepSize: 10 };
  
  const dataMin = Math.min(...validData);
  const dataMax = Math.max(...validData);
  const range = dataMax - dataMin;
  const stepSize = Math.ceil(range / 6);
  
  return {
    dataMin: Math.max(0, dataMin - stepSize),
    dataMax: dataMax + stepSize,
    stepSize,
  };
}

export default function MemberChart({ 
  data, 
  rankings, 
  rankingData = [], 
  title = 'メンバー数推移', 
  className = '' 
}: MemberChartProps) {
  const chartRef = useRef<ChartJS>(null);
  const [currentPeriod, setCurrentPeriod] = useState<ChartPeriod>('1m');
  const [enableZoom, setEnableZoom] = useState(false);
  const [isVisible, setIsVisible] = useState(true);
  const [zoomPluginLoaded, setZoomPluginLoaded] = useState(false);
  const [isMobile, setIsMobile] = useState(false);

  // Detect mobile on mount
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768);
    };
    checkMobile();
    window.addEventListener('resize', checkMobile);
    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  // Filter data based on selected period
  const getFilteredData = (period: ChartPeriod): {
    memberData: MemberHistoryPoint[];
    rankingData: (number | null)[];
  } => {
    if (period === 'all' || data.length === 0) {
      return { memberData: data, rankingData };
    }
    
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
        return { memberData: data, rankingData };
    }
    
    const filteredIndexes: number[] = [];
    const filteredMemberData = data.filter((point, index) => {
      const isIncluded = new Date(point.date) >= filterDate;
      if (isIncluded) filteredIndexes.push(index);
      return isIncluded;
    });
    
    const filteredRankingData = filteredIndexes.map(index => rankingData[index] || null);
    
    return { memberData: filteredMemberData, rankingData: filteredRankingData };
  };

  const { memberData: filteredData, rankingData: filteredRankingData } = getFilteredData(currentPeriod);

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
  }, [currentPeriod, zoomPluginLoaded]);

  // Calculate label ranges
  const memberCounts = filteredData.map(point => point.memberCount);
  const memberLabelRange = getVerticalLabelRange(memberCounts);
  const rankingLabelRange = getRankingLabelRange(filteredRankingData);
  const hasRankingData = filteredRankingData.some(v => v !== null && v !== undefined);

  // Build chart data
  const chartData: ChartData = {
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
        type: 'line' as const,
        label: 'メンバー数',
        data: memberCounts,
        borderColor: function(context) {
          const chart = context.chart;
          const { ctx, chartArea } = chart;
          if (!chartArea) return 'rgb(0, 183, 96)';
          return createMemberGradient(ctx, chartArea);
        },
        backgroundColor: 'rgba(0,0,0,0)',
        borderWidth: 3,
        pointRadius: (ctx) => {
          const dataLength = ctx.dataset.data.length;
          return dataLength > 100 ? 1 : 3;
        },
        pointHoverRadius: 5,
        tension: 0.4,
        fill: false,
        pointBackgroundColor: '#fff',
        pointBorderColor: 'rgba(0, 183, 96, 1.0)',
        pointBorderWidth: 1,
        yAxisID: 'memberAxis',
        spanGaps: true,
      },
    ],
  };

  // Add ranking bar dataset if data exists
  if (hasRankingData) {
    chartData.datasets.push({
      type: 'bar' as const,
      label: 'ランキング',
      data: filteredRankingData,
      backgroundColor: function(context) {
        const chart = context.chart;
        const { ctx, chartArea } = chart;
        if (!chartArea) return 'rgba(0, 183, 96, 0.2)';
        return createRankingGradient(ctx, chartArea);
      },
      borderRadius: filteredData.length < 10 ? 4 : 2,
      barPercentage: currentPeriod === '1w' ? 0.77 : 0.9,
      yAxisID: 'rankingAxis',
    });
  }

  // Build chart options with dual Y-axis
  const options: ChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    aspectRatio: isMobile ? 1.2 : 1.8,
    animation: {
      duration: isVisible ? 1000 : 0,
      easing: 'easeOutQuart',
    },
    layout: {
      padding: {
        top: 0,
        left: 0,
        right: hasRankingData ? 0 : 24,
        bottom: hasRankingData ? 0 : 9,
      },
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
            if (context.datasetIndex === 0) {
              return `メンバー数: ${formatNumber(context.parsed.y)}人`;
            } else {
              const rankingValue = context.parsed.y;
              return rankingValue ? `ランキング: ${rankingValue}位` : 'ランキング: 圏外';
            }
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
        borderColor: 'rgba(0, 183, 96, 0.5)',
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
            onZoomComplete: (context) => {
              // Dynamic Y-axis rescaling on zoom
              const chart = context.chart;
              const xMin = chart.scales.x.min;
              const xMax = chart.scales.x.max;
              
              if (xMin !== undefined && xMax !== undefined) {
                const visibleMemberData = memberCounts.slice(Math.floor(xMin), Math.ceil(xMax) + 1);
                const visibleRankingData = filteredRankingData.slice(Math.floor(xMin), Math.ceil(xMax) + 1);
                
                const newMemberRange = getVerticalLabelRange(visibleMemberData);
                const newRankingRange = getRankingLabelRange(visibleRankingData);
                
                chart.options.scales!.memberAxis!.min = newMemberRange.dataMin;
                chart.options.scales!.memberAxis!.max = newMemberRange.dataMax;
                
                if (hasRankingData) {
                  chart.options.scales!.rankingAxis!.min = newRankingRange.dataMin;
                  chart.options.scales!.rankingAxis!.max = newRankingRange.dataMax;
                }
                
                chart.update('none');
              }
            },
          },
          pan: {
            enabled: enableZoom,
            mode: 'x',
            onPanComplete: (context) => {
              // Dynamic Y-axis rescaling on pan
              const chart = context.chart;
              const xMin = chart.scales.x.min;
              const xMax = chart.scales.x.max;
              
              if (xMin !== undefined && xMax !== undefined) {
                const visibleMemberData = memberCounts.slice(Math.floor(xMin), Math.ceil(xMax) + 1);
                const visibleRankingData = filteredRankingData.slice(Math.floor(xMin), Math.ceil(xMax) + 1);
                
                const newMemberRange = getVerticalLabelRange(visibleMemberData);
                const newRankingRange = getRankingLabelRange(visibleRankingData);
                
                chart.options.scales!.memberAxis!.min = newMemberRange.dataMin;
                chart.options.scales!.memberAxis!.max = newMemberRange.dataMax;
                
                if (hasRankingData) {
                  chart.options.scales!.rankingAxis!.min = newRankingRange.dataMin;
                  chart.options.scales!.rankingAxis!.max = newRankingRange.dataMax;
                }
                
                chart.update('none');
              }
            },
          },
          limits: {
            x: { minRange: 7 },
          },
        },
      }),
    },
    scales: {
      x: {
        grid: {
          display: hasRankingData ? true : false,
          color: '#efefef',
        },
        border: {
          display: false,
        },
        ticks: {
          maxTicksLimit: currentPeriod === '24h' ? 6 : 8,
          color: 'rgba(107, 114, 128, 0.8)',
          padding: hasRankingData ? 20 : currentPeriod === '1w' ? 10 : 3,
          autoSkip: true,
          maxRotation: 90,
          font: {
            size: isMobile ? 11 : 12,
          },
        },
      },
      memberAxis: {
        type: 'linear',
        position: 'left',
        min: memberLabelRange.dataMin,
        max: memberLabelRange.dataMax,
        display: currentPeriod !== '1w',
        grid: {
          color: 'rgba(0, 0, 0, 0.05)',
          drawBorder: false,
        },
        border: {
          display: false,
        },
        ticks: {
          callback: function(value) {
            if (Number(value) === 0) return '1';
            return formatNumber(Number(value));
          },
          stepSize: memberLabelRange.stepSize,
          precision: 0,
          autoSkip: true,
          padding: currentPeriod === '1w' ? 0 : 5,
          color: '#aaa',
          font: {
            size: isMobile ? 11 : 12,
          },
        },
      },
      ...(hasRankingData && {
        rankingAxis: {
          type: 'linear',
          position: 'right',
          min: rankingLabelRange.dataMin,
          max: rankingLabelRange.dataMax,
          display: currentPeriod !== '1w' && filteredRankingData.some(v => v !== null && v !== 0),
          grid: {
            display: false,
          },
          ticks: {
            display: currentPeriod !== '1w' && filteredRankingData.some(v => v !== null && v !== 0),
            callback: function(value) {
              const rankValue = Math.ceil(Number(value));
              if (!rankValue) return '';
              return `${rankValue}位`;
            },
            stepSize: rankingLabelRange.stepSize,
            autoSkip: true,
            maxTicksLimit: 14,
            color: '#aaa',
            font: {
              size: isMobile ? 11 : 12,
            },
          },
        },
      }),
    },
    elements: {
      point: {
        hoverBackgroundColor: 'rgba(0, 183, 96, 1.0)',
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
            <Chart ref={chartRef} type={hasRankingData ? 'bar' : 'line'} data={chartData} options={options} />
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