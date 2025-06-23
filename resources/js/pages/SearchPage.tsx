import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

import { SearchForm, SearchResults } from '@/components/search';

import { useStarWarsSearch } from '../hooks/useStarWars';
import { SearchResourceType } from '../types';

const SearchPage: React.FC = () => {
  const navigate = useNavigate();
  const [searchState, setSearchState] = useState<{
    resource: SearchResourceType;
    query: string;
    hasSearched: boolean;
    currentPage: number;
  }>({
    resource: 'people',
    query: '',
    hasSearched: false,
    currentPage: 1,
  });

  const {
    data: searchResults,
    isLoading,
    error,
  } = useStarWarsSearch(
    searchState.resource,
    searchState.query,
    searchState.currentPage,
    searchState.hasSearched && searchState.query.length > 0
  );

  const handleSearch = (resource: SearchResourceType, query: string) => {
    setSearchState({
      resource,
      query,
      hasSearched: true,
      currentPage: 1, // Reset to first page on new search
    });
  };

  const handlePageChange = (page: number) => {
    setSearchState((prev) => ({
      ...prev,
      currentPage: page,
    }));
  };

  const handleViewDetails = (id: number, type: SearchResourceType) => {
    if (type === 'people') {
      navigate(`/person/${id}`);
    } else if (type === 'films') {
      navigate(`/movie/${id}`);
    }
  };

  const results = searchResults?.data || [];
  const meta = searchResults?.meta;

  return (
    <div className='min-h-screen bg-gray-50 pt-20'>
      <div className='flex flex-col md:flex-row justify-center gap-8 px-4'>
        <SearchForm onSearch={handleSearch} isLoading={isLoading} />
        <SearchResults
          results={results}
          resourceType={searchState.resource}
          isLoading={isLoading}
          onViewDetails={handleViewDetails}
          meta={meta}
          currentPage={searchState.currentPage}
          onPageChange={handlePageChange}
          noResultsMessage={
            error
              ? `Error: ${error.message}`
              : `There are zero matches. Use the form to search for ${searchState.resource === 'people' ? 'People' : 'Movies'}.`
          }
        />
      </div>
    </div>
  );
};

export default SearchPage;
