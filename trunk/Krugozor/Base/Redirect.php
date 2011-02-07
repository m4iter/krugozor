<?php
/**
 *  ласс уведомлений на основе редиректа.
 * »спользуетс€ примерно так:
 *
 * $redirect = new Base_Redirect($databaseInstance);
 * $redirect->setMessage('user_edit_ok');
 * $redirect->addParam('user_name', $this->user->getFullName());
 * $redirect->addParam('id_user', $this->user->getId());
 * $redirect->setRedirectUrl($url);
 * $redirect->run();
 */
class Base_Redirect
{
    private $id_notification;
    private $notification_hidden = 0;
    private $notification_type;
    private $notification_header;
    private $notification_message;
    private $notification_params = array();
    private $redirect_url;
    private $status;

    public function __construct(Db_Mysql_Base $db)
    {
        $this->db = $db;
        $this->status = 200;
        $this->notification_type = 'normal';
    }

    public function getId()
    {
        return $this->id_notification;
    }

    public function getHidden()
    {
        return $this->notification_hidden;
    }

    public function setHidden($is_hidden=1)
    {
        $this->notification_hidden = $is_hidden;

        return $this;
    }

    public function getType()
    {
        return $this->notification_type;
    }

    public function setType($type)
    {
        if (null !== $type)
        {
            $this->notification_type = $type;
        }

        return $this;
    }

    public function getHeader()
    {
        return $this->notification_header;
    }

    public function setHeader($header)
    {
        $this->notification_header = $header;

        return $this;
    }

    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    public function setRedirectUrl($url)
    {
        if (is_array($url))
        {
            $url = forward_static_call_array(array('self', 'implode'), $url);
        }

        $this->redirect_url = (string)$url;

        return $this;
    }

    public function getMessage()
    {
        return $this->notification_message;
    }

    public function setMessage($message)
    {
        $this->notification_message = $message;

        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function addParam($key, $value)
    {
        $this->notification_params[$key] = $value;

        return $this;
    }

    public function getParams()
    {
        return $this->notification_params;
    }

    public static function implode()
    {
        return '/'.implode('/', func_get_args()).'/';
    }

    /**
     * «аписывает в базу сообщение и делает отсылает
     * заголовок Location на $this->redirect_url.
     * «апись в базу будет осуществл€тьс€ только в том случае,
     * если свойство $this->notification_hidden определено
     * как FALSE (не "скрытый" редирект, т.е. с выводом сообщени€).
     *
     * @access public
     * @param void
     * @return void
     */
    public function run()
    {
        if (!$this->notification_hidden)
        {
	        $this->db->query('INSERT INTO `notifications` SET
	                                `notification_hidden` = ?i,
	                                `notification_type` = "?s",
	                                `notification_header` = "?s",
	                                `notification_message` = "?s",
	                                `notification_params` = "?s"
	                   ', $this->notification_hidden,
	                      $this->notification_type,
	                      $this->notification_header,
	                      $this->notification_message,
	                      serialize($this->notification_params)
	                  );

	        if (strpos($this->redirect_url, '?') !== FALSE)
	        {
	            $this->redirect_url .= '&';
	        }
	        else
	        {
	            $this->redirect_url .= '?';
	        }

	        $this->redirect_url = $this->redirect_url . 'notif=' . $this->db->getLastInsertId();
        }

        return $this;
    }

    /**
     * ѕолучает информацию о совершившемс€ действии.
     *
     * @access public
     * @param void
     * @return array
     */
    public function findById($id)
    {
        $res = $this->db->query('
          SELECT
             `notification_hidden`,
             `notification_type` as type,
             `notification_header` as header,
             `notification_message` as message,
             `notification_params` as params
          FROM
             `notifications`
          WHERE
             `id_notification` = ?i
          LIMIT
             0, 1', $id);

        if ($data = $res->fetch_assoc())
        {
            $this->id_notification = $id;
            $this->notification_hidden = $data['notification_hidden'];
            $this->notification_type = $data['type'];
            $this->notification_header = $data['header'];
            $this->notification_message = $data['message'];
            $this->notification_params = unserialize($data['params']);

            $this->db->query('DELETE FROM `notifications` WHERE `id_notification` = ?i', $this->id_notification);
        }
    }
}