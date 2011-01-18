/**
* ������� ���������� ������ ������ �� ���� tagName 
* � ������� className
* 
* @param string tagName ��� ����
* @param string className ��� ������
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
* ���������� firstChild ���� ��� ���� node. 
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
* ���������� previousSibling ���� ��� ���� node. 
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
* ���������� nextSibling ���� ��� ���� node. 
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
* ������� ���� �������� ���� node.
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