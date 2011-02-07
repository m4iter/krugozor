function checkForm(formId)
{
    this.error_messages = {
        'empty_input_fields': CONFIG['messages'][CONFIG['lang']]['forms']['empty_input_fields']
    }

    // ���� �����
    this.formObj = document.getElementById(formId);

    // ����, ������� ����� ����� ��������� JavaScript-�� �� �������.
    this.checkedFields = new Array();

    /**
    * ����� ��������� ���� ����� � �������� � ������ ������ �� ���������
    * ������� ���� text, password � textarea.
    * 
    * @param void
    * @return array ������ �� �������� �� ��������� ����
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
    
    // ������ ��� ���� ��������� �������� ����� (input= text, password, textarea).
    this.arrayTextInputs = this.getInput();

    // ��������, ��������� �� �� �� �����.
    if (!this.formObj || typeof this.formObj != 'object')
    {
        throw new UserException(CONFIG['messages'][CONFIG['lang']]['forms']['exception']);
    }
 
    /**
    * ����� ������������� ����� �� ������������� 
    * ��������� ���� �����.
    * 
    * @param void
    * @return void
    */
    this.putFocus = function()
    {
        //    ��������:
        //    ���� ���� ���������, �� �� ��������� � ���������� ����, ���������� � �������
        //        ���� ��� �����, �� ������ ����� � �������.
        //        ����� ���������� ���� � ������� continue.
        //    ���� ���� �� ���������, ������ �����, �������.   
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
    * ����� �������� �� �����.
    * ���� ���� �� ���� ���� ������ (�� �������� ������ ��� �������� ������ � ��. �� word-�������),
    * �� ������� ���������� false.
    * � �������� ���������� ������ ����� ������� ������ ��� �����,
    * �� ������� �������� ������� �� ������ ����������������.
    * 
    * @param void
    * @return boolean
    */
    this.isEmptyInput = function()
    {
        var arglen = arguments.length;
        var args = new Array();

        // �������� ��������� ������� �� ������� arguments.
        for (var i=0; i<arglen; i++) {
            args[i] = arguments[i];
        }

        for (i=0; i<this.arrayTextInputs.length; i++)
        {
            // ���� ��������� ������ ������� � ��� ������� ����������
            // ���� ������������ � ������� ����������, �� ������
            // ��� ���� ��������� �� �����. ����������. 
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
    * ����� ��� �� ������� this.checkedFields � ��������� �������� �����, ��������� � ���� �������.
    * ������ ����������� � �������, � ��������� ����:
    
    objectForm.checkedFields = new Array(
            ["����� ������", "user_name",      "text"],
            ...
            ["����� ������", "user_age_day",   "select-one", [0, "0", ""] ],
            ...
            ["����� ������", "user_sex",       "radio"]
        );
    
    * ��� ������ ���� ���������� - ����� ������, ������� ���������� �������.
    *     ������ ���� - ��� �������� �����, ������� �����������.
    *     ������ ���� - ��� ����.
    *     �������� ���� - ������, ������������ � ��������� ����� ���� select-one.
    *     � ���� ������� ����������� ��������, ��� ���������� ������� �� ���������� select-�� 
    *     �������� ����� ���������� false, �.�. ������� �� ���������� ������.
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
			
            // �� ������ ������ �������� � ������ ������� ���� ����
            f_type = f_type.toLowerCase();
            
            // ���� ������� ����, ����������� � ������� checkedFields ���������� ��� text
            // � ���� � ���� ������ ������� ���������� � �������� ���������, �� ������ ��������.
            if (f_type == "text" && f_name && 
			   (this.formObj[f_name].type == "text"
                || this.formObj[f_name].type == "password"
                || this.formObj[f_name].type == "textarea") )
            {
				
                // ���� ��������� ������� �������� �� noempty
                if (f_val.in_array("noempty") != -1)
                {    // ������ - ���� ������.
                    if (!this.formObj[f_name].value.noempty())
                    {
                        str_err += f_err + "\n";
                        alert(str_err);
                        this.formObj[f_name].focus();
                        return false;
                    }
                }
				
                // ���� ��������� ������� �������� �� is_mail
                if (f_val.in_array("is_mail") != -1)
                {
                    // ������ - ��� �� email.
                    if (!this.formObj[f_name].value.is_mail())
                    {
                        str_err += f_err + "\n";
                        alert(str_err);
                        this.formObj[f_name].focus();
                        return false;
                    }
                }
            }
            // ���� ������� ����, ����������� � ������� checkedFields ���������� ��� select-one
            // � ���� � ���� ������ ������� ���������� � �������� ���������, �� ������ ��������.
            else if (f_type == "select-one" && this.formObj[f_name] != null 
			         && this.formObj[f_name].type == "select-one") 
            {
                // ������ - �� ������� �������� �� ������.
                if (f_val.in_array(this.formObj[f_name].value) != -1) {
                    str_err += f_err + "\n";
                    alert(str_err);
                    this.formObj[this.checkedFields[i][1]].focus();
                    return false;
                }
            }
            // �����-������.
            else if (f_type == "radio")
            {   
                var link = this.formObj[f_name];
                str_err += f_err + "\n";
                
                // ������ - �� ������� �� ���� ������.
                if (!isCheckRadio(link)) {
                    alert(str_err);
                    link.item(0).focus();
                    return false;
                }
            }
        } // end for
        
        return true;
    }
}// ����� ������


/**
* ������� ��������� ������ �� ������ ����������� �
* ���������� true, ���� ����� radio-������ �����������, �.�. 
* �������� �������� �� ������ radio-������, � false � �������� ������.
* ������������ � ������: isEmptyThisFields
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
* ������� ����� � ��������������� id_form:
* ��������� ���� ������ ���� ���������,
* �������� select ���������� � 0-� ������� option,
* � radio � checkbox ��������� ���������.
* 
* @param string id �����
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
* ������� �������� ��� �� �������� ������� �� ���� �����,
* �� ������� ��������� _this (������ �� this ���� �����).
* ���������� �� ������� onkeyup, ��������.
* ������ ���������� �������� ���������, ������� ����� �������� ������������.
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
* ������� �������� ��� ������� �� ����������� a-z,0-9,-,_ �� ���� �����,
* �� ������� ��������� _this (������ �� this ���� �����).
* ���������� �� ������� onkeyup, ��������.
* ������ ���������� �������� ���������, ������� ����� �������� ������������.
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
 * ������� "����������" ��� "��������" ������ �����������
 * ����� ���� � ���� input.
 * 
 * @param string id ������������� ���������� ����
 * @param object _this ������ �� ������� ���������� (������, �����������)
 * @return false ��� �������������� �������� �� �����
 */
function star2chars(id, _this)
{
    var id = document.getElementById(id);
    id.type  = id.type == 'password' ? 'text' : 'password';
    _this.firstChild.nodeValue = id.type == 'password' ? '������� ������' : '������ ������';
    return false;
}