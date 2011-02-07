/**
 * ����� ���������� false ���� ������ �����,
 * �.�. �� �������� ��������, ��� �� �������� ��������,
 * �������� �� ����������.
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
 * ����� ���������� ������ ��������� ��������, 
 * ���� ������ �������� email-�������
 * ��� null, ���� ������������ ���.
 * ������: alert( 'ivan.ivanov@my.moscow.info.ru'.isMail(1) )
 * 
 * @param int number_of_element ���� ������� ����������
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
 * ���������� ��� ����� �� ������.
 * �.�. �� ������-���� C:\documents\���������\����������\Irachka.jpg
 * ������� ����� Irachka.jpg ���� get_ext ���������� � 1 � ��� �����
 * ��� ���������� � �������� ������.
 * 
 * @param bolean with_ext ���������� ��� ����� � �����������
 * @return string
 */
String.prototype.getFileName = function(with_ext)
{
    if (!this.valueOf()) {
        return null;
    }

    var filename = this.substring(this.lastIndexOf("\\")+1, this.length);

    // ���������� ������ � �����������
    if (with_ext) {
        return filename;
    }

    // ���������� ��� ����������
    return filename.substring(0, filename.lastIndexOf('.'));
}

/**
 * ����������� ������ ������ ������ � ���������.
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
 * ���� � ������ ��������� ������� �� ���� �� ����� �����������,
 * ������������� � ������� images_types.
 * ���������� TRUE, ���� ����� ��������� �������, FALSE ��� �������.
 * 
 * @param array images_types ������ ���������� ����� ����������
 * @return boolean
 */
String.prototype.hasImageExt = function(images_types)
{
    var type = this.substr(this.lastIndexOf('.')+1).toLowerCase();

    return (images_types.in_array(type) != -1);
}