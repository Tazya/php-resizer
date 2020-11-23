<?php
namespace App\Validation;

use App\Validation\Rules\ImageSizeRule;
use App\Validation\Rules\ImageTypeRule;
use App\Validation\Rules\NotFalseRule;
use App\Validation\Rules\SizeBetweenRule;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator as RakitValidator;

/**
 * Класс для валидации запроса на ресайз изображения
 *
 * @package App\Validation
 */
class Validator
{
    /**
     * Метод принимает массив параметров запроса, проверяет на корректность и
     * возвращает объект содержащий информацию о валидации
     *
     * @param  array      $data
     * @return Validation
     */
    public static function validate(array $data): Validation
    {
        $validator = new RakitValidator([
            'url:required'     => 'url - не указан обязательный параметр',
            'url:url'          => 'url - должен быть валидным адресом',
            'size:required'    => 'size - не указан обязательный параметр',
            'size:sizeBetween' => 'size - размер должен быть между :min и :max в формате 512x512 (высота x ширина)',
            'cropping:in'      => 'cropping - Значение обрезки изображения должно быть :allowed_values',
        ]);

        $validator->addValidator('sizeBetween', new SizeBetweenRule());

        $validator->setTranslations([
            'or' => 'или',
        ]);

        $validation = $validator->make($data, [
            'url'      => 'required|url',
            'size'     => 'required|sizeBetween:256x256,1024x1024',
            'cropping' => 'in:0,1',
        ]);

        $validation->validate();

        return $validation;
    }

    /**
     * Метод принимает файл изображения в виде строки и проверяет его на
     * соответствие максимальным размерам и разрешенным типам
     *
     * @param  string|bool $imageFile
     * @return Validation
     */
    public static function validateImage($imageFile): Validation
    {
        $validator = new RakitValidator([
            'image:fileExists'   => 'url - изображение не существует',
            'image:imageType'    => 'url - разрешенные типы файла - :allowed_types',
            'image:imageMaxSize' => 'url - размер изображения должен быть меньше или равен :max_size.',
        ]);

        $validator->addValidator('fileExists', new NotFalseRule());
        $validator->addValidator('imageType', new ImageTypeRule());
        $validator->addValidator('imageMaxSize', new ImageSizeRule());

        $validation = $validator->make(
            ['image' => $imageFile],
            ['image' => 'fileExists|imageType:jpg,jpeg|imageMaxSize:2048x2048']
        );

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
    public static function makeErrorMessage(Validation $validation): string
    {
        $errors     = $validation->errors()->all();
        $firstError = $errors[0];

        return json_encode(['error' => $firstError]);
    }
}
