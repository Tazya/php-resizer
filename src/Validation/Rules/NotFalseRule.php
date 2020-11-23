<?php
namespace App\Validation\Rules;

use Rakit\Validation\Rule;

/**
 * Класс для проверки на валидность изображения.
 * Расширяет класс Rule и делает доступным новое правило валидации:
 * соответствие файла изображения требуемым типам.
 *
 * @package App\Validation\Rules
 */
class NotFalseRule extends Rule
{
    /**
     * Если true, и значение не проходит проверку,
     * то дальнейшая валидация этого значения не проводится
     *
     * @var bool
     */
    protected $implicit = true;

    /**
     * Сообщение для вывода в списке ошибок валидатора.
     *
     * @var string
     */
    protected $message = ':attribute - значение не должно быть false';

    /**
     * Производит проверку, является ли значение отличным
     * от false
     *
     * @param  mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return $value !== false;
    }
}
