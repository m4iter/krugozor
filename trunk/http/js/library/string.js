/**
 * Метод возвращает false если строка пуста,
 * т.е. не содержит символов, или не содержит символов,
 * отличных от пробельных.
 * 
 * @param void
 * @return boolean
 */
String.prototype.isEmpty = function()
{
    if (!this.valueOf()) {
        return true;
    }

    for (var j=0; j < this.length; j++)
    {
        if (this.charAt(j)!=" " && this.charAt(j)!="\n" && this.charAt(j)!="\t" && this.charAt(j)!="\r")
        {
            return false;
        }
    }
    
    return true;
}

/**
 * Метод возвращает массив найденных подстрок, 
 * если строка является email-адресом
 * или null, если соответствий нет.
 * Пример: alert( 'ivan.ivanov@my.moscow.info.ru'.isMail(1) )
 * 
 * @param int number_of_element ключ массива результата
 * @return boolean
 */
String.prototype.isMail = function(number_of_element)
{
    var reg = new RegExp("^\s*(([_\.\da-z0-9\-]+)@([\da-z0-9\-.]+)\.([a-z]{2,6}))\s*$", "i");

    var result = this.match(reg);

    if (result)
    {
        return number_of_element ? result[number_of_element] : result;
    }

    return false;
}

/**
 * Возвращает имя файла из строки.
 * Т.е. из строки-пути C:\documents\Документы\Фотографии\Irachka.jpg
 * функция вернёт Irachka.jpg если get_ext установлен в 1 и имя файла
 * без расширения в обратном случае.
 * 
 * @param bolean with_ext возвращать имя файла с расширением
 * @return string
 */
String.prototype.getFileName = function(with_ext)
{
    if (!this.valueOf()) {
        return null;
    }

    var filename = this.substring(this.lastIndexOf("\\")+1, this.length);

    // возвратить вместе с расширением
    if (with_ext) {
        return filename;
    }

    // возвратить без расширения
    return filename.substring(0, filename.lastIndexOf('.'));
}

/**
 * Преобразует первый символ строки в прописной.
 * 
 * @param void
 * @return string
 */
String.prototype.ucfirst = function()
{
    var s1 = this.charAt(0).toUpperCase();
    var s2 =  this.substring(1, this.length);
    return s1 + s2;
}

/**
 * Ищет в строке окончания похожее на один из типов изображений,
 * перечисленных в массиве images_types.
 * Возвращает TRUE, если такое окончание найдено, FALSE при неудаче.
 * 
 * @param array images_types массив допустимых типов расширений
 * @return boolean
 */
String.prototype.hasImageExt = function(images_types)
{
    var type = this.substr(this.lastIndexOf('.')+1).toLowerCase();

    return (images_types.in_array(type) != -1);
}