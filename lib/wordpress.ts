import axios, { AxiosInstance } from 'axios';

const API_URL = typeof window === 'undefined' 
  ? (process.env.WORDPRESS_API_URL || 'http://localhost/projects/wp-json')
  : (process.env.NEXT_PUBLIC_WORDPRESS_API_URL || 'http://localhost/projects/wp-json');

// Create axios instance
const wpApi: AxiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add request interceptor for authentication
wpApi.interceptors.request.use(
  (config) => {
    const token = typeof window !== 'undefined' ? localStorage.getItem('authToken') : null;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor for error handling
wpApi.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      if (typeof window !== 'undefined') {
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
      }
    }
    return Promise.reject(error);
  }
);

// API Methods
export const wordpressApi = {
  // Posts
  getPosts: async (params?: { page?: number; per_page?: number; search?: string }) => {
    const response = await wpApi.get('/wp/v2/posts', { params });
    return {
      posts: response.data,
      total: parseInt(response.headers['x-wp-total'] || '0'),
      totalPages: parseInt(response.headers['x-wp-totalpages'] || '0'),
    };
  },

  getPost: async (slug: string) => {
    const response = await wpApi.get(`/wp/v2/posts`, {
      params: { slug },
    });
    return response.data[0];
  },

  getPostById: async (id: number) => {
    const response = await wpApi.get(`/wp/v2/posts/${id}`);
    return response.data;
  },

  // Projects
  getProjects: async (params?: { page?: number; per_page?: number }) => {
    const response = await wpApi.get('/wp/v2/projects', { params });
    return {
      projects: response.data,
      total: parseInt(response.headers['x-wp-total'] || '0'),
      totalPages: parseInt(response.headers['x-wp-totalpages'] || '0'),
    };
  },

  getProject: async (slug: string) => {
    const response = await wpApi.get(`/wp/v2/projects`, {
      params: { slug },
    });
    return response.data[0];
  },

  getFeaturedProjects: async () => {
    const response = await wpApi.get('/headless/v1/featured-projects');
    return response.data;
  },

  // Team Members
  getTeamMembers: async () => {
    const response = await wpApi.get('/wp/v2/team');
    return response.data;
  },

  // Testimonials
  getTestimonials: async () => {
    const response = await wpApi.get('/wp/v2/testimonials');
    return response.data;
  },

  // Categories
  getCategories: async () => {
    const response = await wpApi.get('/wp/v2/categories');
    return response.data;
  },

  getProjectCategories: async () => {
    const response = await wpApi.get('/wp/v2/project-categories');
    return response.data;
  },

  // Tags
  getTags: async () => {
    const response = await wpApi.get('/wp/v2/tags');
    return response.data;
  },

  // Custom Endpoints
  getHomepageData: async () => {
    const response = await wpApi.get('/headless/v1/homepage');
    return response.data;
  },

  getMenu: async (location: string) => {
    const response = await wpApi.get(`/headless/v1/menus/${location}`);
    return response.data;
  },

  getSiteSettings: async () => {
    const response = await wpApi.get('/headless/v1/settings');
    return response.data;
  },

  search: async (query: string) => {
    const response = await wpApi.get('/headless/v1/search', {
      params: { query },
    });
    return response.data;
  },

  getRelatedPosts: async (id: number) => {
    const response = await wpApi.get(`/headless/v1/related/${id}`);
    return response.data;
  },

  // Contact Form
  submitContactForm: async (data: {
    name: string;
    email: string;
    message: string;
  }) => {
    const response = await wpApi.post('/headless/v1/contact', data);
    return response.data;
  },

  // Authentication
  login: async (username: string, password: string) => {
    const response = await wpApi.post('/headless/v1/auth/login', {
      username,
      password,
    });
    
    if (response.data.token && typeof window !== 'undefined') {
      localStorage.setItem('authToken', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
    }
    
    return response.data;
  },

  logout: async () => {
    try {
      await wpApi.post('/headless/v1/auth/logout');
    } finally {
      if (typeof window !== 'undefined') {
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
      }
    }
  },

  validateToken: async () => {
    const response = await wpApi.post('/headless/v1/auth/validate');
    return response.data;
  },

  getCurrentUser: async () => {
    const response = await wpApi.get('/headless/v1/auth/me');
    return response.data;
  },

  refreshToken: async () => {
    const response = await wpApi.post('/headless/v1/auth/refresh');
    
    if (response.data.token && typeof window !== 'undefined') {
      localStorage.setItem('authToken', response.data.token);
    }
    
    return response.data;
  },
};

export default wpApi;