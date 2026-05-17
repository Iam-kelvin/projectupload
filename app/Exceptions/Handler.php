<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (PostTooLargeException $e, Request $request) {
            $message = 'That PDF is larger than the server currently accepts. Try a smaller PDF or use a direct cloud upload flow for very large files.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 413);
            }

            return back()->withErrors(['pdf_file' => $message]);
        });
    }
}
