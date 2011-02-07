var CONFIG = new Array();
CONFIG['dir'] = new Array();
CONFIG['dir']['XMLHR'] = '/http/ajax/';
CONFIG['true_images_types'] = new Array('jpg', 'jpeg');
CONFIG['lang'] = 'ru';
CONFIG['messages'] = new Array();
CONFIG['messages']['ru'] = new Array();
CONFIG['messages']['ru']['forms'] = new Array();
CONFIG['messages']['ru']['forms']['exception'] = 'formObj �� �������� �������� �����!';
CONFIG['messages']['ru']['forms']['empty_input_fields'] = '��������� �� ��� ���� �����!';


/*************************************************************
*                     ����������
*************************************************************/
/**
* ���������������� ����������.
* 
* @param string ��������� �� ������
* @return void
*/
function UserException(message)
{
   this.name = '�������������� ��������: ';
   this.message = this.name + ': ' + message;
}

/*************************************************************
* ����������� ��������.
* ����� � kruglov.ru
************************************************************
var isDOM = document.getElementById //DOM1 browser (MSIE 5+, Netscape 6, Opera 5+)
var isOpera = isOpera5 = window.opera && isDOM //Opera 5+
var isOpera6 = isOpera && window.print //Opera 6+
var isOpera7 = isOpera && document.readyState //Opera 7+
var isMSIE = document.all && document.all.item && !isOpera //Microsoft Internet Explorer 4+
var isMSIE5 = isDOM && isMSIE //MSIE 5+
var isNetscape4 = document.layers //Netscape 4.*
var isMozilla = isDOM && navigator.appName == "Netscape" //Mozilla ��� Netscape 6.**/


/*************************************************************
* ������� �������
*************************************************************/

/**
 * ������� ���������� ������, ������� ������������ �����  
 * display-�������� �����.
 * ��� ��� ����������� ������������� ����� ����������:
 * display="" - ��� ������ ������� ����������� �� ���������. 
 * ���� ���� ���� 'block' - ������ 'block', ��� 'none' - ����� 'none',
 * ���� ������ �������� - ����� ������� � ��.
 * ������������ � ��������: hide_show_layer, show_layer

function get_block()
{
    if (document.getElementById)
    {
        return "block";
    }

    return '';
} */


/**
 * ������� ������������� ��� �������/�������� ������.
 * ��������� id ��������, �������� ����� ���������� � flag
 * ���� flag � ������, �� ��� location-������� �� #id_��������.
 * ����������������� �������: get_block

function hide_show_layer(i)
{
    var lnk = document.getElementById(i);
    var status = get_block();
    
    if (lnk.style.display == "none") {
        lnk.style.display = status;
    }
    else if (lnk.style.display == status) {
        lnk.style.display = "none";
    }
} */
	



/*************************************************************
* ������ �������
*************************************************************/

/**
* ������� �������� ��������� �� �������� � ���������� ���.
* ���� ��������� ������ ��������, ���������� FALSE.
* 
* @param void
* @return string|false

function get_selection()
{
    if (document.getSelection)
    {
        return document.getSelection();
    }
    else if (document.selection && document.selection.createRange && document.selection.type == "Text")
    {
        return document.selection.createRange().text;
    }

    return false;
}*/

/**
* ����������� � ����� ������ text
* 
* @param string
* @return boolean

function copyInBuffer(text)
{
    if (window.clipboardData)
    {
        window.clipboardData.setData("Text", text);

        return true;
    }
    
    return false;    
}*/

// �������� ������������ ���� � ������� path � ���������� width � height.
/*
function openWindow(path, width, height)
{
    var l = (screen.availWidth - width)/2;
    var t = (screen.availHeight - height)/2;

    var win = window.open(path, '', 'width='+width+', height='+height+',toolbar=0,menubar=0,scrollbars=1,resizable=1,location=0,status=0,left=' + l + ',top=' + t + '');
    win.focus();
}*/

/**
* ���������� ����� timestamp UNIX
* 
* @param void
* @return int

function getUnixTimestamp()
{
    var now = new Date();
    var str = new String(Date.UTC(now.getUTCFullYear(),
                         now.getUTCMonth(),
                         now.getUTCDate(),
                         now.getUTCHours(),
                         now.getUTCMinutes(),
                         now.getUTCSeconds(),
                         now.getUTCMilliseconds()));
    return str.slice(0, -3);
}*/
/*
function pluralForm(n, form1, form2, form3)
{
    n = Math.abs(n) % 100;
    var n1 = n % 10;
    
    if (n > 10 && n < 20) {
        return form3;
    }
       
    if (n1 > 1 && n1 < 5) {
        return form2;
    }
   
    if (n1 == 1) {
        return form1;
    }
   
    return form3;
}*/


function Dump(d,l) {
    if (l == null) l = 1;
    var s = '';
    if (typeof(d) == "object") {
        s += typeof(d) + " {\n";
        for (var k in d) {
            for (var i=0; i<l; i++) s += "  ";
            s += k+": " + Dump(d[k],l+1);
        }
        for (var i=0; i<l-1; i++) s += "  ";
        s += "}\n"
    } else {
        s += "" + d + "\n";
    }
    return s;
}