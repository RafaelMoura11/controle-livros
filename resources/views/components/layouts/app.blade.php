<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Controle de Livros</title>

    @fluxAppearance
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 border-r border-zinc-800 p-4">
        <flux:navlist class="w-full">
            <flux:navlist.item
                href="{{ route('books.index') }}"
                icon="book-open"
                :active="request()->routeIs('books.*')"
            >
                Livros
            </flux:navlist.item>

            <flux:navlist.item
                href="{{ route('loans.index') }}"
                icon="calendar-days"
                :active="request()->routeIs('loans.*')"
            >
                Empréstimos
            </flux:navlist.item>

            <flux:navlist.item
                href="{{ route('contacts.index') }}"
                icon="user-group"
                :active="request()->routeIs('contacts.*')"
            >
                Contatos
            </flux:navlist.item>
        </flux:navlist>
    </aside>

    {{-- Conteúdo --}}
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>

</div>

@fluxScripts
@livewireScripts
</body>
<script>
  localStorage.setItem('flux.appearance', 'dark');
  document.documentElement.classList.remove('light');
</script>
</html>
