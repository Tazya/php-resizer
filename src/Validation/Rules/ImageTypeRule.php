<?php
namespace App\Validation\Rules;

use Rakit\Validation\MimeTypeGuesser;
use Rakit\Validation\Rule;

/**
 * Класс для проверки на валидность изображения.
 * Расширяет класс Rule и делает доступным новое правило валидации:
 * соответствие файла изображения требуемым типам.
 *
 * @package App\Validation\Rules
 */
class ImageTypeRule extends Rule
{
    /**
     * Сообщение для вывода в списке ошибок валидатора.
     *
     * @var string
     */
    protected $message = ':attribute - разрешенные типы файла - :allowed_types';

    /**
     * Принимает параметры и записывает их в массив доступных типов.
     *
     * @param  array $params
     * @return Rule
     */
    public function fillParameters(array $params): Rule
    {
        if (count($params) === 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $this->params['allowed_types'] = $params;

        return $this;
    }

    /**
     * Производит проверку изображения на тип.
     * Принимает файл изображения в виде строки
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters(['allowed_types']);

        $allowedTypesText = implode(', ', $this->params['allowed_types']);
        $this->setParameterText('allowed_types', $allowedTypesText);

        $imageData = getimagesizefromstring($value);

        if (!$imageData) {
            return false;
        }

        return $this->isAllowedType($imageData['mime']);
    }

    /**
     * Метод-предикат, проверяет на наличие mime-типа изображения в списке доступных расширений.
     *
     * @param  string $mime
     * @return bool
     */
    protected function isAllowedType(string $mime): bool
    {
        $allowedTypes = $this->parameter('allowed_types');

        if (empty($allowedTypes)) {
            return true;
        }

        $guesser = new MimeTypeGuesser();
        $ext     = $guesser->getExtension($mime);

        if (in_array($ext, $allowedTypes, true)) {
            return true;
        }

        return false;
    }
}
