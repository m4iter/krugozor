<?php
abstract class Upload_Abstract
{
    /**
     * Массив, содержащий информацию о загруженном файле.
     * Аналог содержания одного элемента $_FILES.
     *
     * @var array
     */
    protected $file;

    /**
     * Максимально-допустимый размер загружаемого файла в байтах.
     *
     * @var int
     */
    protected $max_size;

    /**
     * Массив ошибок, вознихших при загрузке файла.
     *
     * @var array
     */
    protected $errors;

    /**
     * Массив допустимых MIME-типов загружаемого файла.
     *
     * @var array
     */
    protected $allowable_mime_types = array();

   /**
    * Будущее расширение загруженного файла.
    *
    * @var string
    */
    protected $file_ext;

    /**
     * Будущее имя загружаемого файла.
     *
     * @var string
     */
    protected $file_name;

    /**
     * Директория, в которую будет загружен файл.
     *
     * @var string
     */
    protected $file_directory;

    /**
     * Максимально-допустимая длинна имени файла.
     *
     * @var int
     */
    const FILE_NAME_MAX_LENGTH = 255;

    /**
     * Максимально-допустимая длинна расширения файла.
     *
     * @var int
     */
    const FILE_EXT_MAX_LENGTH = 10;

    /**
     * Загружаемый файл превысил параметр php.ini upload_max_filesize
     *
     * @var int
     */
    const ERROR_MAX_FILESIZE_INI = 1;

    /**
     * Загруженный файл превысил допустимый размер $this->max_size
     *
     * @var int
     */
    const ERROR_INVALID_FILE_SIZE = 2;

    /**
     * Файл не был загружен.
     *
     * @var int
     */
    const ERROR_FILE_WAS_NOT_LOADED = 3;

    /**
     * Файл был загружен частями.
     *
     * @var int
     */
    const ERROR_FILE_WAS_ONLY_PARTIALLY = 4;

    /**
     * Недопустимый mime-тип.
     *
     * @var int
     */
    const ERROR_INVALID_FILE_MIME_TYPE = 5;

    /**
     * Принимает значение одного элемента массива $_FILES
     *
     * @param array
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Устанавливает будущее имя загружаемого файла.
     * Если имя не указывается, файл будет сохранён с оригинальным именем.
     *
     * @param string
     */
    public function setName($file_name)
    {
        $this->file_name = self::deleteBadSymbols(trim($file_name));

        // Усекаем имя файла до указанного числа символов.
        if (strlen($this->file_name) > self::FILE_NAME_MAX_LENGTH)
        {
            $this->file_name = substr($this->file_name, 0, self::FILE_NAME_MAX_LENGTH);
        }
    }

    /**
     * Устанавливает расширение загружаемого файла.
     * Если расширение не указывается, файл будет сохранён с оригинальным расширением.
     *
     * @param string
     */
    public function setExt($file_ext)
    {
        $this->file_ext = self::deleteBadSymbols(trim($file_ext));

        // Усекаем расширение файла до указанного числа символов.
        if (strlen($this->file_ext) > self::FILE_EXT_MAX_LENGTH)
        {
            $this->file_ext = substr($this->file_ext, 0, self::FILE_EXT_MAX_LENGTH);
        }
    }

    /**
     * Устанавливает максимально-дрпустимый размер файла.
     * Значение $size может быть любой формой представления
     * человекопонятной нумерации размерности данных, принятых в PHP: 8M, 2B, 30G
     *
     * @param string
     * @return void
     */
    public function setMaxSize($size)
    {
        $this->max_size = Base_String::getBytes($size);
    }

    /**
     * Устанавливает допустимые mime-типы загружаемых файлов.
     *
     * @param array|string массив или строка - допустимые mime-типы
     * @return void
     */
    public function setAllowableType($type)
    {
        $args = func_get_args();

        foreach ($args as $arg)
        {
            if (is_array($arg))
            {
                foreach ($arg as $val)
                {
                    $this->setTrueFilesType($val);
                }
            }
            else
            {
                if (!in_array($arg, $this->allowable_mime_types))
                {
                    $this->allowable_mime_types[] = strtolower($arg);
                }
            }
        }
    }

    /**
     * Возвращает TRUE, если файл был загружен на сервер
     * и FALSE в противном случае.
     *
     * @param void
     * @return bool
     */
    public function isUploaded()
    {
        return $this->file['error'] !== 4 && is_uploaded_file($this->file['tmp_name']);
    }

    /**
     * Конкретный метод класса-наследника должен включить в данный метод
     * те методы проверок на ошибки, которые ему нужны.
     * На данный момент существуют следующие методы проверок на ошибки:
     *
     * $this->checkUpload() - проверяет, был ли загружен файл.
     * $this->checkSize() - проверка на допустимый размер загружаемого файла.
     * $this->checkMimeType() - проверка на mime-тип.
     * $this->checkOtherErrors() - проверка на ошибку неполной загрузки файла.
     *
     * @param  void
     * @return void
     */
    abstract protected function checkErrors();

    /**
     * Возвращает ошибки, если таковые имеются.
     *
     * @return void|array
     */
    public function getErrors()
    {
        if ($this->errors === null)
        {
            $this->errors = array();

            $this->checkErrors();
        }

        return $this->errors;
    }

    /**
     * Копирует загруженный файл в директорию $directory.
     *
     * @param $directory
     * @param $file_name
     * @param $extension
     */
    public function copy($directory)
    {
        if (!is_dir($directory))
        {
            throw new Exception('Не найдена указанная директория для загрузки');
        }

        $this->file_directory = trim($directory, ' /\\').DIRECTORY_SEPARATOR;

        if ($this->file['error'] === 0 &&
            file_exists($this->file['tmp_name']) &&
            is_uploaded_file($this->file['tmp_name']))
        {
            $pathinfo = pathinfo($this->file['name']);

            // Если расширение файла явно объявленно, то оно станет раширением файла при копировании.
            // В противном случае расширением будет оригинальное расширение загруженного файла
            $this->setExt( $this->file_ext ?: (isset($pathinfo['extension'])
                                               ? strtolower($pathinfo['extension'])
                                               : '')
                         );

            // Имя файла будет либо оригинальное, либо объявленное пользователем.
            $this->setName( !empty($this->file_name) ? $this->file_name : $pathinfo['filename'] );

            if (!move_uploaded_file($this->file['tmp_name'],
                                    $this->file_directory.
                                    $this->file_name.
                                    ($this->file_ext ? '.'.$this->file_ext : '')
                                   )
               )
            {
                throw new Exception('Ошибка копирования в директорию '.$this->file_directory);
            }
        }

        return false;
    }

    /**
     * Метод проверки MIME-типа файла.
     *
     * @param void
     * @return void
     */
    protected function checkMimeType()
    {
        if ($this->file['type'] &&
            $this->allowable_mime_types &&
            !in_array($this->file['type'], $this->allowable_mime_types))
        {
            return self::ERROR_INVALID_FILE_MIME_TYPE;
        }

        return 0;
    }

    /**
     * Проверяет, был ли файл загружен частично.
     *
     * @param void
     * @return int ноль в случае успеха, код ошибки в случае ошибки
     */
    protected function checkOtherErrors()
    {
        return $this->file['error'] === 3 ? self::ERROR_FILE_WAS_ONLY_PARTIALLY : 0;
    }

    /**
     * Проверяет, был ли загружен файл.
     * Данная ошибка возникает в ситуациях, когда выполнен POST-запрос,
     * но файл на клиенте не был выбран.
     *
     * @param void
     * @return int
     */
    protected function checkUpload()
    {
        return $this->isUploaded() ? 0 : self::ERROR_FILE_WAS_NOT_LOADED;
    }

    /**
     * Проверяет размер загруженного изображения.
     * Ошибка, вознакающая в ходе данной проверки, может быть вызвана следующими условиями:
     * 1. Размер принятого файла превысил максимально допустимый размер,
     *    который задан директивой upload_max_filesize конфигурационного файла php.ini.
     * 2. Размер загружаемого файла превысил размер $this->max_size
     *
     * @param void
     * @return int
     */
    protected function checkSize()
    {
        if ($this->file['error'] === 1)
        {
            return self::ERROR_MAX_FILESIZE_INI;
        }
        else if (!is_null($this->max_size) && $this->max_size < $this->file['size'])
        {
            return self::ERROR_INVALID_FILE_SIZE;
        }

        return 0;
    }

    /**
     * Удаляет из строки $in все служебные символы Windows и Unix.
     *
     * @param string
     * @return string
     */
    private static function deleteBadSymbols($in)
    {
        return preg_replace('~[/\:*?"<>|]~', '', $in);
    }
}