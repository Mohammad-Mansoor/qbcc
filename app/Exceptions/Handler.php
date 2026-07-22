<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Database\QueryException) {
            $message = $exception->getMessage();
            if (strpos($message, 'SQLSTATE[42S22]') !== false || strpos($message, 'Unknown column') !== false) {
                $sql = $exception->getSql();
                
                $missingColumn = 'Unknown';
                if (preg_match("/Unknown column '([^']+)'/", $message, $matches)) {
                    $missingColumn = $matches[1];
                }
                
                $table = 'Unknown';
                if (preg_match("/(?:from|into|update)\s+`?([^`\s]+)`?/i", $sql, $matches)) {
                    $table = $matches[1];
                }

                $logFile = storage_path('logs/missing_columns.log');
                
                $logData = str_repeat("-", 35) . "\n"
                         . "[" . now()->format('Y-m-d H:i:s') . "]\n\n"
                         . "URL: " . request()->fullUrl() . "\n"
                         . "Method: " . request()->method() . "\n\n"
                         . "Table: " . $table . "\n"
                         . "Missing Column: " . $missingColumn . "\n\n"
                         . "SQL:\n" . $sql . "\n\n";
                         
                file_put_contents($logFile, $logData, FILE_APPEND);
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
