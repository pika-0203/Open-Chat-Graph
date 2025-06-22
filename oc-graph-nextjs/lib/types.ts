// OpenChat related types
export interface OpenChatSummary {
  id: number;
  name: string;
  description: string;
  memberCount: number;
  category: string;
  imgUrl: string;
  dailyGrowth: number;
  weeklyGrowth: number;
}

export interface MemberDifference {
  difference: number;
  percentage: number;
}

export interface MemberDiffStats {
  daily: MemberDifference;
  weekly: MemberDifference;
}

export interface OpenChatDetail {
  id: number;
  name: string;
  description: string;
  memberCount: number;
  category: string;
  tags: string[];
  imgUrl: string;
  lastUpdate: string;
  emblemUrl: string | null;
  memberDiff: MemberDiffStats;
}

export interface MemberHistoryPoint {
  date: string;
  memberCount: number;
}

export interface RankingPosition {
  position: number | null;
  change: number;
}

export interface Rankings {
  daily: RankingPosition;
  weekly: RankingPosition;
  total: RankingPosition;
}

export interface ChartMetadata {
  startDate: string;
  endDate: string;
  totalDataPoints: number;
}

export interface OpenChatStatistics {
  memberHistory: MemberHistoryPoint[];
  rankings: Rankings;
  chartMetadata: ChartMetadata;
}

// API Response types
export interface OpenChatDetailResponse {
  openChat: OpenChatDetail;
  statistics: OpenChatStatistics;
}

export interface SiteStats {
  totalOpenChats: number;
  totalMembers: number;
  lastUpdated: string;
}

export interface TopRankings {
  daily: OpenChatSummary[];
  weekly: OpenChatSummary[];
  total: OpenChatSummary[];
}

export interface HomePageResponse {
  siteStats: SiteStats;
  topRankings: TopRankings;
  recentlyAdded: OpenChatSummary[];
}

// Error types
export interface ApiError {
  error: string;
}