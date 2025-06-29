import { type FC } from 'react';
import {
  StarWarsSearchResult,
  SearchResourceType,
  PaginationMeta,
} from '../../types';
import { Pagination } from '../ui';

interface SearchResultsProps {
  results: StarWarsSearchResult[];
  resourceType: SearchResourceType;
  isLoading: boolean;
  onViewDetails: (id: number, type: SearchResourceType) => void;
  noResultsMessage?: string;
  meta?: PaginationMeta;
  currentPage?: number;
  onPageChange?: (page: number) => void;
}

const SearchResults: FC<SearchResultsProps> = ({
  results,
  resourceType,
  isLoading,
  onViewDetails,
  noResultsMessage,
  meta,
  currentPage = 1,
  onPageChange,
}) => {
  const showPagination = meta && onPageChange && meta.count > 0;

  return (
    <div className='w-full max-w-[582px] bg-white border border-card-border rounded shadow-card flex flex-col mb-4'>
      <div className='px-8 pt-8 flex-shrink-0'>
        <h2 className='text-lg font-bold text-black mb-4'>Results</h2>
        <div className='w-full h-px bg-divider'></div>
      </div>

      <div className='flex-1 flex flex-col overflow-hidden'>
        <div className='px-8 pb-4 flex-1 flex flex-col overflow-hidden'>
          {isLoading ? (
            <div className='flex-1 flex items-center justify-center'>
              <p className='text-[14px] font-bold text-divider'>Searching...</p>
            </div>
          ) : results.length === 0 ? (
            <div className='flex-1 flex items-center justify-center'>
              <p className='text-[14px] font-bold text-center max-w-xs text-divider'>
                {noResultsMessage ||
                  'There are zero matches. Use the form to search for People or Movies.'}
              </p>
            </div>
          ) : (
            <div className='flex-1 overflow-y-auto'>
              <div className='space-y-0'>
                {results.map((result, index) => {
                  const isLast = index === results.length - 1;
                  const displayName = result.name || result.title || 'Unknown';

                  return (
                    <div key={result.id}>
                      <div className='flex items-center justify-between py-4'>
                        <h3 className='text-base font-bold text-black flex-1 min-w-0 pr-4'>
                          <span className='truncate block'>{displayName}</span>
                        </h3>
                        <button
                          onClick={() => onViewDetails(result.id, resourceType)}
                          className={`
                            w-[187px] h-[34px] bg-button-green hover:bg-button-green-hover text-white text-[14px] font-bold border border-button-green hover:border-button-green-hover rounded-[17px] transition-colors duration-150 flex items-center justify-center
                          `}
                        >
                          SEE DETAILS
                        </button>
                      </div>
                      {!isLast && (
                        <div className='w-full h-px bg-divider'></div>
                      )}
                    </div>
                  );
                })}
              </div>
            </div>
          )}
        </div>

        {showPagination && (
          <Pagination
            meta={meta}
            currentPage={currentPage}
            onPageChange={onPageChange}
            isLoading={isLoading}
          />
        )}
      </div>
    </div>
  );
};

export default SearchResults;
