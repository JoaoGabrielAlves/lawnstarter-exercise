import React from 'react';
import { useNavigate } from 'react-router-dom';

const Header: React.FC = () => {
  const navigate = useNavigate();
  return (
    <header className='fixed top-0 left-0 w-full h-12 bg-white flex items-center justify-center z-50 shadow-card'>
      <h1
        className='text-lg font-bold text-button-green cursor-pointer'
        onClick={() => navigate('/')}
      >
        SWStarter
      </h1>
    </header>
  );
};

export default Header;
