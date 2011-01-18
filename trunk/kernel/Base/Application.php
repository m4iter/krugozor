<?php
class Base_Application
{
    /**
     * Имя переменной запроса, содержащее запрошенный URI.
     * Именно эта переменая содержит запрошенный REQUEST_URI, переданный ей
     * с помощью механизма mod_rewrite-a.
     *
     * @var string
     */
    const REQUEST_PATH = '_path';

    /**
     * Объект-оболочка запроса.
     *
     * @var Http_Request
     */
    private $request;

    /**
     * Объект-оболочка ответа.
     *
     * @var Http_Response
     */
    private $response;

    /**
     * Массив допустимых URL-адресов проекта.
     *
     * @var array
     */
    private $maps = array();

    /**
     * @param Http_Request $request
     * @param Http_Response $response
     */
    public function __construct(Http_Request $request, Http_Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Принимает массив допустимых карт URL.
     *
     * @param array
     * @return Base_Application
     */
    public function setMaps(array $maps)
    {
        $this->maps = $maps;

        return $this;
    }

    /**
     * Основной метод приложения.
     *
     * @param void
     * @return void
     */
    public function run()
    {
        try
        {
            $uri = $this->request->getRequest(self::REQUEST_PATH);

            if (!$this->compareRequestWithUriMaps($uri))
            {
                if (!$this->compareRequestWithStandartUriMap($uri))
                {
                    $this->response->clearHeaders();
                    $this->request->getRequest()->setModuleName(new Http_UriPartEntity('404'));
                    $this->request->getRequest()->setControllerName(new Http_UriPartEntity('404'));
                }
            }

            $controller_name = $this->getControllerClassName(
                                   $this->request->getRequest()->getModuleName()->getCamelCaseStyle(),
                                   $this->request->getRequest()->getControllerName()->getCamelCaseStyle()
                               );

            $controller = new $controller_name($this->request, $this->response);
            $result = $controller->run();

            if (!is_object($result))
            {
                throw new LogicException('Нет результата');
            }

            if ($result instanceof Base_View)
            {
	            // Если в запросе присутствует notif, значит необходимо получить во view
	            // информацию, переданную с предыдущей страницы и вывести её на экране.
	            // @todo: вынести это в контроллер, если можно
	            if ($this->request->getRequest('notif', 'decimal'))
	            {
	                $this->redirect = new Base_Redirect();

	                $this->redirect->findById($this->request->getRequest('notif', 'decimal'));

	                if ($this->redirect->getId())
	                {
	                    $result->setRedirect($this->redirect);
	                }
	            }

	            $result->run();

	            $this->response->sendHeaders();

                echo $result->getOut();

                if (Base_Registry::getInstance()->config['enabled_debug_info'] ||
	                isset($this->request->getRequest()->aaa))
	            {
	                echo $controller->getDebugInformation();
	            }
            }
            else if ($result instanceof Base_Redirect)
            {
                $this->response->setHeader('Location', $result->getRedirectUrl());

                $this->response->sendCookie();

                $this->response->sendHeaders();
            }
            else if ($result instanceof Module_Common_Model_ImagePng)
            {
                $this->response->sendHeaders();

                imagepng($result->getGdResource());
            }
        }
        catch (Exception $e)
        {
            $this->response->sendHeaders();

            echo '<div style="padding:10px"><h3>Фатальная ошибка:</h3><p>' . $e->getMessage() .
            '</p><p><pre>' . print_r($e->getTraceAsString(), 1) . '</pre></p></div>';
        }
    }

    /**
     * Разбирает текущий URI-запрос, который передается в качестве аргумента,
     * и сравнивает его с одним из паттернов URL-карты $this->maps.
     * Если совпадение найдено, то в объект-оболочку Request записывается информация
     * из карт, такая как
     * - имя модуля
     * - имя контролера
     * - запрошеный URI-адрес
     * - параметры запроса.
     *
     * @param string URI-запрос
     * @return boolean true если для запроса $uri найдены совпадения в $this->maps
     *                 и false в противном случае.
     * @todo: сделать проверку на физическое наличие упомянутого в карте $this->maps контроллера.
     */
    private function compareRequestWithUriMaps($uri)
    {
        foreach ($this->maps as $map)
        {
            if (preg_match($map['pattern'], $uri, $params))
            {
                array_shift($params);

                foreach ($params as $index => $value)
                {
                    $this->request->getRequest()->{$map['aliases'][$index]} = $value;
                }

                $this->request->getRequest()->setModuleName(new Http_UriPartEntity($map['module']));
                $this->request->getRequest()->setControllerName(new Http_UriPartEntity($map['controller']));
                $this->request->getRequest()->setUri($uri);

                if (isset($map['additional']))
                {
                    $this->request->getRequest()->setData($map['additional']);
                }

                $this->request->getRequest()->setFrontend(
                    isset($map['is_frontend']) ? (int) $map['is_frontend'] : 0
                );

                return true;
            }
        }

        return false;
    }

    /**
     * По символу "/" разбирает URI-запрос $uri таким образом,
     * что четное число получившихся при разборе значений образуют пары
     * вида "свойство" => "значение". Данные пары помещаются в Request.
     * Первая пара является именем модуля и именем контроллера.
     * Например URI-запрос вида:
     *
     * /ajax/region/country/155
     *
     * метод распарсит таким образом, что при наличие соответствующего файла
     * и класса, в Request будет помещена информация о текущем модуле Ajax,
     * контроллере Region и переменной запроса country со значением 155.
     *
     * @param string URI-запрос
     * @return boolean true если для запроса $uri найден контроллер
     *                 и false в противном случае.
     */
    private function compareRequestWithStandartUriMap($uri)
    {
        $uri_parts = explode('/', trim($uri, ' /'));

        $count_params = count($uri_parts);

        if ($count_params % 2)
        {
            return false;
        }

        for ($i=0; $i<$count_params; $i++)
        {
            $params[$uri_parts[$i]] = $uri_parts[++$i];
        }

        $first_element = Base_Array::array_kshift($params);

        list($module, $controller) = each($first_element);

        try
        {   // Дергаем автолоад, поэтому здесь уместно использовать try-catch
            if (class_exists($this->getControllerClassName(
                Http_UriPartEntity::formatToCamelCaseStyle($module),
                Http_UriPartEntity::formatToCamelCaseStyle($controller)
               )))
            {
                $this->request->getRequest()->setModuleName(new Http_UriPartEntity($module));
                $this->request->getRequest()->setControllerName(new Http_UriPartEntity($controller));
                $this->request->getRequest()->setUri($uri);
                $this->request->getRequest()->setData($params);

                return true;
            }
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     * Возвращает имя класса контроллера.
     *
     * @param $module имя модуля
     * @param $controller имя контроллера
     * @return string
     */
    private function getControllerClassName($module, $controller)
    {
        return 'Module_'.ucfirst($module).'_Controller_'.ucfirst($controller);
    }
}