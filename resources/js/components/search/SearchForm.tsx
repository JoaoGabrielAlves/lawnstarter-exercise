import { type FC, type FormEvent, useState } from 'react';
import { SearchResourceType } from '../../types';

interface SearchFormProps {
  onSearch: (resource: SearchResourceType, query: string) => void;
  isLoading?: boolean;
}

const SearchForm: FC<SearchFormProps> = ({ onSearch, isLoading = false }) => {
  const [selectedResource, setSelectedResource] =
    useState<SearchResourceType>('people');
  const [query, setQuery] = useState('');

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (query.trim()) {
      onSearch(selectedResource, query.trim());
    }
  };

  const isFormValid = query.trim().length > 0;

  const getPlaceholder = () => {
    return selectedResource === 'people'
      ? 'e.g. Chewbacca, Yoda, Boba Fett'
      : 'e.g. A New Hope, Empire Strikes Back';
  };

  return (
    <div className='w-full max-w-[410px] h-[230px] p-[30px] bg-white border border-card-border rounded shadow-card flex flex-col'>
      <h2 className='text-[16px] font-bold text-black mb-[10px]'>
        What are you searching for?
      </h2>

      <div className='flex items-center gap-6 mb-5'>
        <label className='flex items-center gap-2.5 cursor-pointer'>
          <div className='relative'>
            <input
              type='radio'
              name='resource'
              value='people'
              checked={selectedResource === 'people'}
              onChange={(e) =>
                setSelectedResource(e.target.value as SearchResourceType)
              }
              className='sr-only'
            />
            <div
              className={`w-4 h-4 rounded-full border transition-colors ${
                selectedResource === 'people'
                  ? 'border-character-link'
                  : 'border-gray-300'
              } ${selectedResource === 'people' ? 'bg-character-link' : 'bg-white'}`}
            >
              {selectedResource === 'people' && (
                <div className='w-1 h-1 rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white'></div>
              )}
            </div>
          </div>
          <span className='text-[14px] font-bold text-black'>People</span>
        </label>

        <label className='flex items-center gap-2.5 cursor-pointer'>
          <div className='relative'>
            <input
              type='radio'
              name='resource'
              value='films'
              checked={selectedResource === 'films'}
              onChange={(e) =>
                setSelectedResource(e.target.value as SearchResourceType)
              }
              className='sr-only'
            />
            <div
              className={`w-4 h-4 rounded-full border transition-colors ${
                selectedResource === 'films'
                  ? 'border-character-link'
                  : 'border-gray-300'
              } ${selectedResource === 'films' ? 'bg-character-link' : 'bg-white'}`}
            >
              {selectedResource === 'films' && (
                <div className='w-1 h-1 rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white'></div>
              )}
            </div>
          </div>
          <span className='text-[14px] font-bold text-black'>Movies</span>
        </label>
      </div>

      <form onSubmit={handleSubmit} className='flex-1 flex flex-col'>
        <div className='mb-5'>
          <input
            type='text'
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder={getPlaceholder()}
            disabled={isLoading}
            className={`w-full h-10 px-2.5 border rounded text-[14px] font-bold bg-white ${
              query.trim()
                ? 'border-character-comma'
                : 'border-divider text-divider'
            }`}
          />
        </div>

        <button
          type='submit'
          disabled={!isFormValid || isLoading}
          className={`
            w-full h-8 text-sm font-bold text-white transition-colors rounded-[20px] border
            ${
              isFormValid
                ? 'bg-button-green border-button-green cursor-pointer'
                : 'bg-divider border-divider cursor-not-allowed'
            }
          `}
        >
          {isLoading ? 'SEARCHING...' : 'SEARCH'}
        </button>
      </form>
    </div>
  );
};

export default SearchForm;
