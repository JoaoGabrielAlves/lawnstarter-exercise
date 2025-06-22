import { useQuery } from '@tanstack/react-query';
import { starWarsApi } from '../services/starWarsApi';
import { SearchResourceType } from '../types';

export const useStarWarsSearch = (
  resource: SearchResourceType,
  query: string,
  page: number = 1,
  enabled: boolean = true
) => {
  return useQuery({
    queryKey: ['starwars', 'search', resource, query, page],
    queryFn: () => starWarsApi.search(resource, query, page),
    enabled: enabled && query.length > 0,
    staleTime: 5 * 60 * 1000,
  });
};

export const useStarWarsPerson = (id: number) => {
  return useQuery({
    queryKey: ['starwars', 'person', id],
    queryFn: () => starWarsApi.getPerson(id),
    staleTime: 30 * 60 * 1000,
  });
};

export const useStarWarsFilm = (id: number) => {
  return useQuery({
    queryKey: ['starwars', 'film', id],
    queryFn: () => starWarsApi.getFilm(id),
    staleTime: 30 * 60 * 1000,
  });
};

export const useStarWarsPeople = (search?: string, page: number = 1) => {
  return useQuery({
    queryKey: ['starwars', 'people', search, page],
    queryFn: () => starWarsApi.getPeople(search, page),
    staleTime: 5 * 60 * 1000,
  });
};

export const useStarWarsFilms = (search?: string, page: number = 1) => {
  return useQuery({
    queryKey: ['starwars', 'films', search, page],
    queryFn: () => starWarsApi.getFilms(search, page),
    staleTime: 5 * 60 * 1000,
  });
};
