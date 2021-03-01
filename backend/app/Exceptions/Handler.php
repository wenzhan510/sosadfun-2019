<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;

class Handler extends ExceptionHandler
{
    /**
    * A list of the exception types that are not reported.
    *
    * @var array
    */
    protected $dontReport = [
    ];

    /**
    * A list of the inputs that are never flashed for validation exceptions.
    *
    * @var array
    */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
    * Report or log an exception.
    *
    * @param  \Exception  $exception
    * @return void
    */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
    * Render an exception into an HTTP response.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Exception  $exception
    * @return \Illuminate\Http\Response
    */
    public function render($request, Exception $exception)
    {
        if($exception instanceof AuthenticationException){
            return response()->error(config('error.401'), 401);
        }
        if($exception instanceof AuthorizationException){
            return response()->error(config('error.403'), 403);
        }
        if($exception instanceof ModelNotFoundException){
            return response()->error(config('error.404'), 404);
        }
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            return response()->error($exception->getMessage()??config('error.'.$statusCode), $statusCode);
        }
        if ($exception instanceof PDOException) {
            $dbCode = trim($exception->getCode());
            switch ($dbCode)
            {
                case 23000://db duplicate rows
                return response()->error(config('error.408'), 408);
                break;
                default:
                //echo 'Connection failed: ' . $exception->getMessage();
                $errorMessage = 'database invalid';
            }
            return response()->error($errorMessage, 595);
        }
        //下面这部分代码，会将所有其他的错误掩盖不反馈
        // if ($exception instanceof Exception) {
        //     return response()->error($exception, 599);
        // }
        return parent::render($request, $exception);
    }
}
