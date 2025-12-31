<div class="page-container page-stack">
    <div class="flex items-center justify-between gap-3">
        <div class="flex-1">
            <flux:input
                wire:model.live="search"
                placeholder="Buscar por título, autor ou ISBN..."
            />
        </div>

        <flux:button href="{{ route('books.create') }}">
            Novo livro
        </flux:button>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Título</flux:table.column>
                <flux:table.column>Autor</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column class="text-right">
                    <div class="flex justify-end">Ações</div>
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($books as $book)
                    <flux:table.row>
                        <flux:table.cell>
                            <a class="underline" href="{{ route('books.show', $book) }}">
                                {{ $book->title }}
                            </a>
                        </flux:table.cell>

                        <flux:table.cell>{{ $book->author ?? '—' }}</flux:table.cell>

                        <flux:table.cell>
                            @if ($book->activeLoan)
                                <flux:badge color="red">Emprestado</flux:badge>
                            @else
                                <flux:badge color="green">Disponível</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="text-right">
                            <div class="flex gap-2">
                                <flux:button variant="ghost" href="{{ route('books.show', $book) }}">
                                    Ver
                                </flux:button>
                                <flux:button variant="ghost" href="{{ route('books.edit', $book) }}">
                                    Editar
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="p-4">
            {{ $books->links() }}
        </div>
    </flux:card>
</div>
