/*
   ������ �������������:

    var ajax = new Ajax();

    // ��� ����������� ��������, ������� 1, ��� �������� �� 
    // readyState � status.
    ajax.setObserverState(function(){alert('async 1:: ' + ajax.getText())});

    // ��� ����������� ��������, ������� 2 � ��������� �� 
    // readyState � status.
    ajax.observerState = function()
    {
        if (ajax.getHttpRequest().readyState == 4) {
            if (ajax.getHttpRequest().status == 200) {
                alert('async 2:: ' + ajax.getText())
            }
        }
    }
    
    ajax.get("/ajax_test", true); // false ��� ����������� ������� 

    // ��� ���������� ��������
    alert('sync:: ' + ajax.getText());

*/
function Ajax()
{
    /**
     * ��������� XMLHttpRequest.
     * 
     * @access private
     * @var object XMLHttpRequest
     */
    var req;

    /**
     * HTTP-���������
     * 
     * @access private
     * @var array
     */
    var httpHeaders = {"If-Modified-Since" : "Sat, 1 Jan 2000 00:00:00 GMT"}

    /**
     * �������� �������� readyState
     * 
     * @access private
     * @var array
     */
    var statuses = ['�� ��������������',
                    '����� open() ������, ������ �� ���������',
                    '������ ��� �������',
                    '����� ������� ������ ��������',
                    '������ �������, ���������� �������'];

    this.addUniqueQS = false;
    
    /**
    * ������� ������ XMLHttpRequest
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
            throw new UserException('XMLHttprequest �� �������� � ����� ��������');
        }
    })();

    /**
     * ����� ��������� � �������� ��������� ��������� �������,
     * ������� ������������� � ����������� onreadystatechange.
     * ������� ������ ����� ��������� ��� �������� ���� ��������:
     * - ������ ������ - ������ �� Ajax ������
     * - ������ ������ - ������ �� ������ xmlHttpRequest 
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
     * ����������� ���������������� �����, �����������
     * � ����������� onreadystatechange.
     * ������ ��������������� � ���������� �������:
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
                alert('����� observerState ������ ���� ������������� ����� �������������� �������');
            }
        }
    }*/

    /**
     * ���������� GET-������ �� ������ url
     * 
     * @param string url
     * @param boolean ������������ �������.
     *        true - �����������, false - ����������
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
     * ������������� ��������� HTTP � ������ key 
     * � ��������� value.
     * 
     * @access public
     * @param key ��� HTTP-���������
     * @param value �������� HTTP-���������
     * @return void
     */
    this.setHeader = function(key, value)
    {
        httpHeaders[key] = value;
    }

    /**
     * ���������� HTTP-���������.
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

    /*  ������ ��������� ������ (XMLHttpRequest) � ���������� �� ������ */

    /**
     * ���������� ��������� ������� XMLHttpRequest
     *
     * @param void
     * @return object req
     */
    this.getHttpRequest = function()
    {
        return req;
    }

    /**
     * ���������� ������ HTTP
     * 
     * @param void
     * @return int
     */
    this.getStatus = function()
    {
        return req.status;
    }

    /**
     * ���������� ����������� ������ JS, ������� ��������
     * "���������������" �������� � ���� ������ ������ ������ - JSON.
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
     * ���������� ����� ������ �������
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
     * ������� ���-������ key => value �� XML-������������� ������.
     * XML ������ ������ ����� ���: <key>value</key>
     * �������� ������� ����������� XML ������ ����� ��� root_tag
     *
     * @access public
     * @param root_tag ��� ��������� ���� XML-��������.
     * @return array
     */
    this.getXml2HashByTagName = function(root_tag)
    {
        if (typeof root_tag == 'undefined')
        {
            var root_tag = 'root';
        }

        var xmlDomDoc = req.responseXML; // ������ ���� xmldomdocument
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
     * ������� ���-������ key => value �� XML-������������� ������.
     * XML ������ ������ ����� ���: <mytag mykey="key">value</mytag>
     * �������� ������� ����������� XML ������ ����� ��� root_tag
     *
     * @access public
     * @param root_tag ��� ��������� ���� XML-��������.
     * @return array
     */
    this.getXml2HashByValue = function(root_tag)
    {
        if (typeof root_tag == 'undefined')
        {
            var root_tag = 'root';
        }

        var xmlDomDoc = req.responseXML; // ������ ���� xmldomdocument
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