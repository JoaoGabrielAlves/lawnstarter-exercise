export interface StarWarsPerson {
  id: number;
  name: string;
  birth_year: string;
  gender: string;
  eye_color: string;
  hair_color: string;
  height: string;
  mass: string;
  films: Array<{
    id: number;
    title: string;
  }>;
}

export interface StarWarsFilm {
  id: number;
  title: string;
  episode_id: number;
  opening_crawl: string;
  director: string;
  producer: string;
  release_date: string;
  characters: Array<{
    id: number;
    name: string;
  }>;
}

export interface StarWarsSearchResult {
  id: number;
  name?: string;
  title?: string;
  [key: string]: unknown;
}

export type SearchResourceType = 'people' | 'films';
