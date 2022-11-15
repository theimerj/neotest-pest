<?php

use TestProject\User;

uses()->group('file group');

$makeUser = fn () => new User(18, 'John');

beforeEach(fn () => $this->sut = $makeUser());

test('class constructor')
    ->expect($makeUser)
    ->name->toBe('John')
    ->age->toBe(18)
    ->favorite_movies->toBeEmpty();

test('tellName', function () {
    expect($this->sut)
        ->tellName()->toBeString()->toContain('John');
})
    ->group('special tests');

it('can tellAge')
    ->expect($makeUser)
    ->tellAge()->toBeString()->toContain('18');

test('addFavoriteMovie')
    ->expect($makeUser)
    ->addFavoriteMovie('Avengers')
    ->toBeTrue()
    ->favorite_movies->toContain('Avengers')->toHaveLength(1);

test('removeFavoriteMovie')
    ->expect($makeUser)
    ->addFavoriteMovie('Avengers')->toBeTrue()
    ->addFavoriteMovie('Justice League')->toBeTrue()
    ->removeFavoriteMovie('Avengers')->toBeTrue()
    ->favorite_movies->not->toContain('Avengers')->toHaveLength(1);
