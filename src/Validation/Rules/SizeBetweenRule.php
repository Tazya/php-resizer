<?php
namespace App\Validation\Rules;

use Rakit\Validation\Rule;

/**
 * Класс правила валидации для параметра size.
 *
 * @package App\Validation\Rules
 */
class SizeBetweenRule extends Rule
{
    /** @var string */
    protected $message = "Размер :attribute должен быть между :min и :max";

    /** @var array */
    protected $fillableParams = ['min', 'max'];

    /**
     * Производит проверку размера для ресайза
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $minParameter = $this->parameter('min');
        $maxParameter = $this->parameter('max');

        [$minHeight, $minWidth] = explode('x', $minParameter);
        [$maxHeight, $maxWidth] = explode('x', $maxParameter);

        [$height, $width] = explode('x', $value);

        $isHeightCorrect = ((int) $height >= (int) $minHeight) && ((int) $height <= (int) $maxHeight);
        $isWidthCorrect  = ((int) $width >= (int) $minWidth) && ((int) $width <= (int) $maxWidth);

        return $isHeightCorrect && $isWidthCorrect;
    }
}
