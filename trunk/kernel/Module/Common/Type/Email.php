<?php
class Module_Common_Type_Email implements Module_Common_Type_Interface
{
    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getValue()
    {
        return $this->email;
    }

    /*
    public static function getEmailFromParts($user_email_name, $user_email_domain)
    {
        return new self($user_email_name.'@'.$user_email_domain);
    }

	function getMailParts($mail)
	{
        if (!$dog_position = strpos($mail, '@'))
        {
            return false;
        }

        $parts['local_part'] = substr($mail, 0, $dog_position);

        $last_dot = strrpos($mail, '.');

        $parts['domain_name'] = substr($mail, $dog_position + 1, $last_dot - $dog_position - 1);

        $parts['domain'] = substr($mail, $last_dot + 1);

        return $parts;
    }*/
}