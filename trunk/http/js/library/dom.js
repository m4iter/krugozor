/**
* Функция возвращает массив ссылок на тэги tagName 
* с классом className
* 
* @param string tagName имя тега
* @param string className имя класса
*/
getElementsByTagClassName = function (tagName, className)
{
    var arr = new Array();
    var tags = document.getElementsByTagName(tagName);
    var j = 0;

    for (var i=0; i < tags.length; i++)
    {
        var tClasName = tags.item(i).className;
        if (tClasName == className || tClasName.indexOf(className) != -1)
        {
            arr[j++] = tags.item(i);
        }
    }
    
    return arr;
}

/**
* Возвращает firstChild типа тег узла node. 
* 
* @param object
* @return object
*/
function getFirstChildTag(node)
{
    var node = node.firstChild;

    while (node && node.nodeType != 1)
    {
        node = node.nextSibling;
    }

    return node;
}

/**
* Возвращает previousSibling типа тег узла node. 
* 
* @param object
* @return object
*/
function getPreviousSiblingTag(node)
{
    node = node.previousSibling;
    
    while (node && node.nodeType != 1)
    {
        node = node.previousSibling;
    }

    return node;
}

/**
* Возвращает nextSibling типа тег узла node. 
* 
* @param object
* @return object
*/
function getNextSiblingTag(node)
{
    node = node.nextSibling;
    
    while (node && node.nodeType != 1)
    {
        node = node.nextSibling;
    }

    return node;
}

/*
* Удаляет всех потомков узла node.
* 
* @param object
* @return void
*/
function dom_remove_child(node)
{
    if (node.hasChildNodes())
    {
        for(var i=0; i<node.childNodes.length; i++)
        {
            node.removeChild(node.childNodes[i]);
        }
    }
}