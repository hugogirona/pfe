<?php

namespace App\Support;

use Illuminate\Support\Collection;

class JuryRoster
{
    public const int COUNT = 15;

    /**
     * @return Collection<int, array{number: int, label: string, first_name: string, last_name: string, email: string, password: string}>
     */
    public static function members(): Collection
    {
        return collect(range(1, self::COUNT))->map(fn (int $number) => self::member($number));
    }

    /**
     * @return array{number: int, label: string, first_name: string, last_name: string, email: string, password: string}
     */
    public static function member(int $number): array
    {
        $padded = str_pad((string) $number, 2, '0', STR_PAD_LEFT);

        return [
            'number' => $number,
            'label' => "Membre du jury {$padded}",
            'first_name' => 'Jury',
            'last_name' => $padded,
            'email' => "jury{$padded}@giggr.be",
            'password' => "jury{$padded}giggr",
        ];
    }
}
