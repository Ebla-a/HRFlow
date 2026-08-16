<?php
namespace Modules\AI\Contracts;

interface AiToolInterface
{
    public function name(): string;

    public function description(): string;

    public function parameters(): array;

    public function execute(array $arguments, int $userId): array;
}
