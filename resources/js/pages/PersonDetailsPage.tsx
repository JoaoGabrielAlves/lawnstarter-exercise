import React from 'react';
import { useNavigate, useParams } from 'react-router-dom';

import PersonDetails from '@/components/PersonDetails';

import { useStarWarsPerson } from '../hooks/useStarWars';
import { StarWarsPerson } from '../types';

const PersonDetailsPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const personId = parseInt(id || '0', 10);

  const { data: personData, isLoading, error } = useStarWarsPerson(personId);

  const handleBackToSearch = () => {
    navigate('/');
  };

  const handleMovieClick = (movieId: number) => {
    navigate(`/movie/${movieId}`);
  };

  if (error) {
    return (
      <div className='min-h-screen bg-gray-50 pt-20 flex justify-center'>
        <div className='w-[804px] bg-white border border-card-border shadow-card border-radius-4 rounded p-8'>
          <p className='text-red-500 text-center mb-6'>
            Error loading person details: {error.message}
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
    <div className='min-h-screen bg-gray-50 pt-20 flex justify-center'>
      {isLoading || !personData ? (
        <PersonDetails
          person={{} as StarWarsPerson}
          onBackToSearch={handleBackToSearch}
          onMovieClick={handleMovieClick}
          isLoading={true}
        />
      ) : (
        <PersonDetails
          person={personData.data}
          onBackToSearch={handleBackToSearch}
          onMovieClick={handleMovieClick}
        />
      )}
    </div>
  );
};

export default PersonDetailsPage;
