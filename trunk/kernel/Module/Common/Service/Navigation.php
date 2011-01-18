<?php
/**
 * Сервис получения списка записей на основе Request-даных, таких как
 * сортировка, лимитирование и пр.
 * Пример испольования:
 *
 * $navigation = new Module_Group_Service_Navigation($this->getRequest(),
 *                                                   $this->getMapper('Group/Group'),
 *                                                   new Base_Navigation(10, 100));
 * // Получение списка записей. Метод getList() реализуется для каждого конкретного
 * // класса сервиса.
 * $items = $navigation->getList();
 *
 * // Объект типа Base_Navigation для построения "отстраничивателя"
 * $navigation = $navigation->getNavigation();
 */
abstract class Module_Common_Service_Navigation
{
    /**
     * @var object Http_Request
     */
    protected $request;

    /**
     * @var Base_Mapper
     */
    protected $mapper;

    /**
     * @var Base_Navigation
     */
    protected $navigation;

    /**
     * Параметры сортировки.
     * Вывод записей будет осуществляться согласно данному
     * массиву параметров, например "... ORDER BY `id` DESC"
     *
     * @var array
     */
    protected $sort_vars = array
    (
        'field_name' => 'id',
        'sort_order' => 'DESC',
    );

    /**
     * Массив перечисления, где ключом является алиас из Request, а значением
     * имя реального столбца в БД в виде `coil_name` или `table_name.col_name`.
     *
     * @var array
     */
    protected $sort_cols_values = array('id' => 'id');

    /**
     * @param object Http_Request $request
     * @param object Base_Mapper $mapper
     * @param object Base_Navigation $navigation
     */
    public function __construct(Http_Request $request, Base_Mapper $mapper, Base_Navigation $navigation)
    {
        $this->request    = $request;
        $this->mapper     = $mapper;
        $this->navigation = $navigation;

        // Если по каким-либо причинам сценарий вызван без параметров
        // сортировки, то в Request записываем параметры сортироки по-умолчанию.
        $this->declareDefaultSortVarsIfNotDefined();
    }

    /**
     * Возвращает список записей.
     *
     * @abstract
     * @param void
     * @return mixed
     */
    abstract public function getList();

    /**
     * Возвращает объект "Навигатора" для построения "отстраничивателя".
     *
     * @param void
     * @return object Base_Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Возвращает имя поля таблицы БД, по которому будет происходить
     * сортировка списка.
     *
     * @param void
     * @return string
     */
    protected function getRealSortFieldName()
    {
        if (isset($this->sort_cols_values[$this->request->getRequest('field_name')]))
        {
            return $this->sort_cols_values[$this->request->getRequest('field_name')];
        }
        else
        {
            return $this->sort_vars['field_name'];
        }
    }

    /**
     * Возвращает порядок сортировки.
     *
     * @param void
     * @return string
     */
    protected function getRealSortOrder()
    {
        switch ($this->request->getRequest('sort_order'))
        {
            case 'ASC':
                return 'ASC';

            case 'DESC':
            default:
                return 'DESC';
        }
    }

    /**
     * Записывает в Request параметры сортировки по умолчанию
     * $this->sort_vars в том случае, если в Request они не определены.
     *
     * @param void
     * @return void
     */
    private function declareDefaultSortVarsIfNotDefined()
    {
        foreach ($this->sort_vars as $key => $value)
        {
            if (!isset($this->request->getRequest()->$key))
            {
               $this->request->getRequest()->$key = $value;
            }
        }
    }
}
?>