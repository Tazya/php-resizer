<?php
namespace App\Validation\Rules;

use Rakit\Validation\Rule;

/**
 * Класс правила валидации для параметра size.
 * Расширяет класс Rule и делает доступным новое правило валидации: соответствие
 * значениям минимальных и максимальных резрешений.
 * Значения выставляются в формате '256x256' (высота х ширина).
 *
 * @package App\Validation\Rules
 */
class SizeBetweenRule extends Rule
{
    /**
     * Сообщение для вывода в списке ошибок валидатора.
     *
     * @var string
     */
    protected $message = "Размер :attribute должен быть между :min и :max";

    /**
     * Список передаваемых параметров при использовании правила в валидаторе.
     *
     * @var array
     */
    protected $fillableParams = ['min', 'max'];

    /**
     * Производит проверку размера для ресайза.
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $minSize = $this->parameter('min');
        $maxSize = $this->parameter('max');

        [$minHeight, $minWidth] = explode('x', $minSize);
        [$maxHeight, $maxWidth] = explode('x', $maxSize);

        [$height, $width] = explode('x', $value);

        $isHeightCorrect = ((int) $height >= (int) $minHeight) && ((int) $height <= (int) $maxHeight);
        $isWidthCorrect  = ((int) $width >= (int) $minWidth) && ((int) $width <= (int) $maxWidth);

        return $isHeightCorrect && $isWidthCorrect;
    }
}
