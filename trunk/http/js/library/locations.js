/**
 * ������ HTTP ������ � ������� select � ���������.
 * 
 * @param what ���� �� ��� ��������� (������������� � ������� checked_user_locations)
 * ������������, ��� ����������� - ������, ������� ��� ������.
 * @param id - ������������� ������, ���� ������������� �������; 
 *             ������������� �������, ���� ������������� ������.
 * @return void
 */
function locations_create(what, id)
{
    var ajax = new Ajax();
    ajax.setObserverState(function(ajx){locations_make_select(ajx.getJson2HashByKey(), what)});
    ajax.get("/ajax/" + what + "/?id=" + id, true);
}

/**
 * ���������� select elemId
 * 
 * @param elemId ID ��������
 * @return void
 */
function locations_visibility(elemId)
{
	document.getElementById(elemId).style.display = 'inline';
}

/**
 * �������� select elemId
 * 
 * @param elemId ID ��������
 * @return void
 */
function locations_hidden(elemId)
{
	document.getElementById(elemId).style.display = 'none';
}

/**
 * �������, ��������� ��������� ������ select ��� ��������� ��� ����������
 * 
 * @param ��������� �� select
 * @return void
 */
function locations_next_operation(_this)
{
    if (_this.value > 0)
    {
        var loc = locations_chain[locations_chain.in_array(_this.id)+1];
        
        if (loc)
        {
            locations_create(loc, _this.value);
           
            locations_visibility(loc);
        }

        for (i=locations_chain.in_array(_this.id)+1; i<locations_chain.length-1; i++)
        {
            locations_create(locations_chain[i+1], 0);
        }
    }
    else
    {
        for (var i=locations_chain.length-1; i>locations_chain.in_array(_this.id); i--)
        {
            locations_create(locations_chain[i], 0);
            
            locations_hidden(locations_chain[i]);
        }
    }
}

/**
 * ��������� select.
 * 
 * @param array arr ������ ���� ID_�����������_������ => ���_�����������_������
 * @param string ������������� select-a, ������� ���������� ��������� option-s
 * @return void
 */
function locations_make_select(arr, id_select)
{
    var lnk = document.getElementById(id_select);
    
    // ��������� ����� ������� option
    var option_text = lnk.firstChild.firstChild.nodeValue;
    // ��������� ����� ������� option
    var option_style = lnk.firstChild.style;	
    
    // ������� ��� option-� ������� ���� �� �����    
    while (lnk.firstChild)
    {
        lnk.removeChild(lnk.firstChild);
    }

    var opt = document.createElement('OPTION');
    opt.appendChild(document.createTextNode(option_text));
    opt.setAttribute('value', '0');
        
    // ��������� �����, ������� ���� ������������ ��� �����
    // optiona-a ����������
    for (var i=0; i<option_style.length; i++) 
    {    	
     	opt.style.setProperty(option_style.item(i), option_style.getPropertyValue(option_style.item(i)), '');
    } 

    lnk.appendChild(opt);

    if (arr)
    {
        for (j in arr)
        {
            if (typeof arr[j] == 'function') continue;
            
            var opt = document.createElement('OPTION');
            opt.appendChild(document.createTextNode(arr[j]));
            opt.setAttribute('value', j);

            if (checked_user_locations[id_select] == j)
            {
                opt.setAttribute('selected', 'selected');
            }

            lnk.appendChild(opt);
        }
    }
}