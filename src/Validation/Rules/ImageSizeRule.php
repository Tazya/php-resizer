<?php
namespace App\Validation\Rules;

use Rakit\Validation\Rule;

/**
 * Класс для проверки на валидность размера изображения.
 * Расширяет класс Rule и делает доступным новое правило валидации:
 * соответствие изображения переданному максимальному разрешению.
 *
 * @package App\Validation\Rules
 */
class ImageSizeRule extends Rule
{
    /**
     * Сообщение для вывода в списке ошибок валидатора.
     *
     * @var string
     */
    protected $message = ":attribute - Размер изображения должен быть меньше :max_size.";

    /**
     * Список передаваемых параметров при использовании правила в валидаторе.
     *
     * @var array
     */
    protected $fillableParams = ['max_size'];

    /**
     * Производит проверку изображения на максимальное разрешение.
     * Принимает файл изображения в виде строки
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $imageData = getimagesizefromstring($value);

        if (!$imageData) {
            return false;
        }

        [$width, $height] = $imageData;

        $maxSize                = $this->parameter('max_size');
        [$maxHeight, $maxWidth] = explode('x', $maxSize);

        $isHeightCorrect = $height <= (int) $maxHeight;
        $isWidthCorrect  = $width <= (int) $maxWidth;

        return $isHeightCorrect && $isWidthCorrect;
    }
}
