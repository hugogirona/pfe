<footer class="bg-dark text-bg">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex flex-col md:flex-row justify-between items-start gap-8">

            <div>
                <x-logo class="text-bg" />
                <p class="mt-3 text-sm text-bg/60 max-w-xs">
                    La plateforme qui connecte les musiciens amateurs.
                </p>
            </div>

            <nav class="flex flex-col gap-3">
                <h2 class="sr-only">Navigation de pied de page</h2>
                <a href="{{ route('home') }}" class="text-sm text-bg/70 hover:text-bg transition-colors duration-150">Accueil</a>
                <a href="#" class="text-sm text-bg/70 hover:text-bg transition-colors duration-150">Explorer</a>
                <a href="#" class="text-sm text-bg/70 hover:text-bg transition-colors duration-150">Contact</a>
            </nav>

        </div>
        <div class="mt-12 pt-6 border-t border-bg/10 flex items-center justify-between">
            <span class="text-xs text-bg/40">© {{ date('Y') }} Giggr. Tous droits réservés.</span>
            <x-lang-switcher />
        </div>
    </div>
</footer>
