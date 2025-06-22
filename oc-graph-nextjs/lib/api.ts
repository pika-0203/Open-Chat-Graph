import axios from 'axios';
import { OpenChatDetailResponse, HomePageResponse } from './types';

// Use different URLs for server-side vs client-side
const API_BASE_URL = typeof window === 'undefined' 
  ? 'http://host.docker.internal:7000'  // Server-side: access from Docker container
  : process.env.NEXT_PUBLIC_API_URL || 'http://localhost:7000'; // Client-side

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// API functions
export const api = {
  /**
   * Get OpenChat detail by ID
   */
  async getOpenChatDetail(id: number): Promise<OpenChatDetailResponse> {
    try {
      const response = await apiClient.get<OpenChatDetailResponse>(`/api/nextjs/openchat/${id}`);
      return response.data;
    } catch (error) {
      if (axios.isAxiosError(error)) {
        if (error.response?.status === 404) {
          throw new Error('OpenChat not found');
        }
        if (error.response?.data?.error) {
          throw new Error(error.response.data.error);
        }
      }
      throw new Error('Failed to fetch OpenChat details');
    }
  },

  /**
   * Get home page data (to be implemented later)
   */
  async getHomePageData(): Promise<HomePageResponse> {
    try {
      const response = await apiClient.get<HomePageResponse>('/api/nextjs/home');
      return response.data;
    } catch (error) {
      if (axios.isAxiosError(error) && error.response?.data?.error) {
        throw new Error(error.response.data.error);
      }
      throw new Error('Failed to fetch home page data');
    }
  },
};

export default api;