/**
 * ����� ���� � ������� �������� arg � 
 * ���������� ������ ��������, ���� ��� ��� ������������
 * � -1 ���� ��������� �������� � ������� ���.
 * 
 * @param mixed
 * @return int
 */
Array.prototype.in_array = function(arg)
{
	for (var i=0; i < this.length; i++)
    {
		if (this[i] == arg)
        {
		    return i;
		}
	}

	return -1;
}