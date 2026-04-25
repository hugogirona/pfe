@php
$profiles = [
    [
        'name'  => 'Valentine',
        'age'   => 26,
        'city'  => 'Liège, Belgique',
        'bio'   => 'Cherche groupe de rock pour projet sérieux. 8 ans d\'expérience, disponible les weekends.',
        'image' => 'valentine.webp',
        'url'   => '#',
    ],
    [
        'name'  => 'Thomas',
        'age'   => 30,
        'city'  => 'Namur, Belgique',
        'bio'   => 'Batteur jazz & funk depuis 12 ans. Disponible en soirée et les weekends pour projets studio ou scène.',
        'image' => 'thomas.webp',
        'url'   => '#',
    ],
    [
        'name'  => 'Sarah',
        'age'   => 22,
        'city'  => 'Liège, Belgique',
        'bio'   => 'Pianiste classique reconvertie jazz. Je cherche des musiciens motivés pour des jams régulières.',
        'image' => 'sarah.webp',
        'url'   => '#',
    ],
];
@endphp

<section class="py-16 md:py-20 bg-pastel-taupe">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-12">
            <h2 class="font-heading text-4xl md:text-5xl text-dark">Ils sont déjà sur Giggr.</h2>
            <p class="mt-4 text-base md:text-lg text-dark/55">Découvre des musiciens près de chez toi.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($profiles as $profile)
                <x-parts.home.profile-card
                    :name="$profile['name']"
                    :age="$profile['age']"
                    :city="$profile['city']"
                    :bio="$profile['bio']"
                    :image="$profile['image']"
                    :url="$profile['url']"
                />
            @endforeach
        </div>

    </div>
</section>
