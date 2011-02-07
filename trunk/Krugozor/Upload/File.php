<?php
class Upload_File extends Upload_Abstract
{
    protected function checkErrors()
    {
        $this->checkSize();
        $this->checkMimeType();
        $this->checkOtherErrors();
    }

    public function getFileObject()
    {
        $file = new Module_Advert_Model_File();
        $file->setExt($this->file_ext);
        $file->setName($this->file_name);
        $file->setMimeType($this->file['type']);
        $file->setSize($this->file['size']);
        return $file;
    }

    protected function checkSize()
    {
        $value = parent::checkSize();

        if ($value == self::ERROR_MAX_FILESIZE_INI)
        {
            $this->errors[] = array('UPLOAD_MAX_FILESIZE',
                                    array('file_name' => $this->file['name'],
                                          'max_size' => Helper_Format::createPhpFormatBytes(
                                                        Base_String::getBytes(ini_get('upload_max_filesize'))
                                                        )
                                         )
                                   );

            return $value;
        }
        else if ($value == self::ERROR_INVALID_FILE_SIZE)
        {
            $this->errors[] = array('UPLOAD_INVALID_FILE_SIZE',
                                    array('file_name' => $this->file['name'],
                                          'file_size' => Helper_Format::createPhpFormatBytes($this->file['size']),
                                          'max_size' => Helper_Format::createPhpFormatBytes($this->max_size),
                                         )
                                   );

            return $value;
        }

        return 0;
    }

    protected function checkUpload()
    {
        if ($value = parent::checkUpload())
        {
            $this->errors[] = array('UPLOAD_FILE_WAS_NOT_LOADED', array());

            return $value;
        }

        return 0;
    }

    protected function checkMimeType()
    {
        if ($value = parent::checkMimeType())
        {
            $this->errors[] = array('UPLOAD_INVALID_FILE_MIME_TYPE',
                                    array('file_name' => $this->file['name'],
                                          'allowable_mime_types' => implode(', ', $this->allowable_mime_types)
                                         )
                                   );
            return $value;
        }

        return 0;
    }

    protected function checkOtherErrors()
    {
        if ($value = parent::checkOtherErrors())
        {
            $this->errors[] = array('UPLOAD_FILE_WAS_ONLY_PARTIALLY', array());

            return $value;
        }

        return 0;
    }
}
?>