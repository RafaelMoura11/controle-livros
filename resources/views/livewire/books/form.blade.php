<div class="max-w-3xl mx-auto p-6 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">
            {{ $book ? 'Editar livro' : 'Novo livro' }}
        </h1>

        <flux:button variant="ghost" href="{{ route('books.index') }}">
            Voltar
        </flux:button>
    </div>

    <flux:card>
        <div class="p-6 space-y-4">
            <flux:input label="Título" wire:model="title" />
            <flux:input label="Autor" wire:model="author" />
            <flux:input label="ISBN" wire:model="isbn" />
            <flux:textarea label="Notas" wire:model="notes" />

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" href="{{ route('books.index') }}">
                    Cancelar
                </flux:button>

                <flux:button wire:click="save" wire:loading.attr="disabled">
                    Salvar
                </flux:button>
            </div>
        </div>
    </flux:card>
</div>
