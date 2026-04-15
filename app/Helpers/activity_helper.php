<?php

if (! function_exists('log_activity')) {
    /**
     * Record an entry in the activity log.
     *
     * @param string      $action      Dot-notation verb  e.g. 'user.created'
     * @param string|null $targetType  Resource type      e.g. 'user'
     * @param int|null    $targetId    Primary key of the affected record
     * @param string|null $description Human-readable summary
     * @param array|null  $metadata    Optional JSON context (before/after values, etc.)
     */
    function log_activity(
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        (new \App\Models\ActivityLogModel())->log(
            $action,
            $targetType,
            $targetId,
            $description,
            $metadata
        );
    }
}
