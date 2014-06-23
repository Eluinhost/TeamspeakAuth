<?php
namespace PublicUHC\TeamspeakAuth\Controllers;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends ContainerAware {

    public function onException(FlattenException $exception) {
        $format =  $this->container->get('request')->getRequestFormat();

        $errorCode = $exception->getStatusCode();

        $errorMessage = 'Unknown Error';
        $errorType = 'Unknown Error';
        switch($errorCode) {
            case 400:
                $errorMessage = $exception->getMessage();
                $errorType = 'Bad Request';
                break;
            case 403:
                $errorMessage = 'Access Denied';
                $errorType = 'Unauthorized';
                break;
            case 404:
                $errorMessage = 'File Not Found';
                $errorType = 'Not Found';
                break;
            case 500:
                $errorMessage = 'Internal Server Error';
                $errorType = 'Internal Server Error';
                break;
        }

        switch($format) {
            case 'json':
                return new JsonResponse([
                    'ERROR' => $errorMessage
                ], $errorCode);
            default:
                $errorCodeHtml = str_replace('0', '<i class="fa fa-times-circle"></i>', $errorCode);
                return new Response(
                    $this->container->get('templating')->render(
                        'error.html.haml',
                        [
                            'errorMessage'  => $errorMessage,
                            'errorCode'     => $errorCode,
                            'errorCodeHtml'     => $errorCodeHtml,
                            'errorType'     => $errorType
                        ]
                    )
                );
        }
    }
} 