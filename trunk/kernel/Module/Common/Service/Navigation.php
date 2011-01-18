<?php
/**
 * ������ ��������� ������ ������� �� ������ Request-�����, ����� ���
 * ����������, ������������� � ��.
 * ������ ������������:
 *
 * $navigation = new Module_Group_Service_Navigation($this->getRequest(),
 *                                                   $this->getMapper('Group/Group'),
 *                                                   new Base_Navigation(10, 100));
 * // ��������� ������ �������. ����� getList() ����������� ��� ������� �����������
 * // ������ �������.
 * $items = $navigation->getList();
 *
 * // ������ ���� Base_Navigation ��� ���������� "����������������"
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
     * ��������� ����������.
     * ����� ������� ����� �������������� �������� �������
     * ������� ����������, �������� "... ORDER BY `id` DESC"
     *
     * @var array
     */
    protected $sort_vars = array
    (
        'field_name' => 'id',
        'sort_order' => 'DESC',
    );

    /**
     * ������ ������������, ��� ������ �������� ����� �� Request, � ���������
     * ��� ��������� ������� � �� � ���� `coil_name` ��� `table_name.col_name`.
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

        // ���� �� �����-���� �������� �������� ������ ��� ����������
        // ����������, �� � Request ���������� ��������� ��������� ��-���������.
        $this->declareDefaultSortVarsIfNotDefined();
    }

    /**
     * ���������� ������ �������.
     *
     * @abstract
     * @param void
     * @return mixed
     */
    abstract public function getList();

    /**
     * ���������� ������ "����������" ��� ���������� "����������������".
     *
     * @param void
     * @return object Base_Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * ���������� ��� ���� ������� ��, �� �������� ����� �����������
     * ���������� ������.
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
     * ���������� ������� ����������.
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
     * ���������� � Request ��������� ���������� �� ���������
     * $this->sort_vars � ��� ������, ���� � Request ��� �� ����������.
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