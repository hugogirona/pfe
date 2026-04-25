@props(['name'])

<span {{ $attributes->class(['sprite-' . $name . '-mask inline-block bg-current']) }}
      aria-hidden="true"></span>
