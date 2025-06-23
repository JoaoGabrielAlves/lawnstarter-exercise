import { type FC } from 'react';
import { PaginationMeta } from '../types';
import PageIndicator from './PageIndicator';

interface PaginationProps {
  meta: PaginationMeta;
  currentPage: number;
  onPageChange: (page: number) => void;
  isLoading?: boolean;
}

const Pagination: FC<PaginationProps> = ({
  meta,
  currentPage,
  onPageChange,
  isLoading = false,
}) => {
  const { count, next, previous } = meta;

  // Calculate total pages (assuming 10 results per page based on SWAPI default)
  const resultsPerPage = 10;
  const totalPages = Math.ceil(count / resultsPerPage);

  const hasPrevious = previous !== null;
  const hasNext = next !== null;

  const handlePrevious = () => {
    if (hasPrevious && !isLoading) {
      onPageChange(currentPage - 1);
    }
  };

  const handleNext = () => {
    if (hasNext && !isLoading) {
      onPageChange(currentPage + 1);
    }
  };

  // Don't render pagination if there's only one page or no results
  if (count <= resultsPerPage) {
    return null;
  }

  return (
    <div className='flex items-center justify-between px-8 py-4 border-t border-divider'>
      <div className='flex items-center justify-between w-full'>
        <button
          onClick={handlePrevious}
          disabled={!hasPrevious || isLoading}
          className={`
            px-4 py-2 text-[14px] font-bold rounded border transition-colors duration-150
            ${
              hasPrevious && !isLoading
                ? 'bg-white border-button-green text-button-green hover:bg-button-green hover:text-white'
                : 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
            }
          `}
        >
          Previous
        </button>

        <PageIndicator
          currentPage={currentPage}
          totalPages={totalPages}
          totalResults={count}
        />

        <button
          onClick={handleNext}
          disabled={!hasNext || isLoading}
          className={`
            px-4 py-2 text-[14px] font-bold rounded border transition-colors duration-150
            ${
              hasNext && !isLoading
                ? 'bg-white border-button-green text-button-green hover:bg-button-green hover:text-white'
                : 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
            }
          `}
        >
          Next
        </button>
      </div>
    </div>
  );
};

export default Pagination;
