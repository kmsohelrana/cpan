<?php

namespace App\Logs;

use Opcodes\LogViewer\Logs\LaravelLog;

class PatchedLaravelLog extends LaravelLog
{
    /**
     * Override to strip null entries from sequential-array contexts (e.g. [null]).
     *
     * Laravel's Monolog writes a [null] context when a log call is made with a
     * single null context value, which causes the log viewer's Vue renderer to
     * crash (blank screen) when the entry is expanded. We filter those out for
     * list-style contexts only, leaving associative/object contexts untouched.
     */
    protected function extractContextsFromFullText(): void
    {
        parent::extractContextsFromFullText();

        if (is_array($this->context) && array_is_list($this->context)) {
            $this->context = array_values(array_filter($this->context, fn ($value) => $value !== null));
        }
    }
}
