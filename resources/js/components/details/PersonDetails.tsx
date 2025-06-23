import { type FC, Fragment } from 'react';
import { StarWarsPerson } from '../../types';

interface PersonDetailsProps {
  person: StarWarsPerson;
  films?: Array<{ id: number; title: string }>; // Films loaded separately
  onBackToSearch: () => void;
  onMovieClick: (movieId: number) => void;
  isLoading?: boolean;
  isFilmsLoading?: boolean; // Separate loading state for films
}

const PersonDetails: FC<PersonDetailsProps> = ({
  person,
  films = [],
  onBackToSearch,
  onMovieClick,
  isLoading = false,
  isFilmsLoading = false,
}) => {
  if (isLoading) {
    return (
      <div className='w-full max-w-[804px] h-[calc(100vh-5rem)] md:h-[537px] bg-white border border-card-border rounded shadow-card flex items-center justify-center mx-auto overflow-hidden'>
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

  const renderMoviesSection = () => {
    if (isFilmsLoading) {
      return (
        <div className='text-[14px] font-normal text-divider'>
          Loading movies...
        </div>
      );
    }

    if (films && films.length > 0) {
      return <div className='flex flex-wrap'>{formatMovies(films)}</div>;
    }

    return <div className='text-[14px] font-normal'>No movies available</div>;
  };

  return (
    <div className='w-full max-w-[804px] h-[537px] bg-white border border-card-border rounded shadow-card p-4 md:p-[30px] flex flex-col mx-auto overflow-hidden'>
      <h1 className='text-[18px] font-bold text-black mb-4 md:mb-[32px] flex-shrink-0'>
        {person.name}
      </h1>

      <div className='flex-1 overflow-y-auto md:overflow-visible'>
        <div className='grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-[48px] h-full'>
          <div className='space-y-2 md:space-y-3 min-w-0'>
            <div className='flex-shrink-0'>
              <h2 className='text-[16px] font-bold text-black mb-[10px]'>
                Details
              </h2>
              <div className='w-full h-px bg-divider'></div>
            </div>

            <div className='text-[14px] font-normal text-black md:max-h-60 md:overflow-y-auto'>
              <div>Birth Year: {person.birth_year}</div>
              <div>Gender: {person.gender}</div>
              <div>Eye Color: {person.eye_color}</div>
              <div>Hair Color: {person.hair_color}</div>
              <div>Height: {person.height}</div>
              <div>Mass: {person.mass}</div>
            </div>
          </div>

          <div className='space-y-2 md:space-y-3 min-w-0'>
            <div className='flex-shrink-0'>
              <h2 className='text-[16px] font-bold text-black mb-[10px]'>
                Movies
              </h2>
              <div className='w-full h-px bg-divider'></div>
            </div>

            <div className='text-[14px] leading-relaxed md:max-h-60 md:overflow-y-auto'>
              {renderMoviesSection()}
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

export default PersonDetails;
