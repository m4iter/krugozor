<?php
abstract class Upload_Abstract
{
    /**
     * ������, ���������� ���������� � ����������� �����.
     * ������ ���������� ������ �������� $_FILES.
     *
     * @var array
     */
    protected $file;

    /**
     * �����������-���������� ������ ������������ ����� � ������.
     *
     * @var int
     */
    protected $max_size;

    /**
     * ������ ������, ��������� ��� �������� �����.
     *
     * @var array
     */
    protected $errors;

    /**
     * ������ ���������� MIME-����� ������������ �����.
     *
     * @var array
     */
    protected $allowable_mime_types = array();

   /**
    * ������� ���������� ������������ �����.
    *
    * @var string
    */
    protected $file_ext;

    /**
     * ������� ��� ������������ �����.
     *
     * @var string
     */
    protected $file_name;

    /**
     * ����������, � ������� ����� �������� ����.
     *
     * @var string
     */
    protected $file_directory;

    /**
     * �����������-���������� ������ ����� �����.
     *
     * @var int
     */
    const FILE_NAME_MAX_LENGTH = 255;

    /**
     * �����������-���������� ������ ���������� �����.
     *
     * @var int
     */
    const FILE_EXT_MAX_LENGTH = 10;

    /**
     * ����������� ���� �������� �������� php.ini upload_max_filesize
     *
     * @var int
     */
    const ERROR_MAX_FILESIZE_INI = 1;

    /**
     * ����������� ���� �������� ���������� ������ $this->max_size
     *
     * @var int
     */
    const ERROR_INVALID_FILE_SIZE = 2;

    /**
     * ���� �� ��� ��������.
     *
     * @var int
     */
    const ERROR_FILE_WAS_NOT_LOADED = 3;

    /**
     * ���� ��� �������� �������.
     *
     * @var int
     */
    const ERROR_FILE_WAS_ONLY_PARTIALLY = 4;

    /**
     * ������������ mime-���.
     *
     * @var int
     */
    const ERROR_INVALID_FILE_MIME_TYPE = 5;

    /**
     * ��������� �������� ������ �������� ������� $_FILES
     *
     * @param array
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * ������������� ������� ��� ������������ �����.
     * ���� ��� �� �����������, ���� ����� ������� � ������������ ������.
     *
     * @param string
     */
    public function setName($file_name)
    {
        $this->file_name = self::deleteBadSymbols(trim($file_name));

        // ������� ��� ����� �� ���������� ����� ��������.
        if (strlen($this->file_name) > self::FILE_NAME_MAX_LENGTH)
        {
            $this->file_name = substr($this->file_name, 0, self::FILE_NAME_MAX_LENGTH);
        }
    }

    /**
     * ������������� ���������� ������������ �����.
     * ���� ���������� �� �����������, ���� ����� ������� � ������������ �����������.
     *
     * @param string
     */
    public function setExt($file_ext)
    {
        $this->file_ext = self::deleteBadSymbols(trim($file_ext));

        // ������� ���������� ����� �� ���������� ����� ��������.
        if (strlen($this->file_ext) > self::FILE_EXT_MAX_LENGTH)
        {
            $this->file_ext = substr($this->file_ext, 0, self::FILE_EXT_MAX_LENGTH);
        }
    }

    /**
     * ������������� �����������-���������� ������ �����.
     * �������� $size ����� ���� ����� ������ �������������
     * ���������������� ��������� ����������� ������, �������� � PHP: 8M, 2B, 30G
     *
     * @param string
     * @return void
     */
    public function setMaxSize($size)
    {
        $this->max_size = Base_String::getBytes($size);
    }

    /**
     * ������������� ���������� mime-���� ����������� ������.
     *
     * @param array|string ������ ��� ������ - ���������� mime-����
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
     * ���������� TRUE, ���� ���� ��� �������� �� ������
     * � FALSE � ��������� ������.
     *
     * @param void
     * @return bool
     */
    public function isUploaded()
    {
        return $this->file['error'] !== 4 && is_uploaded_file($this->file['tmp_name']);
    }

    /**
     * ���������� ����� ������-���������� ������ �������� � ������ �����
     * �� ������ �������� �� ������, ������� ��� �����.
     * �� ������ ������ ���������� ��������� ������ �������� �� ������:
     *
     * $this->checkUpload() - ���������, ��� �� �������� ����.
     * $this->checkSize() - �������� �� ���������� ������ ������������ �����.
     * $this->checkMimeType() - �������� �� mime-���.
     * $this->checkOtherErrors() - �������� �� ������ �������� �������� �����.
     *
     * @param  void
     * @return void
     */
    abstract protected function checkErrors();

    /**
     * ���������� ������, ���� ������� �������.
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
     * �������� ����������� ���� � ���������� $directory.
     *
     * @param $directory
     * @param $file_name
     * @param $extension
     */
    public function copy($directory)
    {
        if (!is_dir($directory))
        {
            throw new Exception('�� ������� ��������� ���������� ��� ��������');
        }

        $this->file_directory = trim($directory, ' /\\').DIRECTORY_SEPARATOR;

        if ($this->file['error'] === 0 &&
            file_exists($this->file['tmp_name']) &&
            is_uploaded_file($this->file['tmp_name']))
        {
            $pathinfo = pathinfo($this->file['name']);

            // ���� ���������� ����� ���� ����������, �� ��� ������ ���������� ����� ��� �����������.
            // � ��������� ������ ����������� ����� ������������ ���������� ������������ �����
            $this->setExt( $this->file_ext ?: (isset($pathinfo['extension'])
                                               ? strtolower($pathinfo['extension'])
                                               : '')
                         );

            // ��� ����� ����� ���� ������������, ���� ����������� �������������.
            $this->setName( !empty($this->file_name) ? $this->file_name : $pathinfo['filename'] );

            if (!move_uploaded_file($this->file['tmp_name'],
                                    $this->file_directory.
                                    $this->file_name.
                                    ($this->file_ext ? '.'.$this->file_ext : '')
                                   )
               )
            {
                throw new Exception('������ ����������� � ���������� '.$this->file_directory);
            }
        }

        return false;
    }

    /**
     * ����� �������� MIME-���� �����.
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
     * ���������, ��� �� ���� �������� ��������.
     *
     * @param void
     * @return int ���� � ������ ������, ��� ������ � ������ ������
     */
    protected function checkOtherErrors()
    {
        return $this->file['error'] === 3 ? self::ERROR_FILE_WAS_ONLY_PARTIALLY : 0;
    }

    /**
     * ���������, ��� �� �������� ����.
     * ������ ������ ��������� � ���������, ����� �������� POST-������,
     * �� ���� �� ������� �� ��� ������.
     *
     * @param void
     * @return int
     */
    protected function checkUpload()
    {
        return $this->isUploaded() ? 0 : self::ERROR_FILE_WAS_NOT_LOADED;
    }

    /**
     * ��������� ������ ������������ �����������.
     * ������, ����������� � ���� ������ ��������, ����� ���� ������� ���������� ���������:
     * 1. ������ ��������� ����� �������� ����������� ���������� ������,
     *    ������� ����� ���������� upload_max_filesize ����������������� ����� php.ini.
     * 2. ������ ������������ ����� �������� ������ $this->max_size
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
     * ������� �� ������ $in ��� ��������� ������� Windows � Unix.
     *
     * @param string
     * @return string
     */
    private static function deleteBadSymbols($in)
    {
        return preg_replace('~[/\:*?"<>|]~', '', $in);
    }
}