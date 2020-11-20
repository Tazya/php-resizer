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
     * к меньшим размерам указанным в size, меньшая сторона меняется пропорционально.
     * Изображение центрируется, а область не занятая изображением закрашивается белым.
     * При параметре 1 изображение обрезается, меньшая сторона исходного изображения приводится
     * к размерам указанным в size, большая сторона изменяет свой размер пропорционально меньшей,
     * а то что не помещается в новые размеры обрезается.
     *
     * @var int
     */
    protected $cropping;

    /**
     * Цвет фона изображения в формате rgb: 0,0,0
     *
     * @var string
     */
    protected $background;

    /**
     * Конструктор принимает параметры для обрезки:
     * $size, в формате 512x512 (высота x ширина)
     * $cropping (0 или 1).
     *
     * @param string $size
     * @param int    $cropping
     * @param string $background
     */
    public function __construct(string $size, int $cropping = 0, $background = '255,255,255')
    {
        [$height, $width] = explode('x', $size);

        $this->setHeight($height)
            ->setWidth($width)
            ->setCropping($cropping)
            ->setBackground($background);
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
        $image = new Imagick();
        $image->readImageBlob(file_get_contents($url));

        if ($this->hasCropping()) {
            $image->cropThumbnailImage($this->getWidth(), $this->getHeight());

            return $image;
        }

        return $this->createThumbnail($image);
    }

    /**
     * Устанавливает высоту ресайза
     *
     * @param  int  $height
     * @return self
     */
    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Устанавливает ширину ресайза
     *
     * @param  int  $width
     * @return self
     */
    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Устанавливает значение обрезки
     *
     * @param  int  $cropping
     * @return self
     */
    public function setCropping(int $cropping): self
    {
        $this->cropping = $cropping;

        return $this;
    }

    /**
     * Устанавливает цвет фона, принимает строку в формате RGB: 0,0,0
     *
     * @param  string $color
     * @return self
     */
    public function setBackground(string $color): self
    {
        $this->background = $color;

        return $this;
    }

    /**
     * Возвращает высоту ресайза
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Возвращает ширину ресайза
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Возвращает цвет фона в формате rgb: rgb(0,0,0)
     *
     * @return string
     */
    public function getBackground(): string
    {
        return "rgb($this->background)";
    }

    /**
     * Возвращает true, если включена обрезка изображения
     *
     * @return bool
     */
    public function hasCropping(): bool
    {
        return (bool) $this->cropping;
    }

    /**
     * Создает миниатюру без обрезки исходного изображения
     *
     * @param  Imagick $image
     * @return Imagick
     */
    private function createThumbnail(Imagick $image): Imagick
    {
        $resizedImage = $this->resizeAdaptive($image);
        $imageHeight  = $resizedImage->getImageHeight();
        $imageWidth   = $resizedImage->getImageWidth();

        $backgroundHeight = $this->getHeight();
        $backgroundWidth  = $this->getWidth();

        $thumbnail = new Imagick();
        $thumbnail->newImage($backgroundWidth, $backgroundHeight, $this->getBackground());
        $thumbnail->setImageFormat($resizedImage->getImageFormat());
        $thumbnail->setImageColorspace($resizedImage->getImageColorspace());

        $xLocation = round(($backgroundWidth / 2) - ($imageWidth / 2));
        $yLocation = round(($backgroundHeight / 2) - ($imageHeight / 2));

        $thumbnail->compositeImage(
            $resizedImage,
            imagick::COMPOSITE_DEFAULT,
            $xLocation,
            $yLocation
        );

        return $thumbnail;
    }

    /**
     * Пропорционально изменяет размер изображения
     * Большая сторона изображения приводится к меньшей, указанной в свойствах.
     * Меньшая сторона изображения изменяется пропорционально
     *
     * @param  Imagick $image
     * @return Imagick
     */
    private function resizeAdaptive(Imagick $image): Imagick
    {
        $oldHeight = $image->getImageHeight();
        $oldWidth  = $image->getImageWidth();

        $maxHeight = $this->getHeight();
        $maxWidth  = $this->getWidth();

        $xRatio = $maxWidth / $oldWidth;
        $yRatio = $maxHeight / $oldHeight;

        if (($xRatio * $oldHeight) < $maxHeight) {
            $newHeight = ceil($xRatio * $oldHeight);
            $newWidth  = $maxWidth;
        } else {
            $newWidth  = ceil($yRatio * $oldWidth);
            $newHeight = $maxHeight;
        }

        $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_POINT, 0.9);

        return $image;
    }
}
