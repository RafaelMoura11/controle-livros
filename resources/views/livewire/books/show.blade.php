<div class="page-container page-stack">
    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">
                {{ $book->title }}
            </h1>

            <p class="text-sm text-gray-500">
                {{ $book->author ?? 'Autor não informado' }}
            </p>
        </div>

        <flux:button variant="ghost" href="{{ route('books.index') }}">
            Voltar
        </flux:button>
    </div>

    {{-- Status --}}
    <div>
        @if ($book->activeLoan)
            <flux:badge color="red">
                Emprestado
            </flux:badge>
        @else
            <flux:badge color="green">
                Disponível
            </flux:badge>
        @endif
    </div>

    {{-- Informações do livro --}}
    <flux:card>
        <div class="p-6 space-y-2">
            <div>
                <strong>ISBN:</strong>
                {{ $book->isbn ?? '—' }}
            </div>

            <div>
                <strong>Editora:</strong>
                {{ $book->publisher ?? '—' }}
            </div>

            @if ($book->notes)
                <div>
                    <strong>Notas:</strong>
                    <p class="text-sm text-gray-600">
                        {{ $book->notes }}
                    </p>
                </div>
            @endif
        </div>
    </flux:card>

    {{-- Empréstimo ativo --}}
    @if ($book->activeLoan)
        <flux:card>
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-semibold">
                    Empréstimo ativo
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>Quem pegou:</strong><br>
                        {{ $book->activeLoan->borrower_name }}
                    </div>

                    <div>
                        <strong>Contato:</strong><br>
                        {{ $book->activeLoan->borrower_contact ?? '—' }}
                    </div>

                    <div>
                        <strong>Emprestado em:</strong><br>
                        {{ $book->activeLoan->loaned_at->format('d/m/Y') }}
                    </div>

                    <div>
                        <strong>Devolver até:</strong><br>
                        {{ $book->activeLoan->due_at->format('d/m/Y') }}
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:button
                        color="red"
                        wire:click="returnLoan"
                        wire:loading.attr="disabled"
                    >
                        Marcar como devolvido
                    </flux:button>
                </div>
            </div>
        </flux:card>
    @else
        {{-- Ação emprestar --}}
        <div class="flex justify-end">
            <flux:button wire:click="openLoanModal">
                Emprestar livro
            </flux:button>
        </div>
    @endif

    {{-- Histórico de empréstimos --}}
    @if ($book->loans->count() > 0)
        <flux:card>
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-semibold">
                    Histórico de empréstimos
                </h2>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Quem</flux:table.column>
                        <flux:table.column>Emprestado</flux:table.column>
                        <flux:table.column>Devolução</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($book->loans as $loan)
                            <flux:table.row>
                                <flux:table.cell>
                                    {{ $loan->borrower_name }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $loan->loaned_at->format('d/m/Y') }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $loan->returned_at
                                        ? $loan->returned_at->format('d/m/Y')
                                        : '—'
                                    }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    @if ($loan->isActive())
                                        <flux:badge color="red">Ativo</flux:badge>
                                    @else
                                        <flux:badge color="gray">Finalizado</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>
    @endif

    {{-- Modal de empréstimo --}}
    <flux:modal wire:model="showLoanModal">
        <flux:card>
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-semibold">
                    Emprestar livro
                </h2>

                <flux:input
                    label="Nome de quem está pegando"
                    wire:model="borrower_name"
                />

                <flux:input
                    label="Contato (opcional)"
                    wire:model="borrower_contact"
                />

                <flux:input
                    type="date"
                    label="Data do empréstimo"
                    wire:model="loaned_at"
                />

                <flux:input
                    type="date"
                    label="Data de devolução"
                    wire:model="due_at"
                />

                <flux:textarea
                    label="Observações"
                    wire:model="loan_notes"
                />

                <div class="flex justify-end gap-2">
                    <flux:button
                        variant="ghost"
                        wire:click="$set('showLoanModal', false)"
                    >
                        Cancelar
                    </flux:button>

                    <flux:button
                        wire:click="loan"
                        wire:loading.attr="disabled"
                    >
                        Confirmar empréstimo
                    </flux:button>
                </div>
            </div>
        </flux:card>
    </flux:modal>

</div>
