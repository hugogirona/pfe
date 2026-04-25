<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Accueil')] class extends Component
{

};
?>

<div>
    <x-parts.home.hero />
</div>
