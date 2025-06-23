import { type FC } from 'react';

interface PageIndicatorProps {
  currentPage: number;
  totalPages: number;
  totalResults: number;
}

const PageIndicator: FC<PageIndicatorProps> = ({
  currentPage,
  totalPages,
  totalResults,
}) => {
  return (
    <div className='text-[14px] text-gray-600 text-center'>
      <span>
        Page {currentPage} of {totalPages}
      </span>
      <span className='mx-2'>•</span>
      <span>
        {totalResults} total result{totalResults !== 1 ? 's' : ''}
      </span>
    </div>
  );
};

export default PageIndicator;
