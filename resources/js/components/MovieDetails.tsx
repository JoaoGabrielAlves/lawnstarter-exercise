import { type FC, Fragment } from 'react';
import { StarWarsFilm } from '../types';

interface MovieDetailsProps {
  film: StarWarsFilm;
  characters?: Array<{ id: number; name: string }>; // Characters loaded separately
  onBackToSearch: () => void;
  onCharacterClick?: (characterId: number) => void;
  isLoading?: boolean;
  isCharactersLoading?: boolean; // Separate loading state for characters
}

const MovieDetails: FC<MovieDetailsProps> = ({
  film,
  characters = [],
  onBackToSearch,
  onCharacterClick,
  isLoading = false,
  isCharactersLoading = false,
}) => {
  if (isLoading) {
    return (
      <div className='w-full max-w-[804px] h-[calc(100vh-5rem)] md:h-[537px] bg-white border border-card-border rounded shadow-card flex items-center justify-center mx-auto overflow-hidden'>
        <p className='text-sm font-bold text-divider'>Loading...</p>
      </div>
    );
  }

  const formatCharacters = (
    characters: Array<{ id: number; name: string }>
  ) => {
    return characters.map((character, index) => {
      const isLast = index === characters.length - 1;
      return (
        <Fragment key={character.id}>
          <span
            className='text-[14px] text-character-link hover:underline cursor-pointer font-normal'
            onClick={() => onCharacterClick?.(character.id)}
          >
            {character.name}
          </span>
          {!isLast && (
            <span className='text-[14px] text-character-comma font-normal'>
              ,
            </span>
          )}
        </Fragment>
      );
    });
  };

  const renderCharactersSection = () => {
    if (isCharactersLoading) {
      return (
        <div className='text-[14px] font-normal text-divider'>
          Loading characters...
        </div>
      );
    }

    if (characters && characters.length > 0) {
      return (
        <div className='flex flex-wrap'>{formatCharacters(characters)}</div>
      );
    }

    return (
      <p className='text-[14px] text-divider font-normal'>
        No characters available
      </p>
    );
  };

  return (
    <div className='w-full max-w-[804px] h-[537px] bg-white border border-card-border rounded shadow-[0_1px_2px_0_rgba(132,132,132,0.75)] p-4 md:p-[30px] flex flex-col mx-auto overflow-hidden'>
      <h1 className='text-[18px] font-bold text-black mb-4 md:mb-[32px] flex-shrink-0'>
        {film.title}
      </h1>

      <div className='flex-1 overflow-y-auto md:overflow-visible'>
        <div className='grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-[48px] h-full'>
          <div className='flex flex-col min-w-0'>
            <div className='mb-2 md:mb-[10px] flex-shrink-0'>
              <h2 className='text-[16px] font-bold text-black mb-[6px]'>
                Opening Crawl
              </h2>
              <div className='w-full h-px bg-divider'></div>
            </div>

            <div className='flex-1 md:max-h-[325px] text-[14px] text-black leading-relaxed whitespace-pre-line font-normal md:overflow-y-auto'>
              {film.opening_crawl}
            </div>
          </div>

          <div className='flex flex-col min-w-0'>
            <div className='mb-2 md:mb-[10px] flex-shrink-0'>
              <h2 className='text-[16px] font-bold text-black mb-[6px]'>
                Characters
              </h2>
              <div className='w-full h-px bg-divider'></div>
            </div>

            <div className='flex-1  md:max-h-[325px] text-[14px] leading-relaxed font-normal md:overflow-y-auto'>
              {renderCharactersSection()}
            </div>
          </div>
        </div>
      </div>

      <div className='mt-auto pt-2 md:pt-4 flex-shrink-0'>
        <button
          onClick={onBackToSearch}
          className='w-[187px] h-[34px] bg-button-green hover:bg-button-green-hover text-white text-[14px] font-bold border border-button-green hover:border-button-green-hover rounded-[17px] transition-colors duration-150 flex items-center justify-center'
        >
          BACK TO SEARCH
        </button>
      </div>
    </div>
  );
};

export default MovieDetails;
