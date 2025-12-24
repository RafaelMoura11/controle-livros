<?php

use App\Livewire\Books\Index as BooksIndex;
use App\Livewire\Books\Form as BooksForm;
use App\Livewire\Books\Show as BooksShow;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/books');

Route::get('/books', BooksIndex::class)->name('books.index');
Route::get('/books/create', BooksForm::class)->name('books.create');
Route::get('/books/{book}/edit', BooksForm::class)->name('books.edit');
Route::get('/books/{book}', BooksShow::class)->name('books.show');

Route::get('/', function () {
    return view('welcome');
});
