import { type FC, Fragment } from 'react';
import { StarWarsPerson } from '../types';

interface PersonDetailsProps {
  person: StarWarsPerson;
  onBackToSearch: () => void;
  onMovieClick: (movieId: number) => void;
  isLoading?: boolean;
}

const PersonDetails: FC<PersonDetailsProps> = ({
  person,
  onBackToSearch,
  onMovieClick,
  isLoading = false,
}) => {
  if (isLoading) {
    return (
      <div className='w-[804px] h-[537px] bg-white border border-card-border rounded shadow-card flex items-center justify-center'>
        <p className='text-sm font-bold text-divider'>Loading...</p>
      </div>
    );
  }

  const formatMovies = (films: Array<{ id: number; title: string }>) => {
    return films.map((film, index) => {
      const isLast = index === films.length - 1;

      return (
        <Fragment key={film.id}>
          <span
            className='text-[14px] text-character-link hover:underline cursor-pointer font-normal'
            onClick={() => onMovieClick(film.id)}
          >
            {film.title}
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
    <div className='w-[804px] h-[537px] bg-white border border-card-border rounded shadow-card p-[30px] flex flex-col'>
      <h1 className='text-[18px] font-bold text-black mb-[32px]'>
        {person.name}
      </h1>

      <div className='grid grid-cols-2 gap-[48px] flex-1'>
        <div className='space-y-3'>
          <div>
            <h2 className='text-[16px] font-bold text-black mb-[10px]'>
              Details
            </h2>
            <div className='w-full h-px bg-divider'></div>
          </div>

          <div className='text-[14px] font-normal text-black'>
            <div>Birth Year: {person.birth_year}</div>
            <div>Gender: {person.gender}</div>
            <div>Eye Color: {person.eye_color}</div>
            <div>Hair Color: {person.hair_color}</div>
            <div>Height: {person.height}</div>
            <div>Mass: {person.mass}</div>
          </div>
        </div>

        <div className='space-y-3'>
          <div>
            <h2 className='text-[16px] font-bold text-black mb-[10px]'>
              Movies
            </h2>
            <div className='w-full h-px bg-divider'></div>
          </div>

          <div className='text-[14px] leading-relaxed max-h-60 overflow-y-auto'>
            {person.films && person.films.length > 0 ? (
              <div className='flex flex-wrap'>{formatMovies(person.films)}</div>
            ) : (
              <div className='text-[14px] font-normal'>No movies available</div>
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

export default PersonDetails;
