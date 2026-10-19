<?php

namespace App\Exceptions;

use Throwable;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
    }

    public function render($request, Throwable $e) {
		// API clients should receive an authentication response, never a browser redirect.
		if ($request->is('api/*') && $e instanceof AuthenticationException) {
			return response()->json([
				'status' => 'error',
				'message' => 'Unauthenticated. Send a valid member bearer token in the Authorization header.',
			], 401);
		}

		if ($e instanceof TransportException) {
			if ($request->ajax()) {
				return response()->json(['result' => 'error', 'message' => 'SMTP Configuration is incorrect !']);
			} else {
				return redirect()->route('login')->with('error', 'SMTP Configuration is incorrect !');
			}
		}

		return parent::render($request, $e);
	}
}
