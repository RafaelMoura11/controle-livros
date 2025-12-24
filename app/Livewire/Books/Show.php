<?php

namespace App\Livewire\Books;

use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Show extends Component
{
    public Book $book;

    // Modal
    public bool $showLoanModal = false;

    // Form empréstimo
    public string $borrower_name = '';
    public ?string $borrower_contact = null;
    public ?string $loaned_at = null; // date (Y-m-d)
    public ?string $due_at = null;    // date (Y-m-d)
    public ?string $loan_notes = null;

    public function mount(Book $book): void
    {
        $this->book = $book->load([
            'activeLoan',
            'loans' => fn ($q) => $q->latest('loaned_at'),
        ]);

        // Defaults do form
        $today = now()->toDateString();
        $this->loaned_at = $today;
        $this->due_at = now()->addDays(7)->toDateString();
    }

    public function openLoanModal(): void
    {
        if ($this->book->activeLoan) {
            throw ValidationException::withMessages([
                'borrower_name' => 'Este livro já está emprestado.',
            ]);
        }

        $this->resetLoanForm();
        $this->showLoanModal = true;
    }

    public function loan(): void
    {
        // Recarrega para evitar corrida (duplo clique / 2 abas)
        $this->book->load('activeLoan');

        if ($this->book->activeLoan) {
            throw ValidationException::withMessages([
                'borrower_name' => 'Este livro já está emprestado.',
            ]);
        }

        $data = $this->validate([
            'borrower_name' => ['required', 'string', 'max:255'],
            'borrower_contact' => ['nullable', 'string', 'max:255'],
            'loaned_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:loaned_at'],
            'loan_notes' => ['nullable', 'string'],
        ]);

        Loan::create([
            'book_id' => $this->book->id,
            'borrower_name' => $data['borrower_name'],
            'borrower_contact' => $data['borrower_contact'],
            'loaned_at' => Carbon::parse($data['loaned_at'])->toDateString(),
            'due_at' => Carbon::parse($data['due_at'])->toDateString(),
            'notes' => $data['loan_notes'],
        ]);

        $this->showLoanModal = false;
        $this->resetLoanForm();
        $this->refreshBook();
    }

    public function returnLoan(): void
    {
        $this->book->load('activeLoan');

        if (! $this->book->activeLoan) {
            return; // nada a fazer
        }

        $this->book->activeLoan->update([
            'returned_at' => now()->toDateString(),
        ]);

        $this->refreshBook();
    }

    private function refreshBook(): void
    {
        $this->book = Book::query()
            ->whereKey($this->book->id)
            ->with([
                'activeLoan',
                'loans' => fn ($q) => $q->latest('loaned_at'),
            ])
            ->firstOrFail();
    }

    private function resetLoanForm(): void
    {
        $this->borrower_name = '';
        $this->borrower_contact = null;
        $this->loan_notes = null;

        $this->loaned_at = now()->toDateString();
        $this->due_at = now()->addDays(7)->toDateString();
    }

    public function render()
    {
        return view('livewire.books.show');
    }
}
