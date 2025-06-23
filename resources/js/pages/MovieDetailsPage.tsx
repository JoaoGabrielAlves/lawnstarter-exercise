import React from 'react';
import { useNavigate, useParams } from 'react-router-dom';

import { MovieDetails } from '@/components/details';

import {
  useStarWarsFilmBasic,
  useStarWarsFilmCharacters,
} from '../hooks/useStarWars';
import { StarWarsFilm } from '../types';

const MovieDetailsPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const movieId = parseInt(id || '0', 10);

  const {
    data: movieData,
    isLoading: isMovieLoading,
    error: movieError,
  } = useStarWarsFilmBasic(movieId);

  const {
    data: charactersData,
    isLoading: isCharactersLoading,
    error: charactersError,
  } = useStarWarsFilmCharacters(movieId, !!movieData);

  const handleBackToSearch = () => {
    navigate('/');
  };

  const handleCharacterClick = (characterId: number) => {
    navigate(`/person/${characterId}`);
  };

  if (movieError || charactersError) {
    const errorMessage =
      movieError?.message || charactersError?.message || 'Unknown error';
    return (
      <div className='min-h-screen bg-gray-50 pt-20 flex justify-center px-4'>
        <div className='w-full max-w-[804px] bg-white border border-card-border shadow-card border-radius-4 rounded p-8'>
          <p className='text-red-500 text-center mb-6'>
            Error loading movie details: {errorMessage}
          </p>
          <div className='text-center'>
            <button
              onClick={handleBackToSearch}
              className='w-[187px] h-[34px] bg-button-green hover:bg-button-green-hover text-white text-[14px] font-bold border border-button-green hover:border-button-green-hover rounded-[17px] transition-colors duration-150 flex items-center justify-center'
            >
              BACK TO SEARCH
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className='min-h-screen bg-gray-50 pt-20 flex justify-center px-4'>
      {isMovieLoading || !movieData ? (
        <MovieDetails
          film={{} as StarWarsFilm}
          onBackToSearch={handleBackToSearch}
          onCharacterClick={handleCharacterClick}
          isLoading={true}
        />
      ) : (
        <MovieDetails
          film={movieData.data}
          characters={charactersData?.data || []}
          onBackToSearch={handleBackToSearch}
          onCharacterClick={handleCharacterClick}
          isCharactersLoading={isCharactersLoading}
        />
      )}
    </div>
  );
};

export default MovieDetailsPage;
