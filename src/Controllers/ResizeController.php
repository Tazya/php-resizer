<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

/**
 * Класс ResizeController служит для обработки запросов на ресайз изображения.
 * В случае успешного выполнения возвращает обработанное изображение по HTTP.
 * При неуспешном выполнении отдает JSON с указанием ошибок.
 *
 * @package App\Controllers
 */
class ResizeController
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $rawData = $request->getQueryParams();
        $validation = self::validate($rawData);
        if ($validation->fails()) {
            $errors = $validation->errors()->all();
            $firstError = $errors[0];
            $jsonError = json_encode(['error' => $firstError]);

            $response->getBody()->write($jsonError);

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8');
        }

        $payload = json_encode(['status' => 'ok', 'message' => 'Kolesa Academy!']);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * @param array $data
     *
     * @return Validation
     */
    protected static function validate(array $data): Validation
    {
        $validator = new Validator([
            'required'    => ':attribute - не указан обязательный параметр',
            'cropping:in' => 'Значение обрезки изображения должно быть 0 или 1',
        ]);

        $validation = $validator->make($data, [
            'url'      => 'required|url',
            'size'     => 'required',
            'cropping' => [
                $validator('in', ['0', '1']),
            ],
        ]);

        $validation->validate();
        return $validation;
    }
}
