// Функция принимает ссылку obj на объект тега А. 
// Вызывается метод prompt, который получает некоторое значение. 
// Если значение является числом и лежит в диапазоне от 1 до 365, 
// то это значение записываем как якорь тега А, а так же пишем это значение
// в значение value объекта hidden-поля 
function changeCookieDays(obj, hiddenAutologinFiel)
{   
    var str = 0;
    if (str = prompt('Введите, на какое количество дней необходимо запомнить пароль на этом компьютере', ''))
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

// Включение автологина и показ формы.
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