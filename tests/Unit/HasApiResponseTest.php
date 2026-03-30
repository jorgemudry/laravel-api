<?php

declare(strict_types=1);

use App\Traits\HasApiResponse;

it('transforms meta key to pagination', function (): void {
    $object = new class()
    {
        use HasApiResponse;

        /**
         * @param array<string, mixed> $content
         * @return array<string, mixed>
         */
        public function testParsePagination(array $content): array
        {
            return $this->parsePagination($content);
        }
    };

    $content = [
        'data' => [],
        'meta' => [
            'current_page' => 1,
            'total' => 10,
            'links' => ['first' => '/page/1'],
        ],
    ];

    $result = $object->testParsePagination($content);

    expect($result)->toHaveKey('pagination');
    expect($result)->not->toHaveKey('meta');
    expect($result['pagination'])->not->toHaveKey('links');
    expect($result['pagination'])->toHaveKey('current_page', 1);
    expect($result['pagination'])->toHaveKey('total', 10);
});

it('returns content unchanged when no meta key', function (): void {
    $object = new class()
    {
        use HasApiResponse;

        /**
         * @param array<string, mixed> $content
         * @return array<string, mixed>
         */
        public function testParsePagination(array $content): array
        {
            return $this->parsePagination($content);
        }
    };

    $content = ['data' => ['item1', 'item2']];
    $result = $object->testParsePagination($content);

    expect($result)->toBe($content);
});
