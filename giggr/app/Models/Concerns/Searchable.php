<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Searchable fields. A string value is a column on the model; a
     * "relation => [columns]" pair matches the related model's columns.
     *
     * @return array<int|string, string|list<string>>
     */
    abstract protected function searchable(): array;

    #[Scope]
    protected function search(Builder $query, string $terms): void
    {
        $words = $terms
                |> trim(...)
                |> (fn ($x) => preg_split('/\s+/', $x))
                |> array_filter(...)
                |> array_values(...);

        foreach ($words as $word) {
            $like = '%'.$word.'%';

            $query->where(function (Builder $q) use ($like) {
                foreach ($this->searchable() as $relation => $fields) {
                    if (is_string($fields)) {
                        $q->orWhere($fields, 'like', $like);

                        continue;
                    }

                    $q->orWhereHas($relation, fn (Builder $related) => $related->where(
                        fn (Builder $r) => collect($fields)->each(fn (string $col) => $r->orWhere($col, 'like', $like))
                    ));
                }
            });
        }
    }
}
