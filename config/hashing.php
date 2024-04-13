<?php declare(strict_types=1);

return [
    'driver' => 'argon2id',
    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
    ],
];
