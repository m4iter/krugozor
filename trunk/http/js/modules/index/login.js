// ������� ��������� ������ obj �� ������ ���� �. 
// ���������� ����� prompt, ������� �������� ��������� ��������. 
// ���� �������� �������� ������ � ����� � ��������� �� 1 �� 365, 
// �� ��� �������� ���������� ��� ����� ���� �, � ��� �� ����� ��� ��������
// � �������� value ������� hidden-���� 
function changeCookieDays(obj, hiddenAutologinFiel)
{   
    var str = 0;
    if (str = prompt('�������, �� ����� ���������� ���� ���������� ��������� ������ �� ���� ����������', ''))
    {
        if (isNaN(str)) {
            return false;
        }
        else if (Math.round(str) > 365 || Math.round(str) <= 0) {
            return false;
        }
        else {
            obj.firstChild.nodeValue = Math.round(str);
            document.getElementById(hiddenAutologinFiel).value = Math.round(str);
        }
    }
}

// ��������� ���������� � ����� �����.
function enabledAutologin(_this, id)
{
	var changeCookieDaysBlock = document.getElementById(id);
	
	if (_this.checked)
	{
		changeCookieDaysBlock.style.display = 'block';
	}
	else
	{
		changeCookieDaysBlock.style.display = 'none';
	}
}