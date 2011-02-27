<?php
class Base_Application
{
    /**
     * Имя переменной запроса, содержащее запрошенный URI.
     * Эта переменая содержит запрошенный REQUEST_URI, переданный ей
     * с помощью механизма mod_rewrite-a (см. .htaccess).
     *
     * @var string
     */
    const REQUEST_PATH = '_path';

    /**
     * Объект-хранилище, содержащий все "звёздные" объекты системы.
     *
     * @var Base_Context
     */
    private $context;

    /**
     * Массив допустимых URL-адресов проекта в виде
     * массивов регулярных выражений (см. /config/routes.php).
     *
     * @var array
     */
    private $routes = array();

    /**
     * Указывает, выводить ли отладочную информацию.
     * true - выводить, false - нет.
     *
     * @var boolean
     */
    private $enabled_debug_info = false;

    /**
     * @param Base_Context $context
     */
    public function __construct(Base_Context $context)
    {
        $this->context = $context;
    }

    /**
     * Принимает массив допустимых маршрутов URL.
     *
     * @param array
     * @return Base_Application
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Принимает путь к PHP-файлу описания маршрутов URL.
     * Файл должен с помощью конструкции return возвращать массив
     * правил маршрутизации (см. /config/routes.php).
     *
     * @param string путь до файла правил маршрутизации
     * @return Base_Application
     */
    public function getRoutesFromPhpFile($path)
    {
        if (!file_exists($path))
        {
            throw new RuntimeException('Не найден указанный файл маршрутов');
        }

        $this->setRoutes((array) include $path);

        return $this;
    }

    /**
     * Устанавливает, нужно ли выводить отладочную информацию.
     *
     * @param bool
     * @return Base_Application
     */
    public function enabledDebugInfo($enabled)
    {
        $this->enabled_debug_info = (bool)$enabled;

        return $this;
    }

    /**
     * Основной метод приложения, запускающий конкретные контроллеры
     * и отдающий в output результат.
     *
     * @param void
     * @return void
     */
    public function run()
    {
        $uri = $this->context->getRequest()->getRequest(self::REQUEST_PATH);

        if ($uri === null)
        {
            $uri = '/';
        }

        if (!$this->compareRequestWithUriRoutes($uri))
        {
            if (!$this->compareRequestWithStandartUriMap($uri))
            {
                $this->context->getResponse()->clearHeaders();
                $this->context->getRequest()->getRequest()->setModuleName(new Http_UriPartEntity('404'));
                $this->context->getRequest()->getRequest()->setControllerName(new Http_UriPartEntity('404'));
            }
        }

        $controller_name = $this->getControllerClassName
        (
            $this->context->getRequest()->getRequest()->getModuleName()->getCamelCaseStyle(),
            $this->context->getRequest()->getRequest()->getControllerName()->getCamelCaseStyle()
        );

        $controller = new $controller_name($this->context);
        $result = $controller->run();

        if (!is_object($result))
        {
            throw new RuntimeException('Нет результата');
        }

        if ($result instanceof Base_View)
        {
            // Если в запросе присутствует notif, значит необходимо получить во view
            // информацию, переданную с предыдущей страницы и вывести её на экране.
            // @todo: вынести это в контроллер, если можно
            if ($this->context->getRequest()->getRequest('notif', 'decimal'))
            {
                $redirect = new Base_Redirect($this->context->getDb());

                $redirect->findById($this->context->getRequest()->getRequest('notif', 'decimal'));

                if ($redirect->getId())
                {
                    $result->setRedirect($redirect);
                }
            }

            $result->run();

            $this->context->getResponse()->sendCookie()
                                         ->sendHeaders();
            echo $result->getOut();

            if ($this->enabled_debug_info || isset($this->context->getRequest()->getRequest()->aaa))
            {
                echo $controller->getDebugInformation();
            }
        }
        else if ($result instanceof Base_Redirect)
        {
            $this->context->getResponse()->setHeader('Location', $result->getRedirectUrl())
                                         ->sendCookie()
                                         ->sendHeaders();
        }
        else if ($result instanceof Module_Common_Model_ImagePng)
        {
            $this->context->getResponse()->sendHeaders();

            imagepng($result->getGdResource());
        }
    }

    /**
     * Разбирает текущий URI-запрос, который передается в качестве аргумента,
     * и сравнивает его с одним из паттернов URL-карты $this->routes.
     * Если совпадение найдено, то в объект-оболочку Request записывается информация
     * из карт, такая как
     * - имя модуля
     * - имя контролера
     * - запрошеный URI-адрес
     * - параметры запроса.
     *
     * @param string URI-запрос
     * @return boolean true если для запроса $uri найдены совпадения в $this->routes
     *                 и false в противном случае.
     * @todo: сделать проверку на физическое наличие упомянутого в карте $this->routes контроллера.
     */
    private function compareRequestWithUriRoutes($uri)
    {
        foreach ($this->routes as $map)
        {
            if (preg_match($map['pattern'], $uri, $params))
            {
                array_shift($params);

                foreach ($params as $index => $value)
                {
                    $this->context->getRequest()->getRequest()->{$map['aliases'][$index]} = $value;
                }

                $this->context->getRequest()->getRequest()->setModuleName(new Http_UriPartEntity($map['module']));
                $this->context->getRequest()->getRequest()->setControllerName(new Http_UriPartEntity($map['controller']));
                $this->context->getRequest()->getRequest()->setUri($uri);

                if (isset($map['additional']))
                {
                    $this->context->getRequest()->getRequest()->setData($map['additional']);
                }

                $this->context->getRequest()->getRequest()->setFrontend(
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
                $this->context->getRequest()->getRequest()->setModuleName(new Http_UriPartEntity($module));
                $this->context->getRequest()->getRequest()->setControllerName(new Http_UriPartEntity($controller));
                $this->context->getRequest()->getRequest()->setUri($uri);
                $this->context->getRequest()->getRequest()->setData($params);

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
     * @param string $module имя модуля
     * @param string $controller имя контроллера
     * @return string
     */
    private function getControllerClassName($module, $controller)
    {
        return 'Module_' . ucfirst($module) . '_Controller_' . ucfirst($controller);
    }
}