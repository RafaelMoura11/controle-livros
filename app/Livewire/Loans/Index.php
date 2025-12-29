<?php

namespace App\Livewire\Loans;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $status = 'all'; // active|overdue|returned|all

    #[Url(history: true)]
    public string $search = '';

    public bool $showLoanModal = false;

    // Form empréstimo
    public ?int $book_id = null;
    public string $borrower_name = '';
    public ?string $borrower_contact = null;
    public ?string $loaned_at = null; // Y-m-d
    public ?string $due_at = null;    // Y-m-d
    public ?string $notes = null;

    public function mount(): void
    {
        $this->loaned_at = now()->toDateString();
        $this->due_at = now()->addDays(7)->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openLoanModal(): void
    {
        $this->resetLoanForm();
        $this->showLoanModal = true;
    }

    public function loan(): void
    {
        $data = $this->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'borrower_name' => ['required', 'string', 'max:255'],
            'borrower_contact' => ['nullable', 'string', 'max:255'],
            'loaned_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:loaned_at'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            // trava o livro pra evitar corrida
            $book = Book::query()->whereKey($data['book_id'])->lockForUpdate()->firstOrFail();

            $alreadyLoaned = Loan::query()
                ->where('book_id', $book->id)
                ->whereNull('returned_at')
                ->exists();

            if ($alreadyLoaned) {
                throw ValidationException::withMessages([
                    'book_id' => 'Este livro já está emprestado.',
                ]);
            }

            Loan::create([
                'book_id' => $book->id,
                'borrower_name' => $data['borrower_name'],
                'borrower_contact' => $data['borrower_contact'],
                'loaned_at' => $data['loaned_at'],
                'due_at' => $data['due_at'],
                'notes' => $data['notes'],
            ]);
        });

        $this->showLoanModal = false;
        $this->resetLoanForm();
        $this->resetPage();
    }

    public function markReturned(int $loanId): void
    {
        $loan = Loan::query()->with('book')->findOrFail($loanId);

        if ($loan->returned_at) {
            return;
        }

        $loan->update(['returned_at' => now()->toDateString()]);
        $this->resetPage();
    }

    private function resetLoanForm(): void
    {
        $this->book_id = null;
        $this->borrower_name = '';
        $this->borrower_contact = null;
        $this->notes = null;
        $this->loaned_at = now()->toDateString();
        $this->due_at = now()->addDays(7)->toDateString();
    }

    public function render()
    {
        $today = now()->toDateString();

        $loans = Loan::query()
            ->with(['book'])
            ->when($this->status === 'active', fn ($q) => $q->whereNull('returned_at'))
            ->when($this->status === 'returned', fn ($q) => $q->whereNotNull('returned_at'))
            ->when($this->status === 'overdue', fn ($q) => $q->whereNull('returned_at')->where('due_at', '<', $today))
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where(function ($q) use ($s) {
                    $q->where('borrower_name', 'like', $s)
                      ->orWhere('borrower_contact', 'like', $s)
                      ->orWhereHas('book', fn ($b) => $b->where('title', 'like', $s)->orWhere('author', 'like', $s)->orWhere('isbn', 'like', $s));
                });
            })
            ->orderByRaw("returned_at is null desc") // ativos primeiro
            ->orderBy('due_at')
            ->paginate(10);

        // Livros disponíveis para emprestar (sem active loan)
        $availableBooks = Book::query()
            ->whereDoesntHave('loans', fn ($q) => $q->whereNull('returned_at'))
            ->orderBy('title')
            ->get(['id', 'title', 'author']);

        $counts = [
            'active' => Loan::query()->whereNull('returned_at')->count(),
            'overdue' => Loan::query()->whereNull('returned_at')->where('due_at', '<', $today)->count(),
            'returned' => Loan::query()->whereNotNull('returned_at')->count(),
        ];

        return view('livewire.loans.index', compact('loans', 'availableBooks', 'counts'));
    }
}
