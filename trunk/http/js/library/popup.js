/*
¬ызываетс€ так:

1 вариант - динамическа€ загрузка изображений при загрузке страницы

¬ html пишем: 
<a class="autoload" onclick="popup.show_load_image(this.href); return false;" href="...">...

<script type="text/javascript">
var popup = new popupImage();
popup.getElementsByClassName('a', 'autoload');
popup.loadHiddenImages();
</script>

2 вариант - загрузка изображений непосредственно при клике по ссылке

¬ html пишем: 
<a onclick="popup.show_size_image(this.href, 232, 435); return false;" href="...">...

<script type="text/javascript">
var popup = new popupImage();
</script>
*/

function popupImage_close(_this)
{
    _this.parentNode.style.display = 'none';
}

function popupImage()
{
    // ћассив, который в дальнейшем будет заполнен 
    // ссылками на теги a с классом равным loadImage
    var links = new Array();
    
    // ћассив, который в дальнейшем будет заполнен  
    // пут€ми до изображений.
    var image_array = new Array();
    
    // путь до нестандартного курсора
    this.cursor_url = '/http/images/system/cursors/error.ico';
    
    // ID всплывающего блока
    this.divId = 'popUpBlock_123';
    
    this.images_objects_no_size = new Array();
    
    // «аполн€ет массив this.links ссылками на тэги tagName 
    // с классом className
    this.getElementsByClassName = function(tagName, className)
    {
        var allImageAnhors = document.getElementsByTagName(tagName);
        var j = 0;
    
        for (var i=0; i<allImageAnhors.length; i++)
        {
    	    var tClasName = allImageAnhors.item(i).className;
            
            if (tClasName == className || tClasName.indexOf(className) != -1)
            {
                links[j++] = allImageAnhors.item(i);
            }
        }
    }

    // *подгружает* изображени€ и формирует глобальный массив 
    // с пут€ми к этим изображени€м.
    this.loadHiddenImages = function()
    {
        var j = 0;
        for (i=0; i<links.length; i++) 
        {
            var tsrc = links[i].getAttribute('href');
    		
    		// ѕротокол и хост
    		var protocol_host = location.protocol + "//" + location.host;
    		
    		// ѕоскольку Mozilla возвращает значение атрибута href без протокола и хоста
    		// то в случае их отсутстви€, приписываем их к адресу изображени€.
    		if (tsrc.indexOf(protocol_host) == -1)
            {
    			tsrc = protocol_host + tsrc;
    		}
    		
            eval("loadImage_" + i + " = new Image();\
            loadImage_" + i + ".src = '" + tsrc + "'");
            image_array[j++] = tsrc;
        }
    }

    /**
    * ѕоказывает картинку, загружа€ еЄ во врем€ вызова.
    *
    * @param string src
    * @param int width не об€зательный параметр
    * @param int height не об€зательный параметр
    * @return void
    */
    this.show_size_image = function(src, width, height)
    {
        // если не указаны размеры изображени€.
        if (!width && !height)
        {
            // если в реестре уже загруженных изображений нет объекта этого изображени€,
            // то добавл€ем, предварительно загрузив изображение.
            if (!this.images_objects_no_size[src])
            {
                var img = new Image();
                img.src = src + (isMSIE ? '?' + Math.round(Math.random(1,10000)) : '');
                img.onload = function() {
                    new popupImage().show_image(img.src, img.width, img.height);
                }
                this.images_objects_no_size[src] = img;
            }
            // в реестре агруженных изображений присутствует изображение с таким URL - 
            // просото получаем width и height
            else
            {
                width = this.images_objects_no_size[src].width;
                height = this.images_objects_no_size[src].height;

                this.show_image(src, width, height);
            }
        }
        else
        {
            this.show_image(src, width, height);
        }
    }

    // ѕоказывает уже загруженную картинку по URL src
    this.show_load_image = function(src)
    {
        // находим в массиве путей изображений пор€дковый номер изображени€ с путЄм src
        var index = image_array.in_array(src);
        
        if (index == -1) {
            return false;
        }
        
    	// получаем ссылку на объект изображени€
        var lnk = eval("loadImage_" + index + ";");
        this.show_image(lnk.src, lnk.width, lnk.height);
    }

    // ‘ормирует всплывающий блок и показывет изображение
    this.show_image = function(src, width, height)
    {
    	// заполнение вспывающего блока
        var padding = 16;

  
    	// размеры окна
    	var documentClientHeight = document.documentElement.clientHeight;
    	var documentClientWidth = document.documentElement.clientWidth;
        
    	if (isOpera7) {
    		var documentClientHeight = document.body.clientHeight;
    		var documentClientWidth = document.body.clientWidth;
    	}

    	var left = Math.floor((documentClientWidth/2) - (width/2)) + getScrollLeft() - padding;
    	var top = Math.floor((documentClientHeight/2) - (height/2)) + getScrollTop() - padding;
        
        var id;
        
        if (!(id = document.getElementById(this.divId)))
        {
            id = document.createElement("DIV");
            id.setAttribute('id', this.divId);
        }
    	else
        {
            id.removeChild(id.firstChild);
        }
        
        id.style.position = "absolute";
    	id.style.width = width + 'px';
    	id.style.height = height + 'px';
    	id.style.top = top + 'px';
    	id.style.left = left + 'px';
    	id.style.display = "block";
    	id.style.zindex = "999";
        id.style.padding = '10px';
        id.style.border = '1px solid #333';
        id.style.backgroundColor = '#fff';
        
        var img = document.createElement('IMG');
        img.setAttribute('src', src);
        img.setAttribute('title', '«акрыть окно');
        
        // ¬ IE глюки с курсорами
        if (isMozilla)
        {
            img.style.cursor = 'url(\'' + this.cursor_url + '\'), default';
        }

        if (isMSIE && !navigator.appVersion.match('MSIE 8.0'))
        {
            id.setAttribute('onclick', function() {popupImage_close(this.firstChild)});
            img.setAttribute('onclick', function() {popupImage_close(this)});
        }
        else
        {
            id.setAttribute('onclick', 'popupImage_close(this.firstChild)');
            img.setAttribute('onclick', 'popupImage_close(this)');
            
        }
        
        id.appendChild(img);
        document.body.appendChild(id);
    }
}