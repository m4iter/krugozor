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
     * Отправляет письмо с уникальной ссылкой.
     *
     * @param void
     * @return boolean
     */
    public function sendEmailWithHash()
    {
        if ($this->mail === null)
        {
            throw new Exception(__METHOD__.': Не инициализирован объект почты');
        }

        if (!is_object($this->user) || !$this->user instanceof Module_User_Model_User)
        {
            throw new Exception(__METHOD__.': Не инициализирован объект пользователя');
        }

        if ($this->user->getMail()->getValue())
        {
            $this->mail->setTo($this->user->getMail()->getValue());

            $this->mail->user = $this->user;
            $this->mail->hash = md5($this->user->getLogin().uniqid(rand(),1));

            $this->db->query('INSERT INTO `getpassword` SET `user_id` = ?i, `hash` = "?s"', $this->user->getId(), $this->mail->hash);

            return $this->mail->send();
        } else {
            throw new Exception(__METHOD__.' не может отправить псьмо, отсутствует email-адрес пользователя '.$this->user->getId());
        }
    }

    /**
     * Проверяет хэш $hash на валидность.
     * В случае успеха инстанцирует объект пользователя
     * и очищает таблицу учёта хэшей от записи с данным хэшем.
     *
     * @param string $hash хэш
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
     * Меняет пароль у пользователя, отсылает новый пароль ему на email.
     *
     * @param string|null новый пароль пользователя
     */
    public function sendMailWithNewPassword($new_password=null)
    {
        if ($this->mail === null)
        {
            throw new Exception(__METHOD__.': Не инициализирован объект почты');
        }

        if (!is_object($this->user) || !$this->user instanceof Module_User_Model_User)
        {
            throw new Exception(__METHOD__.': Не инициализирован объект пользователя');
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
            throw new Exception(__METHOD__.' не может отправить псьмо, отсутствует email-адрес пользователя '.$this->user->getId());
        }
    }

    /**
     * Создает строку состоящую из символов в диапазоне 0-9a-z
     * длинной $length
     *
     * @param int длинна строки
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