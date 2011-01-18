<?php
class Module_Common_Type_Datetime extends DateTime
{
    /**
     * ��������� ���� �� ���������.
     *
     * @var string
     */
    const DEFAULT_TIMEZONE = 'Europe/Moscow';

    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        if (empty($time) || false === strtotime($time))
        {
            throw new UnexpectedValueException();
        }

        parent::__construct($time, self::getBugTimezone($timezone));
    }

    /**
     * @see parent::createFromFormat()
     */
    public static function createFromFormat($format, $time, $timezone = null)
    {
        $datetime = parent::createFromFormat($format, $time, self::getBugTimezone($timezone));

        $this_instance = new self();

        return $this_instance->setTimestamp($datetime->getTimestamp());
    }

    /**
     * ������� ���������� ��������� ���������������� ������������� �������.
     *
     * @param void
     * @return string
     */
    public  function formatDateForPeople()
    {
        // ��������� ���� � 0 �����
        $yesterday_begin = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));

        // ��������� ����, �� ������� �� ���������� ���.
        $yesterday_end = mktime(23, 59, 59, date("m"), date("d") - 1, date("Y"));

        if ($this->getTimestamp() >= $yesterday_begin && $this->getTimestamp() <= $yesterday_end)
        {
            return '����� � '.$this->format('H:i');
        }
        else if ($this->getTimestamp() <= $yesterday_end)
        {
            return $this->format('d.m.Y H:i');
        }
        else
        {
            return '������� � '.$this->format('H:i');
        }
    }

    /**
     * Bug #52063   DateTime constructor's second argument doesn't have a null default value
     * http://bugs.php.net/bug.php?id=52063&thanks=6
     */
    private static function getBugTimezone($timezone)
    {
        return null === $timezone || !$timezone instanceof DateTimeZone
               ? new DateTimeZone(self::DEFAULT_TIMEZONE)
               : $timezone;
    }
}
?>