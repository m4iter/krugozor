<?php
/**
 * ������-���������, ���������� ���������������� mapper-�������.
 */
class Mapper_Manager
{
    /**
     * ��������� ���������������� ��������.
     *
     * @var array
     * @static
     */
    private static $mappers = array();

    /**
     * @var Db_Mysql_Base
     */
    private $db;

    public function __construct(Db_Mysql_Base $db)
    {
        $this->db = $db;
    }

    /**
     * ����� ��������� ������ ���� `ModuleName/ModelMapperName`,
     * � ���������� ������ �������, ��������� ������
     * Module_ModuleName_Mapper_ModelMapperName
     *
     * @param string
     * @return Mapper_Abstract
     */
    public function getMapper($path)
    {
        list($module, $model) = explode('/', $path);

        if (isset(self::$mappers[$module][$model]))
        {
            return self::$mappers[$module][$model];
        }

        $mapper_path = 'Module_' . $module . '_Mapper_' . $model;

        if (class_exists($mapper_path))
        {
            return self::$mappers[$module][$model] = new $mapper_path($this->db, $this);
        }
    }
}