import { api } from './api';

/**
 * Star Wars API service layer
 * Handles all Star Wars API interactions with proper separation of concerns
 */
export const starWarsApi = {
  search: (resource: 'people' | 'films', query: string, page = 1) => {
    const params = new URLSearchParams({ q: query, page: page.toString() });
    return api.get(`/v1/starwars/search/${resource}?${params}`);
  },

  getPeople: (search?: string, page = 1) => {
    const params = new URLSearchParams({ page: page.toString() });
    if (search) params.append('search', search);
    return api.get(`/v1/starwars/people?${params}`);
  },

  getPerson: (id: number) => {
    return api.get(`/v1/starwars/people/${id}`);
  },

  getFilms: (search?: string, page = 1) => {
    const params = new URLSearchParams({ page: page.toString() });
    if (search) params.append('search', search);
    return api.get(`/v1/starwars/films?${params}`);
  },

  getFilm: (id: number) => {
    return api.get(`/v1/starwars/films/${id}`);
  },
};
