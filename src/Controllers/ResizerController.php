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
        $rawData      = $request->getQueryParams();
        $preparedData = $this->prepareData($rawData);

        if (array_key_exists('error', $preparedData)) {
            $response->getBody()->write($preparedData['error']);

            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(400);
        }

        $imageFile = $preparedData['image'];
        $size      = $preparedData['size'];
        $cropping  = $preparedData['cropping'] ?? 0;

        $resizer      = new Resizer($size, $cropping);
        $resizedImage = $resizer->resize($imageFile);

        $response->getBody()->write($resizedImage->getImageBlob());

        return $response->withHeader('Content-type', 'image/jpeg')->withStatus(200);
    }

    /**
     * Производит подготовку данных и возвращает массив,
     * содержащий валидные данные для ресайза, файл изображения.
     * В случае неудачи - вернет первую полученную ошибку в ключе 'error'.
     * Ошибка возвращается в следующем порядке: url, size, cropping.
     *
     * @param  array $rawData
     * @return array
     */
    private function prepareData(array $rawData): array
    {
        $validation = Validator::validate($rawData);

        if ($validation->errors()->first('url')) {
            return ['error' => Validator::makeErrorMessage($validation)];
        }

        $imageFile       = @file_get_contents($rawData['url']);
        $imageValidation = Validator::validateImage($imageFile);

        if ($imageValidation->fails()) {
            return ['error' => Validator::makeErrorMessage($imageValidation)];
        }

        if ($validation->fails()) {
            return ['error' => Validator::makeErrorMessage($validation)];
        }

        $validData = $validation->getValidData();

        return array_merge($validData, ['image' => $imageFile]);
    }
}
