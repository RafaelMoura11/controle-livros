<?php

namespace App\Livewire\Books;

use App\Models\Book;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $books = Book::query()
            ->with('activeLoan')
            ->when($this->search, fn ($q) =>
                $q->where('title', 'like', "%{$this->search}%")
                ->orWhere('author', 'like', "%{$this->search}%")
                ->orWhere('isbn', 'like', "%{$this->search}%")
                ->orWhere('publisher', 'like', "%{$this->search}%")
            )
            ->orderBy('title')
            ->paginate(10);

        return view('livewire.books.index', compact('books'));
    }
}
