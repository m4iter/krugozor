/**
 * Метод ищет в массиве значение arg и 
 * возвращает индекс элемента, если оно там присутствует
 * и -1 если заданного значения в массиве нет.
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