<?php
class Module_ImageEmail_Model_ImageEmail extends Module_Common_Model_ImagePng
{
    /**
     * Объект email-a.
     *
     * @var Module_Common_Type_Email
     */
    private $email;

    /**
     * Принимает объект Module_Common_Type_Email
     *
     * @param string
     * @return ImageEmail
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Создает изображение email-адреса.
     *
     * @param void
     * @return void
     */
    public function create()
    {
        $data = imagettfbbox(10, 0, $this->path_ttf, $this->email->getValue());

        $this->iresource = imageCreate($data[2], 20);

        imagefill($this->iresource, 0, 0, $this->getRgbByHex('FFF8E8'));

        imagettftext($this->iresource, 10, 0, 0, 15, $this->getRgbByHex('395773'),
                     $this->path_ttf, $this->email->getValue());
    }
}
?>