/*
   Пример использования:

    var ajax = new Ajax();

    // Для асинхронных запросов, вариант 1, без проверок на 
    // readyState и status.
    ajax.setObserverState(function(){alert('async 1:: ' + ajax.getText())});

    // Для асинхронных запросов, вариант 2 с проверкой на 
    // readyState и status.
    ajax.observerState = function()
    {
        if (ajax.getHttpRequest().readyState == 4) {
            if (ajax.getHttpRequest().status == 200) {
                alert('async 2:: ' + ajax.getText())
            }
        }
    }
    
    ajax.get("/ajax_test", true); // false для синхронного запроса 

    // Для синхронных запросов
    alert('sync:: ' + ajax.getText());

*/
function Ajax()
{
    /**
     * Экземпляр XMLHttpRequest.
     * 
     * @access private
     * @var object XMLHttpRequest
     */
    var req;

    /**
     * HTTP-заголовки
     * 
     * @access private
     * @var array
     */
    var httpHeaders = {"If-Modified-Since" : "Sat, 1 Jan 2000 00:00:00 GMT"}

    /**
     * Описания статусов readyState
     * 
     * @access private
     * @var array
     */
    var statuses = ['Не инициализиован',
                    'Метод open() вызван, запрос не отправлен',
                    'Запрос был передан',
                    'Ответ сервера принят частично',
                    'Данные приняты, соединение закрыто'];

    this.addUniqueQS = false;
    
    /**
    * Создает объект XMLHttpRequest
    * 
    * @param void
    * @return XMLHttpRequest|null
    */
    (function()
    {
        if (window.XMLHttpRequest) {
            try {
                req = new XMLHttpRequest();
            } catch (e){}
        }
        // only IE 6 =< 
        else if (window.ActiveXObject) {
            try {
                var aVersions = ["MSXML2.XMLHttp.5.0", "MSXML2.XMLHttp.4.0", 
                                 "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp",
                                 "Msxml2.XMLHTTP", 'Microsoft.XMLHTTP'];

                for (var j in aVersions)
                {
                    try {
                        req = new ActiveXObject(aVersions[j]);
                        break;
                    } catch (e){}
                }
            } catch (e){}
        }

        if (!req)
        {
            throw new UserException('XMLHttprequest не работает в вашем броузере');
        }
    })();

    /**
     * Метод принимает в качестве аргумента анонимную функцию,
     * которая привязывается к обработчику onreadystatechange.
     * Функция должна иметь интерфейс для принятия двух объектов:
     * - первый объект - ссылка на Ajax объект
     * - второй объект - ссылка на объект xmlHttpRequest 
     * 
     * @param object 
     * @return void
     */
    this.setObserverState = function(observer_function)
    {
        var ajax_object = this;
        var req_object = req;

        this.observerState = function()
        {
            if (req_object.readyState == 4) {
                if (req_object.status == 200) {
                    observer_function(ajax_object, this);
                }
            }
        }

        return this;
    }
    
    /**
     * Абстрактный предопределяемый метод, привязанный
     * к обработчику onreadystatechange.
     * Пример предопределения в клиентском скрипте:
     * 
     * var ajax = new Ajax();
     * ajax.observerState = function()
     * {
     *     if (ajax.getHttpRequest().readyState==4) {
     *         if (ajax.getHttpRequest().status == 200)
     *         {
     *             alert(ajax.getHttpRequest().responseText)
     *         }
     *     }
     * }
     *
     * @param void
     * @return mixed
     
    this.observerState = function()
    {
        if (req.readyState == 4) {
            if (req.status == 200) {
                alert('Метод observerState должен быть предопределен перед использованием объекта');
            }
        }
    }*/

    /**
     * Отправляет GET-запрос по адресу url
     * 
     * @param string url
     * @param boolean синхронность запроса.
     *        true - асинхронный, false - синхронный
     * @return void
     */
    this.get = function(url, synchronicity)
    {
    	if (arguments.length == 1)
    	{
    		synchronicity = true;
    	}
    	
        if (!!this.addUniqueQS)
        {
        	url += (url.indexOf('?') == -1 ? '?' : '&') + Math.floor(Math.random()*1000);   
        }
        
        req.open('GET', url, !!synchronicity);

        this.sendHeaders();

        if (synchronicity)
        {
            req.onreadystatechange = this.observerState;
        }

        req.send(null);

        return this;
    }

    /**
     * Устанавилвает заголовок HTTP с ключом key 
     * и значением value.
     * 
     * @access public
     * @param key имя HTTP-заголовка
     * @param value значение HTTP-заголовка
     * @return void
     */
    this.setHeader = function(key, value)
    {
        httpHeaders[key] = value;
    }

    /**
     * Отправляет HTTP-заголовки.
     * 
     * @access private
     * @param void
     * @return void
     */
    this.sendHeaders = function()
    {
        for (var i in httpHeaders)
        {
            if (typeof httpHeaders[i] == 'string')
            {
                req.setRequestHeader(i, httpHeaders[i]);
            }
        }
    }

    /*  Методы получения ответа (XMLHttpRequest) и результата из ответа */

    /**
     * Возвращает экземпляр объекта XMLHttpRequest
     *
     * @param void
     * @return object req
     */
    this.getHttpRequest = function()
    {
        return req;
    }

    /**
     * Возвращает статус HTTP
     * 
     * @param void
     * @return int
     */
    this.getStatus = function()
    {
        return req.status;
    }

    /**
     * Возвращает стандартный объект JS, который является
     * "сериализованным" объектом в виде строки текста ответа - JSON.
     *
     * @access public
     * @param void
     * @return object
     */
    this.getJson2HashByKey = function()
    {
        return eval( "(" + req.responseText + ")" );
    }

    /**
     * Возвращает текст ответа сервера
     *
     * @access public
     * @param void
     * @return string
     */
    this.getText = function()
    {
        return req.responseText;
    }

    /**
     * Создает хеш-массив key => value из XML-представления ответа.
     * XML ответа должен иметь вид: <key>value</key>
     * Корневой элемент полученного XML должен иметь тег root_tag
     *
     * @access public
     * @param root_tag имя корневого тега XML-элемента.
     * @return array
     */
    this.getXml2HashByTagName = function(root_tag)
    {
        if (typeof root_tag == 'undefined')
        {
            var root_tag = 'root';
        }

        var xmlDomDoc = req.responseXML; // объект типа xmldomdocument
        var arr = new Array();
        var nodes = xmlDomDoc.getElementsByTagName(root_tag)[0].childNodes;

        for (var i=0; i <nodes.length; i++)
        {
            if (nodes.item(i).nodeType == 1)
            {
                arr[nodes.item(i).tagName] = nodes.item(i).firstChild
                                             ? nodes.item(i).firstChild.nodeValue
                                             : null;
            }
        }

        return arr;
    }

    /**
     * Создает хеш-массив key => value из XML-представления ответа.
     * XML ответа должен иметь вид: <mytag mykey="key">value</mytag>
     * Корневой элемент полученного XML должен иметь тег root_tag
     *
     * @access public
     * @param root_tag имя корневого тега XML-элемента.
     * @return array
     */
    this.getXml2HashByValue = function(root_tag)
    {
        if (typeof root_tag == 'undefined')
        {
            var root_tag = 'root';
        }

        var xmlDomDoc = req.responseXML; // объект типа xmldomdocument
        var arr = new Array();
        var nodes = xmlDomDoc.getElementsByTagName(root_tag)[0].childNodes;

        for (var i=0; i <nodes.length; i++)
        {
            if (nodes.item(i).nodeType == 1)
            {
                arr[nodes.item(i).getAttribute('value')] = nodes.item(i).firstChild
                                                           ? nodes.item(i).firstChild.nodeValue
                                                           : null;
            }
        }

        return arr;
    }
}