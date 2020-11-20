<?php
namespace App\Controllers;

use App\Resizer;
use App\Validation\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Класс ResizeController служит для обработки запросов на ресайз изображения.
 * В случае успешного выполнения возвращает обработанное изображение по HTTP.
 * При неуспешном выполнении отдает JSON с указанием ошибок.
 *
 * @package App\Controllers
 */
class ResizerController
{
    /**
     * Производит обработку запроса на ресайз изображения,
     * в случае успешного выполнения возварщает ответ с изображением.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $rawData    = $request->getQueryParams();
        $validation = Validator::validate($rawData);

        if ($validation->fails()) {
            $response->getBody()->write(Validator::makeErrorMessage($validation));

            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        $validData = $validation->getValidData();
        $size      = $validData['size'];
        $cropping  = $validData['cropping'] ?? 0;

        $resizer      = new Resizer($size, $cropping);
        $resizedImage = $resizer->resize($validData['url']);

        $response->getBody()->write($resizedImage->getImageBlob());

        return $response->withHeader('Content-type', 'image/jpeg')->withStatus(200);
    }
}
