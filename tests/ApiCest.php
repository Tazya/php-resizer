<?php
//@codingStandardsIgnoreStart
// phpcs:ignoreFile
use Codeception\Example;

/**
 * Class ApiCest
 */
class ApiCest
{
    /**
     * Проверяем позитивные сценарии
     *
     * @dataProvider positiveDataProvider
     *
     * @param  \ApiTester           $I
     * @param  \Codeception\Example $example
     * @throws \ImagickException
     */
    public function positiveTest(ApiTester $I, Example $example)
    {
        $I->sendGET(
            '/',
            [
                'url'      => $example['url'],
                'size'     => "{$example['height']}x{$example['width']}",
                'cropping' => $example['cropping'],
            ]
        );
        $I->seeResponseCodeIs(200);
        $I->seeHttpHeader('Content-Type', 'image/jpeg');

        $image = new Imagick();
        $image->readImageBlob($I->grabResponse());
        $geometry = $image->getImageGeometry();

        $I->assertEquals($example['width'], $geometry['width'], 'Неправильная ширина');
        $I->assertEquals($example['height'], $geometry['height'], 'Неправильная высота');
    }

    /**
     * Проверяем обработку ошибок в негативных сценариях
     *
     * @dataProvider negativeDataProvider
     *
     * @param \ApiTester           $I
     * @param \Codeception\Example $example
     */
    public function negativeTest(ApiTester $I, Example $example): void
    {
        $I->comment("Негативный сценарий для {$example['get']}");

        $I->sendGET($example['get']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(['error' => 'string:!empty']);
        $I->seeResponseContains(sprintf('"error":"%s -', $example['error']));
    }

    /**
     * Дата провайдер для негативных тестов
     *
     * @return string[][]
     */
    public function negativeDataProvider(): array
    {
        return [
            [
                'get'   => '/?size=1024x1024&croping=3',
                'error' => 'url',
            ],
            [
                'get'   => '/?size=2049x1024&croping=0&url=https%3A%2F%2Fwww.google.com%2F',
                'error' => 'url',
            ],
            [
                'get'   =>
                    '/?url=https%3A%2F%2Fupload.wikimedia.org%2Fwikipedia%2Fcommons%2Fthumb%2Fd%2Fd6%2FManoel.jpg' .
                    '%2F2160px-Manoel.jpg&size=1024x1024',
                'error' => 'url',
            ],
            [
                'get'   =>
                    '/?url=https%3A%2F%2Fjob.kolesa.kz%2Ffiles%2F000%2F000%2F_XUou011.jpg&size=1025x1024&croping=0',
                'error' => 'size',
            ],
            [
                'get'   =>
                    '/?url=https%3A%2F%2Fjob.kolesa.kz%2Ffiles%2F000%2F000%2F_XUou011.jpg&size=256x255&croping=0',
                'error' => 'size',
            ],
            [
                'get'   =>
                    '/?url=https%3A%2F%2Fjob.kolesa.kz%2Ffiles%2F000%2F000%2F_XUou011.jpg&size=256,256&croping=0',
                'error' => 'size',
            ],
            [
                'get'   =>
                    '/?url=https%3A%2F%2Fjob.kolesa.kz%2Ffiles%2F000%2F000%2F_XUou011.jpg&size=1024x1024&croping=3',
                'error' => 'croping',
            ],
            [
                'get'   =>
                    '/?url=https%3A%2F%2Fjob.kolesa.kz%2Ffiles%2F000%2F000%2F_XUou011.jpg&size=1024x1024&croping=-1',
                'error' => 'croping',
            ],
        ];
    }

    /**
     * Дата провайдер для позитивных тестов
     *
     * @return array[]
     */
    public function positiveDataProvider(): array
    {
        return [
            [
                'url'      => 'https://job.kolesa.kz/files/000/000/_XUou011.jpg',
                'height'   => 256,
                'width'    => 256,
                'cropping' => 0,
            ],
            [
                'url'      => 'https://job.kolesa.kz/files/000/000/_XUou011.jpg',
                'height'   => 1024,
                'width'    => 1024,
                'cropping' => 0,
            ],
            [
                'url'      => 'https://job.kolesa.kz/files/000/000/_XUou011.jpg',
                'height'   => 512,
                'width'    => 1024,
                'cropping' => 1,
            ],
            [
                'url'      => 'https://job.kolesa.kz/files/000/000/_XUou011.jpg',
                'height'   => 1024,
                'width'    => 512,
                'cropping' => 1,
            ],
        ];
    }
}
