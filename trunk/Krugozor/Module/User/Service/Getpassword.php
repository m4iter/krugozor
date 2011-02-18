<?php
class Module_User_Service_Getpassword
{
    private $db;
    private $user_mapper;
    private $user;
    private $mail;

    public function __construct()
    {
        $this->db = Base_Registry::getInstance()->objects['db'];
    }

    public function setUser(Module_User_Model_User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function setMail(Base_Mail $mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * ���������� ������ � ���������� �������.
     *
     * @param void
     * @return boolean
     */
    public function sendEmailWithHash()
    {
        if ($this->mail === null)
        {
            throw new Exception(__METHOD__.': �� ��������������� ������ �����');
        }

        if (!is_object($this->user) || !$this->user instanceof Module_User_Model_User)
        {
            throw new Exception(__METHOD__.': �� ��������������� ������ ������������');
        }

        if ($this->user->getMail()->getValue())
        {
            $this->mail->setTo($this->user->getMail()->getValue());

            $this->mail->user = $this->user;
            $this->mail->hash = md5($this->user->getLogin().uniqid(rand(),1));

            $this->db->query('INSERT INTO `getpassword` SET `user_id` = ?i, `hash` = "?s"', $this->user->getId(), $this->mail->hash);

            return $this->mail->send();
        } else {
            throw new Exception(__METHOD__.' �� ����� ��������� �����, ����������� email-����� ������������ '.$this->user->getId());
        }
    }

    /**
     * ��������� ��� $hash �� ����������.
     * � ������ ������ ������������ ������ ������������
     * � ������� ������� ����� ����� �� ������ � ������ �����.
     *
     * @param string $hash ���
     * @return boolean
     */
    public function isValidHash($hash)
    {
        $res = $this->db->query('SELECT `user_id` FROM `getpassword` WHERE `hash` = "?s" LIMIT 1', $hash);

        if (is_object($res) && $res->getNumRows() === 1)
        {
            $this->user = $this->getUserMapper()->findById($res->getOne());

            $this->db->query('DELETE FROM `getpassword` WHERE `hash` = "?s"', $hash);

            return true;
        }

        return false;
    }

    /**
     * ������ ������ � ������������, �������� ����� ������ ��� �� email.
     *
     * @param string|null ����� ������ ������������
     */
    public function sendMailWithNewPassword($new_password=null)
    {
        if ($this->mail === null)
        {
            throw new Exception(__METHOD__.': �� ��������������� ������ �����');
        }

        if (!is_object($this->user) || !$this->user instanceof Module_User_Model_User)
        {
            throw new Exception(__METHOD__.': �� ��������������� ������ ������������');
        }

        $new_password = $new_password === null || !is_scalar($new_password)
                        ? self::createPassword()
                        : $new_password;

        $this->user->setPassword($new_password);

        $this->getUserMapper()->save($this->user);

        if ($this->user->getMail()->getValue())
        {
            $this->mail->setTo($this->user->getMail()->getValue());

            $this->mail->user = $this->user;
            $this->mail->new_password = $new_password;

            return $this->mail->send();
        } else {
            throw new Exception(__METHOD__.' �� ����� ��������� �����, ����������� email-����� ������������ '.$this->user->getId());
        }
    }

    /**
     * ������� ������ ��������� �� �������� � ��������� 0-9a-z
     * ������� $length
     *
     * @param int ������ ������
     * @return string
     */
    private static function createPassword($length=7)
    {
        return substr(md5(uniqid(rand(),1)), 0, $length);
    }

    private function getUserMapper()
    {
        if ($this->user_mapper === null)
        {
            $this->user_mapper = new Module_User_Mapper_User();
        }

        return $this->user_mapper;
    }
}
?>