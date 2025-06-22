import React from 'react';
import { Routes, Route } from 'react-router-dom';

import SearchPage from './pages/SearchPage';
import PersonDetailsPage from './pages/PersonDetailsPage';
import MovieDetailsPage from './pages/MovieDetailsPage';

const AppRoutes: React.FC = () => {
  return (
    <Routes>
      <Route path='/' element={<SearchPage />} />
      <Route path='/person/:id' element={<PersonDetailsPage />} />
      <Route path='/movie/:id' element={<MovieDetailsPage />} />
    </Routes>
  );
};

export default AppRoutes;
