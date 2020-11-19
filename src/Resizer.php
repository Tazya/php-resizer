<?php
namespace App;

use \Imagick;

/**
 * Класс для ресайза изображения.
 * Метод resize принимает путь к изображению и возвращает обработанное изображение
 *
 * @package App
 */
class Resizer
{
    /**
     * Параметры высоты и ширины для обрезки
     *
     * @var int
     */
    protected $height;

    /**
     * Параметры ширины для обрезки
     *
     * @var int
     */
    protected $width;

    /**
     * Условия обрезки изображения, имеет два значения (0 или 1), по умолчанию 0.
     * При параметре 0 изображение не обрезается, большая сторона исходного изображения приводиться
     * к размерам указанным в size, меньшая сторона меняется пропорционально.
     * Изображение центрируется, а область не занятая изображением закрашивается белым.
     * При параметре 1 изображение обрезается, меньшая сторона исходного изображения приводится
     * к размерам указанным в size, большая сторона изменяет свой размер пропорционально меньшей,
     * а то что не помещается в новые размеры обрезается.
     *
     * @var int
     */
    protected $cropping;

    /**
     * Конструктор принимает параметры для обрезки:
     * $size, в формате 512x512 (высота x ширина)
     * $cropping (0 или 1).
     *
     * @param string $size
     * @param int    $cropping
     */
    public function __construct(string $size, int $cropping = 0)
    {
        [$this->height, $this->width] = explode('x', $size);
        $this->cropping               = $cropping;
    }

    /**
     * Принимает путь к изображению и обрезает параметрам
     * указанным в свойствах объекта
     *
     * @param  string  $url
     * @return Imagick
     */
    public function resize(string $url): Imagick
    {
        $image     = new Imagick();

        $imageContent = file_get_contents($url);
        $image->readImageBlob($imageContent);

        $cropping  = (bool) $this->cropping;

        if ($cropping) {
            $image->cropThumbnailImage($this->width, $this->height);
            $newImage = $image;
        } else {
            $newImage = $this->createThumbnail($image);
        }

        return $newImage;
    }

    /**
     * Создает миниатюру без обрезки исходного изображения
     *
     * @param  Imagick $image
     * @return Imagick
     */
    private function createThumbnail(Imagick $image): Imagick
    {
        $oldHeight = $image->getImageHeight();
        $oldWidth  = $image->getImageWidth();

        $yRatio = $this->height / $oldHeight;
        $xRatio = $this->width / $oldWidth;

        if (($xRatio * $oldHeight) < $this->height) {
            $newHeight = ceil($xRatio * $oldHeight);
            $newWidth  = $this->width;
        } else {
            $newWidth  = ceil($yRatio * $oldWidth);
            $newHeight = $this->height;
        }

        $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_UNDEFINED, 0.9);

        $halfHeight = $image->getImageHeight() / 2;
        $halfWidth  = $image->getImageWidth() / 2;

        $background = new Imagick();
        $background->newImage($this->width, $this->height, "white");
        $background->setImageFormat('jpg');

        $background->compositeImage(
            $image,
            imagick::COMPOSITE_DEFAULT,
            $this->width / 2 - $halfWidth,
            $this->height / 2 - $halfHeight
        );

        return $background;
    }
}
