import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

import SearchForm from '@/components/SearchForm';
import SearchResults from '@/components/SearchResults';

import { useStarWarsSearch } from '../hooks/useStarWars';
import { SearchResourceType } from '../types';

const SearchPage: React.FC = () => {
  const navigate = useNavigate();
  const [searchState, setSearchState] = useState<{
    resource: SearchResourceType;
    query: string;
    hasSearched: boolean;
  }>({
    resource: 'people',
    query: '',
    hasSearched: false,
  });

  const {
    data: searchResults,
    isLoading,
    error,
  } = useStarWarsSearch(
    searchState.resource,
    searchState.query,
    1,
    searchState.hasSearched && searchState.query.length > 0
  );

  const handleSearch = (resource: SearchResourceType, query: string) => {
    setSearchState({
      resource,
      query,
      hasSearched: true,
    });
  };

  const handleViewDetails = (id: number, type: SearchResourceType) => {
    if (type === 'people') {
      navigate(`/person/${id}`);
    } else if (type === 'films') {
      navigate(`/movie/${id}`);
    }
  };

  const results = searchResults?.data || [];

  return (
    <div className='min-h-screen bg-gray-50 pt-20'>
      <div className='flex justify-center gap-8'>
        <SearchForm onSearch={handleSearch} isLoading={isLoading} />
        <SearchResults
          results={results}
          resourceType={searchState.resource}
          isLoading={isLoading}
          onViewDetails={handleViewDetails}
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
