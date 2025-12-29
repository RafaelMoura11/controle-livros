<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    public bool $showModal = false;

    public ?Contact $editing = null;

    public string $name = '';
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $notes = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->editing = null;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $c = Contact::findOrFail($id);

        $this->editing = $c;
        $this->name = $c->name;
        $this->phone = $c->phone;
        $this->email = $c->email;
        $this->notes = $c->notes;

        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($this->editing) {
            $this->editing->update($data);
        } else {
            Contact::create($data);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Contact::query()->whereKey($id)->delete();
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->phone = null;
        $this->email = null;
        $this->notes = null;
    }

    public function render()
    {
        $contacts = Contact::query()
            ->when($this->search, function ($q) {
                $s = "%{$this->search}%";
                $q->where('name', 'like', $s)
                  ->orWhere('phone', 'like', $s)
                  ->orWhere('email', 'like', $s);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.contacts.index', compact('contacts'))
            ->layout('components.layouts.app');
    }
}
