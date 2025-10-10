<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;


final class ExcpetionListener
{
    #[AsEventListener]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        if($exception instanceof HttpException){
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ]; 
        }else{
            $data = [
                'status' => 500, 
                'message' => $exception->getMessage()
            ];
        }
        $event->setResponse(new JsonResponse($data));
    }
}
