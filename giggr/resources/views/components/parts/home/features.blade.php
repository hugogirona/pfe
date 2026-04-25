<section class="relative bg-dark py-20 overflow-hidden">

    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(ellipse_80%_50%_at_50%_0%,rgba(246,118,73,0.07),transparent)]"></div>

    <div class="relative max-w-6xl mx-auto px-6">

        <div class="text-center mb-12">
            <p class="inline-flex items-center gap-3 text-accent text-xs font-medium tracking-[0.3em] uppercase mb-5">
                <span class="w-8 h-px bg-accent"></span>
                Rejoins les
                <span class="w-8 h-px bg-accent"></span>
            </p>
            <h2 class="font-heading text-3xl md:text-4xl text-white leading-tight">
                Tout ce qu'il te faut,<br>au même endroit
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <x-parts.home.feature-card icon="search" title="Trouve ton partenaire">
                Filtre par instrument, style musical et ville. Trouve exactement le profil qu'il te faut en quelques secondes.
            </x-parts.home.feature-card>

            <x-parts.home.feature-card icon="plus-circle" title="Publie une annonce">
                En quelques minutes, atteins des centaines de musiciens de ta région. Gratuit et sans prise de tête.
            </x-parts.home.feature-card>

            <x-parts.home.feature-card icon="users" title="Rejoins la communauté">
                Intègre des groupes, participe à des jam sessions et échange avec des passionnés comme toi.
            </x-parts.home.feature-card>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-parts.home.feature-kpi value="12" label="musiciens inscrits" />
            <x-parts.home.feature-kpi value="3" label="annonces actives" />
            <x-parts.home.feature-kpi value="5" label="villes en Belgique" />

            <div class="col-span-2 md:col-span-1 bg-accent/10 border border-accent/20 rounded-2xl py-7 px-6 flex flex-col items-center justify-center text-center gap-4">
                <p class="text-sm text-white/65 leading-snug">Prêt à rejoindre<br>l'aventure ? viens stp on est pas bcp</p>
                <x-cta variant="accent" href="#">S'inscrire</x-cta>
            </div>
        </div>

    </div>

    <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

</section>
