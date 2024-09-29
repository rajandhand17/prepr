<?php

namespace App\Exceptions;

use Exception;

class MaestroAuthenticationException extends Exception
{
    /**
     * Maestro error exception.
     */
    public function report()
    {
        \Log::error('Unauthenticated access to Maestro route');
    }

    public function render($request)
    {
        return redirect()->route('login')->with('error', 'Please login to view panel.');
    }
}
