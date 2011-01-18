<?php
abstract class Module_Common_Model_ImagePng
{
    /**
     * Путь до файла шрифта ttf
     *
     * @access private
     * @var string
     */
    protected $path_ttf;

    /**
     * Ресурс GD
     *
     * @var resource
     */
    protected $iresource;

    /**
     * Основной метод создающий изображение
     * пригодное для вывода через imagepng()
     *
     * @param void
     * @return void
     * @abstract
     */
    abstract public function create();

    public function __construct($ttf)
    {
        if (!file_exists($ttf))
        {
            throw new InvalidArgumentException('Не найден файл шрифта по адресу <b>'.$ttf.'</b>');
        }

        $this->path_ttf = $ttf;
    }
    /**
     * Возвращает ресурс GD.
     *
     * @param void
     * @return resource
     */
    public function getGdResource()
    {
        return $this->iresource;
    }

    /**
     * Возвращает цветовой идентификатор (см. imagecolorallocate ())
     * на основании шестнадцатеричной записи цвета.
     *
     * @param string строка цвета в hex
     * @return int
     * @see imagecolorallocate()
     */
    protected function getRgbByHex($color)
    {
        if (preg_match('#[a-f0-9]{6}#i', $color))
        {
            return imagecolorallocate($this->iresource,
                                      hexdec('0x' . $color{0} . $color{1}),
                                      hexdec('0x' . $color{2} . $color{3}),
                                      hexdec('0x' . $color{4} . $color{5}));
        }

        return false;
    }
}