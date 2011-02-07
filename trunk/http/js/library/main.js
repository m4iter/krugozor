var CONFIG = new Array();
CONFIG['dir'] = new Array();
CONFIG['dir']['XMLHR'] = '/http/ajax/';
CONFIG['true_images_types'] = new Array('jpg', 'jpeg');
CONFIG['lang'] = 'ru';
CONFIG['messages'] = new Array();
CONFIG['messages']['ru'] = new Array();
CONFIG['messages']['ru']['forms'] = new Array();
CONFIG['messages']['ru']['forms']['exception'] = 'formObj не является объектом формы!';
CONFIG['messages']['ru']['forms']['empty_input_fields'] = 'Заполнены не все поля формы!';


/*************************************************************
*                     ИСКЛЮЧЕНИЯ
*************************************************************/
/**
* Пользовательское исключение.
* 
* @param string сообщение об ошибке
* @return void
*/
function UserException(message)
{
   this.name = 'Исключительная ситуация: ';
   this.message = this.name + ': ' + message;
}

/*************************************************************
* Определение броузера.
* Взято с kruglov.ru
************************************************************
var isDOM = document.getElementById //DOM1 browser (MSIE 5+, Netscape 6, Opera 5+)
var isOpera = isOpera5 = window.opera && isDOM //Opera 5+
var isOpera6 = isOpera && window.print //Opera 6+
var isOpera7 = isOpera && document.readyState //Opera 7+
var isMSIE = document.all && document.all.item && !isOpera //Microsoft Internet Explorer 4+
var isMSIE5 = isDOM && isMSIE //MSIE 5+
var isNetscape4 = document.layers //Netscape 4.*
var isMozilla = isDOM && navigator.appName == "Netscape" //Mozilla или Netscape 6.**/


/*************************************************************
* Рабочая область
*************************************************************/

/**
 * Функция возвращает строку, которая представляет собой  
 * display-описание блока.
 * Это для обеспечения совместимости между броузерами:
 * display="" - это значит сделать отображение по умолчанию. 
 * Если блок типа 'block' - станет 'block', был 'none' - будет 'none',
 * была ячейка таблички - будет ячейкой и пр.
 * ИСПОЛЬЗУЕТСЯ В ФУНКЦИЯХ: hide_show_layer, show_layer

function get_block()
{
    if (document.getElementById)
    {
        return "block";
    }

    return '';
} */


/**
 * Функция предназначена для скрытия/открытия блоков.
 * Принимает id элемента, которого нужно обработать и flag
 * Если flag в истине, то идёт location-переход на #id_элемента.
 * ВЗАИМОДЕЙСТВУЮЩАЯ ФУНКЦИЯ: get_block

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
* Прочие функции
*************************************************************/

/**
* Функция получает выделение со страницы и возвращает его.
* Если выделение прошло неудачно, возвращает FALSE.
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
* Копирование в буфер текста text
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

// Открытие всплывающего окна с адресом path и размеракми width и height.
/*
function openWindow(path, width, height)
{
    var l = (screen.availWidth - width)/2;
    var t = (screen.availHeight - height)/2;

    var win = window.open(path, '', 'width='+width+', height='+height+',toolbar=0,menubar=0,scrollbars=1,resizable=1,location=0,status=0,left=' + l + ',top=' + t + '');
    win.focus();
}*/

/**
* Возвращает метку timestamp UNIX
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