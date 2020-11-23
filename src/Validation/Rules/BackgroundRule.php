<?php
namespace App\Validation\Rules;

use Rakit\Validation\Rule;

/**
 * Класс для проверки на валидность цвета background.
 * Расширяет класс Rule и делает доступным новое правило валидации
 *
 * @package App\Validation\Rules
 */
class BackgroundRule extends Rule
{
    /**
     * Разделитель параметров RGB
     *
     * @const string
     */
    protected const RGB_DELIMITER = ',';

    /**
     * Сообщение для вывода в списке ошибок валидатора.
     *
     * @var string
     */
    protected $message = ':attribute - цвет фона должен быть в формате RGB: 255,255,255';

    /**
     * Производит проверку параметра фона
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        if (!$value) {
            return true;
        }

        $rgbParts = explode(self::RGB_DELIMITER, $value);

        if (count($rgbParts) !== 3) {
            return false;
        }

        return array_reduce($rgbParts, static function ($stillCorrect, $part) {
            if (!ctype_digit($part)) {
                return false;
            }

            if ($part < 0 || $part > 255) {
                return false;
            }

            return $stillCorrect;
        }, true);
    }
}
