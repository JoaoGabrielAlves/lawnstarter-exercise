import { type FC, Fragment } from 'react';
import { StarWarsFilm } from '../types';

interface MovieDetailsProps {
  film: StarWarsFilm;
  onBackToSearch: () => void;
  onCharacterClick?: (characterId: number) => void;
  isLoading?: boolean;
}

const MovieDetails: FC<MovieDetailsProps> = ({
  film,
  onBackToSearch,
  onCharacterClick,
  isLoading = false,
}) => {
  if (isLoading) {
    return (
      <div className='w-[804px] h-[537px] bg-white border border-card-border rounded shadow-card flex items-center justify-center'>
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

  return (
    <div className='w-[804px] h-[537px] bg-white border border-card-border rounded shadow-[0_1px_2px_0_rgba(132,132,132,0.75)] p-[30px] flex flex-col'>
      <h1 className='text-[18px] font-bold text-black mb-[32px]'>
        {film.title}
      </h1>

      <div className='grid grid-cols-2 gap-[48px] flex-1'>
        <div className='flex flex-col'>
          <div className='mb-[10px]'>
            <h2 className='text-[16px] font-bold text-black mb-[6px]'>
              Opening Crawl
            </h2>
            <div className='w-full h-px bg-divider'></div>
          </div>

          <div className='h-[325px] text-[14px] text-black leading-relaxed whitespace-pre-line font-normal overflow-y-auto'>
            {film.opening_crawl}
          </div>
        </div>

        <div className='flex flex-col'>
          <div className='mb-[10px]'>
            <h2 className='text-[16px] font-bold text-black mb-[6px]'>
              Characters
            </h2>
            <div className='w-full h-px bg-divider'></div>
          </div>

          <div className='w-[322px] h-[124px] text-[14px] leading-relaxed font-normal'>
            {film.characters && film.characters.length > 0 ? (
              <div className='flex flex-wrap'>
                {formatCharacters(film.characters)}
              </div>
            ) : (
              <p className='text-[14px] text-divider font-normal'>
                No characters available
              </p>
            )}
          </div>
        </div>
      </div>

      <div className='mt-auto'>
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
