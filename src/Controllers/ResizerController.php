<?php
namespace App\Controllers;

use App\Resizer;
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
        $rawData    = $request->getQueryParams();
        $validation = self::validate($rawData);

        if ($validation->fails()) {
            $response->getBody()->write(self::makeErrorMessage($validation));

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
            'url:required'     => 'url - не указан обязательный параметр',
            'url:url'          => 'url - должен быть валидным адресом',
            'url:image'        => 'url - размер изображения должен быть меньше :max_size. ' .
                'Разрешенные типы файла - :allowed_types',
            'size:required'    => 'size - не указан обязательный параметр',
            'size:sizeBetween' => 'size - размер должен быть между :min и :max в формате 512x512 (высота x ширина)',
            'cropping:in'      => 'cropping - Значение обрезки изображения должно быть :allowed_values',
        ]);

        $validator->addValidator('image', new ImageRule());
        $validator->addValidator('sizeBetween', new SizeBetweenRule());

        $validator->setTranslations([
            'or' => 'или',
        ]);

        $validation = $validator->make($data, [
            'url'      => 'required|url|image:jpg,jpeg',
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
