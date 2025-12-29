<div class="max-w-5xl mx-auto space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div class="flex-1">
            <flux:input wire:model.live="search" placeholder="Buscar contato..." />
        </div>

        <flux:button wire:click="create">Novo contato</flux:button>
    </div>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nome</flux:table.column>
                <flux:table.column>Telefone</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column class="text-right">Ações</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($contacts as $c)
                    <flux:table.row :key="$c->id">
                        <flux:table.cell>{{ $c->name }}</flux:table.cell>
                        <flux:table.cell>{{ $c->phone ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $c->email ?? '—' }}</flux:table.cell>

                        <flux:table.cell class="text-right">
                            <div class="inline-flex gap-2 justify-end">
                                <flux:button variant="ghost" wire:click="edit({{ $c->id }})">Editar</flux:button>
                                <flux:button variant="ghost" color="red" wire:click="delete({{ $c->id }})">Excluir</flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="p-4">{{ $contacts->links() }}</div>
    </flux:card>

    <flux:modal wire:model="showModal">
        <flux:card>
            <div class="p-6 space-y-4">
                <h2 class="text-lg font-semibold">
                    {{ $editing ? 'Editar contato' : 'Novo contato' }}
                </h2>

                <flux:input label="Nome" wire:model="name" />
                <flux:input label="Telefone" wire:model="phone" />
                <flux:input label="Email" wire:model="email" />
                <flux:textarea label="Notas" wire:model="notes" />

                <div class="flex justify-end gap-2">
                    <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancelar</flux:button>
                    <flux:button wire:click="save">Salvar</flux:button>
                </div>
            </div>
        </flux:card>
    </flux:modal>
</div>
