function checkForm(formId)
{
    this.error_messages = {
        'empty_input_fields': CONFIG['messages'][CONFIG['lang']]['forms']['empty_input_fields']
    }

    // Сама форма
    this.formObj = document.getElementById(formId);

    // Поля, которые нужно будет проверять JavaScript-ом на клиенте.
    this.checkedFields = new Array();

    /**
    * Метод сканирует поля формы и помещает в массив ссылки на текстовые
    * области типа text, password и textarea.
    * 
    * @param void
    * @return array массив со ссылками на текстовые поля
    */
    this.getInput = function()
    {
        var my_array = new Array();
        var j=0;

        for (var i=0; i < this.formObj.elements.length; i++)
        {
            var type = this.formObj.elements[i].type;
            
            if (type)
            {
                type = type.toLowerCase();
                
                if (type == "text" || type == "password" || type == "textarea")
                {
                    my_array[j++] = this.formObj.elements[i];
                }
            }
        }
        
        return my_array;
    }
    
    // Массив имён всех текстовых областей формы (input= text, password, textarea).
    this.arrayTextInputs = this.getInput();

    // Проверка, ссылаемся ли мы на форму.
    if (!this.formObj || typeof this.formObj != 'object')
    {
        throw new UserException(CONFIG['messages'][CONFIG['lang']]['forms']['exception']);
    }
 
    /**
    * Метод устанавливает фокус на незаполненные 
    * текстовые поля формы.
    * 
    * @param void
    * @return void
    */
    this.putFocus = function()
    {
        //    АЛГОРИТМ:
        //    Если поле заполнено, то мы переходим к следующему полю, указанному в массиве
        //        Если оно пусто, то ставим фокус и выходим.
        //        Иначе продолжаем цикл с помощью continue.
        //    Если поле не заполнено, ставим фокус, выходим.   
        for (var i=0; i<this.arrayTextInputs.length; i++)
        {
            if (this.arrayTextInputs[i].value != "")
            {
                if (this.arrayTextInputs[i+1] && this.arrayTextInputs[i+1].value != "") {
                    continue;
                }
                else if(this.arrayTextInputs[i+1]){
                    //if(document.getElementById(form_id).elements[i+1].offsetHeight != 0){
                    this.arrayTextInputs[i+1].focus();
                    //}
                    break;
                }
            }
            else
            {
                //if(document.getElementById(form_id).elements[i].offsetHeight != 0){
                this.arrayTextInputs[i].focus();
                //wasFocus = true;
                //}
                break;
            }
        }
    }

    /**
    * Метод проходит по форме.
    * Если хотя бы одно поле пустое (не содержит данных или содержит проблы и пр. не word-символы),
    * то функция возвращает false.
    * В качестве аргументов метода можно указать список имён полей,
    * на которых действие функции не должны распростроняться.
    * 
    * @param void
    * @return boolean
    */
    this.isEmptyInput = function()
    {
        var arglen = arguments.length;
        var args = new Array();

        // Получаем аргументы функции из объекта arguments.
        for (var i=0; i<arglen; i++) {
            args[i] = arguments[i];
        }

        for (i=0; i<this.arrayTextInputs.length; i++)
        {
            // Если аргументы метода имеются и имя данного текстового
            // поля присутствует в массиве аргументов, то значит
            // это поле проверять не нужно. Пропускаем. 
            if (arglen && args.in_array(this.arrayTextInputs[i].name) != -1) {
                continue;
            }

            var str = this.arrayTextInputs[i].value;
            var len = str.length;
            var err = null;

            if (len == 0) {
                err = i;
                break;
            } else {
                if (str.isEmpty()) {
                    err = i;
                    break;
                }
            }
        }

        if (err != null)
        {
            alert(this.error_messages['empty_input_fields']);
            this.arrayTextInputs[err].focus();
            return false;
        }

        return true;
    }

    /*
    * Метод идёт по массиву this.checkedFields и проверяет значения полей, указанных в этом массиве.
    * Массив объявляется в шаблоне, в следующем виде:
    
    objectForm.checkedFields = new Array(
            ["текст ошибки", "user_name",      "text"],
            ...
            ["текст ошибки", "user_age_day",   "select-one", [0, "0", ""] ],
            ...
            ["текст ошибки", "user_sex",       "radio"]
        );
    
    * где Первое поле подмассива - текст ошибки, которую необходимо вывести.
    *     Второе поле - имя элемента формы, которое проверяется.
    *     Третье поле - тип поля.
    *     Четвёртое поле - массив, присутствует у элементов формы типа select-one.
    *     В этом массиве перечислены значения, при совпадении которых со значениями select-ов 
    *     проверка будет возвращать false, т.е. признак не выбранного списка.
    */
    this.isEmptyThisFields = function()
    {
        for (var i=0; i<this.checkedFields.length; i++) 
        {
            var str_err = '';
            
			var f_err = this.checkedFields[i][0];
			var f_name = this.checkedFields[i][1];
			var f_type = this.checkedFields[i][2];
			var f_val = this.checkedFields[i][3];
			
			if (!this.formObj[f_name])
			{
                continue;
            }
			
            // на всякий случай обращаем в нижний регистр типа поля
            f_type = f_type.toLowerCase();
            
            // Если текущее поле, определённое в массиве checkedFields определено как text
            // и поле с такм именем реально существует и является текстовым, то делаем проверку.
            if (f_type == "text" && f_name && 
			   (this.formObj[f_name].type == "text"
                || this.formObj[f_name].type == "password"
                || this.formObj[f_name].type == "textarea") )
            {
				
                // Если требуется сделать проверку на noempty
                if (f_val.in_array("noempty") != -1)
                {    // Ошибка - поле пустое.
                    if (!this.formObj[f_name].value.noempty())
                    {
                        str_err += f_err + "\n";
                        alert(str_err);
                        this.formObj[f_name].focus();
                        return false;
                    }
                }
				
                // Если требуется сделать проверку на is_mail
                if (f_val.in_array("is_mail") != -1)
                {
                    // Ошибка - это не email.
                    if (!this.formObj[f_name].value.is_mail())
                    {
                        str_err += f_err + "\n";
                        alert(str_err);
                        this.formObj[f_name].focus();
                        return false;
                    }
                }
            }
            // Если текущее поле, определённое в массиве checkedFields определено как select-one
            // и поле с такм именем реально существует и является текстовым, то делаем проверку.
            else if (f_type == "select-one" && this.formObj[f_name] != null 
			         && this.formObj[f_name].type == "select-one") 
            {
                // Ошибка - не выбрано значение из списка.
                if (f_val.in_array(this.formObj[f_name].value) != -1) {
                    str_err += f_err + "\n";
                    alert(str_err);
                    this.formObj[this.checkedFields[i][1]].focus();
                    return false;
                }
            }
            // Радио-кнопки.
            else if (f_type == "radio")
            {   
                var link = this.formObj[f_name];
                str_err += f_err + "\n";
                
                // Ошибка - не выбрана ни одна кнопка.
                if (!isCheckRadio(link)) {
                    alert(str_err);
                    link.item(0).focus();
                    return false;
                }
            }
        } // end for
        
        return true;
    }
}// конец класса


/**
* Функция принимает ссылку на наборв радиокнопок и
* возвращает true, если набор radio-кнопок активирован, т.е. 
* отмечено значение из набора radio-кнопок, и false в обратном случае.
* Используется в методе: isEmptyThisFields
* 
* @param id collection 
* @return boolean
*/
function isCheckRadio(id)
{
    for (var i=0; i<id.length; i++) {
        if (id.item(i).checked) {
            return true;
        }
    }

    return false;
}

/**
* Очищает форму с идентификатором id_form:
* текстовые поля любого рода очищаются,
* значение select становится в 0-й элемент option,
* с radio и checkbox снимается выделение.
* 
* @param string id формы
* @return void
*/
function clear_form(id_form)
{
    var id_form = document.getElementById(id_form);
    
    for (i=0; i<id_form.elements.length; i++)
    {
        var lnk = id_form.elements.item(i);
        
        switch(lnk.tagName.toUpperCase())
        {
            case 'INPUT':
                if (lnk.type.toLowerCase() == 'text' 
                    || lnk.type.toLowerCase() == 'password'
                    || lnk.type.toLowerCase() == 'file')
                {
                    lnk.value = '';
                }
                else if (lnk.type.toLowerCase() == 'checkbox')
                {
                    lnk.checked = false;
                }
                else if (lnk.type.toLowerCase() == 'radio')
                {
                    lnk.checked = false;
                }
            break;
            
            case 'SELECT':
                if(lnk.multiple)
                {
                    lnk.selectedIndex = -1;
                }
                else
                {
                    lnk.selectedIndex = 0;
                }
            break;
            
            case 'TEXTAREA':
                lnk.value = '';
            break;
        }
    }
}

/**
* Функция вырезает все не числовые символы из поля ввода,
* на который указывает _this (ссылка на this поля ввода).
* Вызывается по событию onkeyup, например.
* Вторым параметром является сообщение, которое будет показано пользователю.
* 
* @param object 
* @return void
*/
function filterFieldDigit(_this)
{
	var newstr = '';
	var str = _this.value;
	var len = _this.value.length;
	var k = 0;

	for (var i = 0; i<len; i++)
	{
		var chr = str.substring(i, i+1);

		if (/[0-9]/.test(chr))
		{	
			newstr = newstr + chr;
		}
		else
		{
			if (!k)
			{
				k = 1;
			}
		}
	}

	_this.value = newstr;
	_this.focus();
}


/**
* Функция вырезает все символы за исключением a-z,0-9,-,_ из поля ввода,
* на который указывает _this (ссылка на this поля ввода).
* Вызывается по событию onkeyup, например.
* Вторым параметром является сообщение, которое будет показано пользователю.
* 
* @param object 
* @return void
*/
function filterFieldAlnum(_this, _alert)
{
	var newstr = '';
	var str = _this.value;
	var len = _this.value.length;
	var k = 0;

	for (var i = 0; i<len; i++)
	{
		var chr = str.substring(i, i+1);

		if (/[0-9a-z_\-]/i.test(chr))
		{	
			newstr = newstr + chr;
		}
		else
		{
			if (!k)
			{
				k = 1;
			}
		}
	}

	_this.value = newstr;
	_this.focus();
}

/**
 * Функция "раскрывает" или "скрывает" пароли посредством
 * смены типа у поля input.
 * 
 * @param string id идентификатор текстового поля
 * @param object _this ссылка на элемент управления (обычно, гиперссылка)
 * @return false для предотвращения перехода по якорю
 */
function star2chars(id, _this)
{
    var id = document.getElementById(id);
    id.type  = id.type == 'password' ? 'text' : 'password';
    _this.firstChild.nodeValue = id.type == 'password' ? 'открыть пароль' : 'скрыть пароль';
    return false;
}