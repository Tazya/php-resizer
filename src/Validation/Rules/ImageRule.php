<?php
namespace App\Validation\Rules;

use Rakit\Validation\MimeTypeGuesser;
use Rakit\Validation\Rule;

/**
 * Класс для проверки на валидность изображения.
 * Расширяет класс Rule и делает доступным новое правило валидации:
 * соответствие изображения требуемым расширениями и переданному максимальному разрешению.
 *
 * @package App\Validation\Rules
 */
class ImageRule extends Rule
{
    /**
     * Устанавливает максимальный размер изображения по-умолчанию
     *
     * @const string
     */
    protected const DEFAULT_MAX_SIZE = '2048x2048';

    /**
     * Сообщение для вывода в списке ошибок валидатора.
     *
     * @var string
     */
    protected $message = "Размер изображения должен быть меньше :max_size. Разрешенные типы файла - :allowed_types";

    /**
     * Устанавливает дефолтный максимальный размер изображения.
     * Принимает параметры и записывает их в массив доступных типов.
     *
     * @param  array $params
     * @return Rule
     */
    public function fillParameters(array $params): Rule
    {
        if (!array_key_exists('max_size', $this->params)) {
            $this->params['max_size'] = self::DEFAULT_MAX_SIZE;
        }

        if (count($params) === 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $this->params['allowed_types'] = $params;

        return $this;
    }

    /**
     * Метод устанавливает максимальный размер изображения в формате 2024x2024 (высота x ширина).
     *
     * @param  string $maxSize
     * @return self
     */
    public function maxSize(string $maxSize): self
    {
        $this->params['max_size'] = $maxSize;

        return $this;
    }

    /**
     * Производит проверку изображения на максимальное разрешение и тип
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters(['allowed_types']);

        $allowedTypesText = implode(', ', $this->params['allowed_types']);
        $this->setParameterText('allowed_types', $allowedTypesText);

        $imageData = getimagesize($value);

        if (!$imageData) {
            return false;
        }

        [$width, $height] = $imageData;
        $mime             = $imageData['mime'];

        return $this->isAllowedType($mime) && $this->isSizeCorrect($height, $width);
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

    /**
     * Метод-предикат, делает проверку на непревышение максимально допустимых размеров изображения.
     *
     * @param  int  $height
     * @param  int  $width
     * @return bool
     */
    protected function isSizeCorrect(int $height, int $width): bool
    {
        $maxSize                = $this->parameter('max_size');
        [$maxHeight, $maxWidth] = explode('x', $maxSize);

        $isHeightCorrect = $height < (int) $maxHeight;
        $isWidthCorrect  = $width < (int) $maxWidth;

        return $isHeightCorrect && $isWidthCorrect;
    }
}
