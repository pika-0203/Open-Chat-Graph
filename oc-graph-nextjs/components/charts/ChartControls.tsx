'use client';

import { useState } from 'react';

export type ChartPeriod = '24h' | '1w' | '1m' | 'all';

interface ChartControlsProps {
  currentPeriod: ChartPeriod;
  onPeriodChange: (period: ChartPeriod) => void;
  enableZoom?: boolean;
  onZoomToggle?: (enabled: boolean) => void;
  showZoomControls?: boolean;
}

const PERIOD_OPTIONS = [
  { value: '24h' as ChartPeriod, label: '最新24時間' },
  { value: '1w' as ChartPeriod, label: '1週間' },
  { value: '1m' as ChartPeriod, label: '1ヶ月' },
  { value: 'all' as ChartPeriod, label: '全期間' },
];

export default function ChartControls({
  currentPeriod,
  onPeriodChange,
  enableZoom = false,
  onZoomToggle,
  showZoomControls = false,
}: ChartControlsProps) {
  return (
    <div className="bg-white border-b border-gray-200">
      {/* Period Selection Tabs */}
      <div className="flex w-full">
        {PERIOD_OPTIONS.map((option) => (
          <button
            key={option.value}
            onClick={() => onPeriodChange(option.value)}
            className={`
              flex-1 py-3 px-4 text-sm font-medium text-center border-b-2 transition-colors
              ${
                currentPeriod === option.value
                  ? 'border-blue-500 text-blue-600 bg-blue-50'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }
            `}
          >
            {option.label}
          </button>
        ))}
      </div>

      {/* Zoom Controls (only show for 'all' period) */}
      {showZoomControls && currentPeriod === 'all' && onZoomToggle && (
        <div className="px-4 py-3 border-b border-gray-100 bg-gray-50">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-medium text-gray-700">
              ランキングの順位を表示
            </h3>
            <div className="flex items-center space-x-4">
              <label className="flex items-center space-x-2">
                <input
                  type="checkbox"
                  checked={enableZoom}
                  onChange={(e) => onZoomToggle(e.target.checked)}
                  className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span className="text-xs text-gray-600">グラフの移動・拡大</span>
              </label>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}