<?php

use App\Livewire\Books\Index as BooksIndex;
use App\Livewire\Books\Form as BooksForm;
use App\Livewire\Books\Show as BooksShow;
use App\Livewire\Loans\Index as LoansIndex;
use App\Livewire\Contacts\Index as ContactsIndex;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/books');

Route::get('/books', BooksIndex::class)->name('books.index');
Route::get('/books/create', BooksForm::class)->name('books.create');
Route::get('/books/{book}/edit', BooksForm::class)->name('books.edit');
Route::get('/books/{book}', BooksShow::class)->name('books.show');

Route::get('/loans', LoansIndex::class)->name('loans.index');
Route::get('/contacts', ContactsIndex::class)->name('contacts.index');

Route::get('/', function () {
    return view('welcome');
});
