<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Livewire\Component;

class Form extends Component
{
    public ?Book $book = null;

    public string $title = '';
    public ?string $author = null;
    public ?string $isbn = null;
    public ?string $publisher = null;
    public ?string $notes = null;

    public function mount(?Book $book = null): void
    {
        $this->book = $book;

        if ($book) {
            $this->title = $book->title;
            $this->author = $book->author;
            $this->isbn = $book->isbn;
            $this->publisher = $book->publisher;
            $this->notes = $book->notes;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $book = $this->book
            ? tap($this->book)->update($data)
            : Book::create($data);

        $this->redirect(route('books.show', $book), navigate: true);
    }

    public function render()
    {
        return view('livewire.books.form');
    }
}
