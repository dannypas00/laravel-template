<?php

declare(strict_types=1);

use App\Traits\PadsArrayWithNull;

use function PHPUnit\Framework\assertEquals;

uses(PadsArrayWithNull::class);

test('that it pads array', function (): void {
    $keys = ['a', 'b', 'c'];
    $values = [
        'a' => 'testa',
        'c' => 'testc',
    ];
    $padded = $this->padArrayWithNull($keys, $values);
    assertEquals(['a' => 'testa', 'b' => null, 'c' => 'testc'], $padded);
});
