<?php

namespace Modules\Performance\ToolAi;

use Modules\AI\Contracts\AiToolInterface;
use Modules\Performance\Services\v1\ReviewService;

class GetMyPerformanceReviewsTool implements AiToolInterface
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    public function name(): string
    {
        return 'get_my_performance_reviews';
    }

    public function description(): string
    {
        return 'Retrieves performance reviews and evaluations for the currently authenticated employee, filtered only by closed evaluation cycles.';
    }

    public function parameters(): array
    {
        return [
            'type'       => 'OBJECT',
            'properties' => (object) [],
        ];
    }

    public function declaration(): array
    {
        return [
            'name'        => $this->name(),
            'description' => $this->description(),
            'parameters'  => $this->parameters(),
        ];
    }

    public function execute(array $arguments, int $userId): array
    {
        $paginator = $this->reviewService->myReviews();

$reviewsCollection = collect($paginator->items());
        if ($reviewsCollection->isEmpty()) {
            return [
                'status'  => 'success',
                'message' => 'No performance reviews found for closed evaluation cycles.',
                'data'    => [],
            ];
        }

        $formattedReviews = $reviewsCollection->map(function ($review) {
            return [
                'review_id'    => $review->id,
                'by'    => $review->reviewer->full_name,
                'cycle_name'  => $review->cycle?->name ?? 'N/A',
                'cycle_status' => $review->cycle?->status,
                'rating_score' => $review->score ??  'N/A',
                'comments'     => $review->comments  ?? 'No specific comments',
                'created_at'   => $review->created_at?->format('Y-m-d'),
            ];
        });

        return [
            'status' => 'success',
            'count'  => $formattedReviews->count(),
            'total'  => $paginator->total(),
            'data'   => $formattedReviews->values()->toArray(),
        ];
    }
}
