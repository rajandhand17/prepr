<?php

namespace App\Traits;

trait SyncUtilTrait
{
    /**
     * @param int $count
     * @param int $size
     *
     * @return array
     */
    protected function prepareBatch(int $count, int $size = 100): array
    {
        $iterations = floor($count / $size);
        $last = ceil($count / $size);
        $batches = collect();

        for ($i = 0; $i < $iterations; $i++) {
            if ($i === 0) {
                $batches->push([
                    'skip' => 0,
                    'take' => $size,
                ]);
            } else {
                $batches->push([
                    'skip' => $i * $size,
                    'take' => $size,
                ]);
            }
        }
        $batches->push([
            'skip' => (int) ($iterations * $size),
            'take' => $size,
        ]);

        return $batches->toArray();
    }

    private function getSchemaName(): string
    {
        return $this->schemaName;
    }
}
