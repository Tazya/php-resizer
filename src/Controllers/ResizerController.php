<?php
namespace App\Controllers;

use App\Validation\Rules\ImageRule;
use App\Validation\Rules\SizeBetweenRule;
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
        $jsonResponse = $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8');

        $rawData    = $request->getQueryParams();
        $validation = self::validate($rawData);

        if ($validation->fails()) {
            $jsonResponse->getBody()->write(self::makeErrorMessage($validation));

            return $jsonResponse->withStatus(400);
        }

        $payload = json_encode(['status' => 'ok', 'message' => 'Kolesa Academy!']);
        $jsonResponse->getBody()->write($payload);

        return $jsonResponse;
    }

    /**
     * Метод принимает массив параметров запроса, проверяет на корректность и
     * возвращает объект содержащий информацию о валидации
     *
     * @param  array      $data
     * @return Validation
     */
    protected static function validate(array $data): Validation
    {
        $validator = new Validator([
            'required'    => ':attribute - не указан обязательный параметр',
            'cropping:in' => 'Значение обрезки изображения :attribute должно быть :allowed_values',
        ]);

        $validator->addValidator('image', new ImageRule());
        $validator->addValidator('sizeBetween', new SizeBetweenRule());

        $validator->setTranslations([
            'or' => 'или',
        ]);

        $validation = $validator->make($data, [
            'url'      => [
                'required',
                'url',
                $validator('image', ['jpg', 'jpeg'])->maxSize('2048x2048'),
            ],
            'size'     => 'required|sizeBetween:256x256,1024x1024',
            'cropping' => 'in:0,1',
        ]);

        $validation->validate();

        return $validation;
    }

    /**
     * Принимает объект валидации и возвращает первую из ошибок
     * в формате json
     *
     * @param  Validation $validation
     * @return string
     */
    protected static function makeErrorMessage(Validation $validation): string
    {
        $errors     = $validation->errors()->all();
        $firstError = $errors[0];

        return json_encode(['error' => $firstError]);
    }
}
