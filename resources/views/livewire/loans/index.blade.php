<div class="max-w-6xl mx-auto space-y-4">

    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Empréstimos</h1>
            <p class="text-sm text-zinc-400">Gerencie empréstimos, atrasos e devoluções.</p>
        </div>

        <flux:button wire:click="openLoanModal">
            Novo empréstimo
        </flux:button>
    </div>

    <div class="flex items-center gap-3">
        <div class="flex-1">
            <flux:input
                wire:model.live="search"
                placeholder="Buscar por livro, pessoa ou contato..."
            />
        </div>

        <select
            wire:model.live="status"
            class="h-10 rounded-lg bg-zinc-900 border border-zinc-800 px-3 text-sm"
        >
            <option value="active">Ativos ({{ $counts['active'] }})</option>
            <option value="overdue">Atrasados ({{ $counts['overdue'] }})</option>
            <option value="returned">Finalizados ({{ $counts['returned'] }})</option>
            <option value="all">Todos</option>
        </select>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Livro</flux:table.column>
                <flux:table.column>Quem pegou</flux:table.column>
                <flux:table.column>Emprestado</flux:table.column>
                <flux:table.column>Devolver até</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column class="text-right">Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($loans as $loan)
                    <flux:table.row :key="$loan->id">
                        <flux:table.cell>
                            <a class="underline" href="{{ route('books.show', $loan->book) }}">
                                {{ $loan->book->title }}
                            </a>
                            <div class="text-xs text-zinc-400">
                                {{ $loan->book->author ?? '—' }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div>{{ $loan->borrower_name }}</div>
                            <div class="text-xs text-zinc-400">{{ $loan->borrower_contact ?? '—' }}</div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $loan->loaned_at?->format('d/m/Y') ?? '—' }}</flux:table.cell>

                        <flux:table.cell>{{ $loan->due_at?->format('d/m/Y') ?? '—' }}</flux:table.cell>

                        <flux:table.cell>
                            @if ($loan->returned_at)
                                <flux:badge color="gray">Finalizado</flux:badge>
                            @else
                                @if ($loan->due_at && $loan->due_at->isPast())
                                    <flux:badge color="red">Atrasado</flux:badge>
                                @else
                                    <flux:badge color="green">Ativo</flux:badge>
                                @endif
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="text-right">
                            <div class="inline-flex gap-2 justify-end">
                                @if (! $loan->returned_at)
                                    <flux:button
                                        variant="ghost"
                                        color="green"
                                        wire:click="markReturned({{ $loan->id }})"
                                        wire:loading.attr="disabled"
                                    >
                                        Marcar devolvido
                                    </flux:button>
                                @else
                                    <flux:button variant="ghost" href="{{ route('books.show', $loan->book) }}">
                                        Ver livro
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <div class="p-4 text-sm text-zinc-400">
                                Nenhum empréstimo encontrado.
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="p-4">
            {{ $loans->links() }}
        </div>
    </flux:card>

    {{-- Modal: Novo empréstimo --}}
    <flux:modal wire:model="showLoanModal">
        <flux:card>
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-semibold">Novo empréstimo</h2>

                <div class="space-y-1">
                    <label class="text-sm text-zinc-300">Livro</label>
                    <select
                        wire:model="book_id"
                        class="w-full h-10 rounded-lg bg-zinc-900 border border-zinc-800 px-3 text-sm"
                    >
                        <option value="">Selecione um livro disponível</option>
                        @foreach ($availableBooks as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->title }}{{ $b->author ? " — {$b->author}" : '' }}
                            </option>
                        @endforeach
                    </select>

                    @error('book_id')
                        <div class="text-xs text-red-400">{{ $message }}</div>
                    @enderror
                </div>

                <flux:input
                    label="Nome de quem está pegando"
                    wire:model="borrower_name"
                />

                <flux:input
                    label="Contato (opcional)"
                    wire:model="borrower_contact"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <flux:input type="date" label="Data do empréstimo" wire:model="loaned_at" />
                    <flux:input type="date" label="Data de devolução" wire:model="due_at" />
                </div>

                <flux:textarea
                    label="Observações"
                    wire:model="notes"
                />

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" wire:click="$set('showLoanModal', false)">
                        Cancelar
                    </flux:button>

                    <flux:button wire:click="loan" wire:loading.attr="disabled">
                        Confirmar
                    </flux:button>
                </div>
            </div>
        </flux:card>
    </flux:modal>

</div>
