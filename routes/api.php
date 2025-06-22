<?php

use App\Http\Controllers\Api\StarWarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/starwars')->group(function () {
    Route::get('/', [StarWarsController::class, 'index']);

    // Resource endpoints
    Route::prefix('people')->group(function () {
        Route::get('/', [StarWarsController::class, 'getPeople']);
        Route::get('/{id}', [StarWarsController::class, 'getPerson'])->whereNumber('id');
    });

    Route::prefix('films')->group(function () {
        Route::get('/', [StarWarsController::class, 'getFilms']);
        Route::get('/{id}', [StarWarsController::class, 'getFilm'])->whereNumber('id');
    });

    Route::prefix('starships')->group(function () {
        Route::get('/', [StarWarsController::class, 'getStarships']);
        Route::get('/{id}', [StarWarsController::class, 'getStarship'])->whereNumber('id');
    });

    Route::prefix('vehicles')->group(function () {
        Route::get('/', [StarWarsController::class, 'getVehicles']);
        Route::get('/{id}', [StarWarsController::class, 'getVehicle'])->whereNumber('id');
    });

    Route::prefix('species')->group(function () {
        Route::get('/', [StarWarsController::class, 'getSpecies']);
        Route::get('/{id}', [StarWarsController::class, 'getSpeciesById'])->whereNumber('id');
    });

    Route::prefix('planets')->group(function () {
        Route::get('/', [StarWarsController::class, 'getPlanets']);
        Route::get('/{id}', [StarWarsController::class, 'getPlanet'])->whereNumber('id');
    });

    // Search endpoint
    Route::get('search/{resource}', [StarWarsController::class, 'search'])
        ->whereIn('resource', ['people', 'films', 'starships', 'vehicles', 'species', 'planets']);
});
