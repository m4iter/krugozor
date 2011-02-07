/**
 * Делает HTTP запрос и создает select в документе.
 * 
 * @param what один из тех элементов (перечисленных в массиве checked_user_locations)
 * определяющем, что запрашивать - страны, регионы или города.
 * @param id - идентификатор страны, если запрашиваются регионы; 
 *             идентификатор региона, если запрашиваются города.
 * @return void
 */
function locations_create(what, id)
{
    var ajax = new Ajax();
    ajax.setObserverState(function(ajx){locations_make_select(ajx.getJson2HashByKey(), what)});
    ajax.get("/ajax/" + what + "/?id=" + id, true);
}

/**
 * Показывает select elemId
 * 
 * @param elemId ID элемента
 * @return void
 */
function locations_visibility(elemId)
{
	document.getElementById(elemId).style.display = 'inline';
}

/**
 * Скрывает select elemId
 * 
 * @param elemId ID элемента
 * @return void
 */
function locations_hidden(elemId)
{
	document.getElementById(elemId).style.display = 'none';
}

/**
 * Функция, создающая следующий списко select или убирающая все предыдущие
 * 
 * @param указатель на select
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
 * Формирует select.
 * 
 * @param array arr массив вида ID_населенного_пункта => имя_населенного_пункта
 * @param string идентификатор select-a, который необходимо наполнять option-s
 * @return void
 */
function locations_make_select(arr, id_select)
{
    var lnk = document.getElementById(id_select);
    
    // Сохраняем текст первого option
    var option_text = lnk.firstChild.firstChild.nodeValue;
    // Сохраняем стиль первого option
    var option_style = lnk.firstChild.style;	
    
    // Убираем все option-ы которые были до этого    
    while (lnk.firstChild)
    {
        lnk.removeChild(lnk.firstChild);
    }

    var opt = document.createElement('OPTION');
    opt.appendChild(document.createTextNode(option_text));
    opt.setAttribute('value', '0');
        
    // добавляем стили, которые были использованы для этого
    // optiona-a изначально
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