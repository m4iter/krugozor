<?
error_reporting(E_ALL);

include('Z:/home/adverts/www/kernel/Db/Mysql/Base.php');
include('Z:/home/adverts/www/kernel/Db/Mysql/Statement.php');
include('Z:/home/adverts/www/kernel/Db/Mysql/Exception.php');
/*
    ini_set('mysql.default_user', 'root');
    ini_set('mysql.default_password', '');
    ini_set('mysql.default_host', 'localhost');
    ini_set('mysql.trace_mode', 0);
    define('MYSQL_DEFAULT_DB', 'adverts');

$db = Db_Mysql_Base::getInstance();
$db->setCurrentDb('adverts');

*/

$string = file_get_contents('http://mamba.ru/mamba.phtml?m=search_place&select=country&countryId=0&regionId=0&cityId=0&lang_id=');

$arr = json_decode($string);


foreach ($arr->list as $ar)
{
  echo iconv('utf-8', 'windows-1251//ignore', $ar->name);
    //$db->query('INSERT INTO country_ VALUES (null, ?i, "?s", "?s")', $ar->oid, iconv('utf-8', 'windows-1251//ignore', $ar->name), '');
}/**/

/*
$string = file_get_contents('http://mamba.ru/mamba.phtml?m=search_place&select=country&countryId=0&regionId=0&cityId=0&lang_id=3');

$arr = json_decode($string);


foreach ($arr->list as $ar)
{
    $db->query('UPDATE country_  SET country_name_en = "?s" WHERE oid = ?i', iconv('utf-8', 'windows-1251//ignore', $ar->name), $ar->oid);
}
*/

/*
$res = $db->query('SELECT * FROM country_ ');
while ($row = $res->fetch_assoc())
{
     $strig = 'http://mamba.ru/mamba.phtml?m=search_place&select=region&countryId='.$row['oid'].'&regionId=0&cityId=0&lang_id=1';

    $arr = json_decode(file_get_contents($strig));
    foreach ($arr->list as $ar)
    {
        $db->query('INSERT INTO region_ VALUES (null, ?i, ?i, "?s", "?s")', $row['id_country'], $ar->oid, iconv('utf-8', 'windows-1251//ignore', $ar->name), '');
    }
    sleep('0.2');
}*/

/*
$res = $db->query('SELECT * FROM country_ ');
while ($row = $res->fetch_assoc())
{
    $strig = 'http://mamba.ru/mamba.phtml?m=search_place&select=region&countryId='.$row['oid'].'&regionId=0&cityId=0&lang_id=3';
    $arr = json_decode(file_get_contents($strig));
    foreach ($arr->list as $ar)
    {
        $db->query('UPDATE  region_ SET region_name_en = "?s" WHERE oid = ?i AND id_country = ?i', iconv('utf-8', 'windows-1251//ignore', $ar->name), $ar->oid, $row['id_country']);
    }
    sleep('0.2');
}*/


/*
$res = $db->query('SELECT country_.id_country, country_.oid AS coid,
                          id_region, region_.oid AS roid 
                   FROM 
                        country_,
                        region_ 
                   WHERE
                        country_.id_country = region_.id_country');

while ($row = $res->fetch_assoc())
{
    $strig = 'http://mamba.ru/mamba.phtml?m=search_place&select=city&countryId='.$row['coid'].'&regionId='.$row['roid'].'&cityId=0&lang_id=1';
    $arr = json_decode(file_get_contents($strig));
    
    foreach ($arr->list as $ar)
    {
        $db->query('INSERT INTO city_ VALUES(NULL, ?i, ?i, ?i, "?s", "?s")', $row['id_region'], $row['id_country'], $ar->oid, iconv('utf-8', 'windows-1251//ignore', $ar->name), '');
    }
    
    sleep('0.2');
}*/

/*
$res = $db->query('SELECT country_.id_country, country_.oid AS coid,
                          id_region, region_.oid AS roid 
                   FROM 
                        country_,
                        region_ 
                   WHERE
                        country_.id_country = region_.id_country');
while ($row = $res->fetch_assoc())
{
    $strig = 'http://mamba.ru/mamba.phtml?m=search_place&select=city&countryId='.$row['coid'].'&regionId='.$row['roid'].'&cityId=0&lang_id=3';
    $arr = json_decode(file_get_contents($strig));
    
    foreach ($arr->list as $ar)
    {
        $db->query('UPDATE  city_ SET city_name_en =  "?s" WHERE id_region = ?i AND id_country= ?i AND oid = ?i', iconv('utf-8', 'windows-1251//ignore', $ar->name), $row['id_region'], $row['id_country'], $ar->oid );
    }
    
    sleep('0.2');
}*/


/*

$res = $db->query('SELECT country_.id_country, country_.oid AS coid, region_.id_region, region_.oid AS roid, city_.id_city, city_.oid AS toid, city_.city_name_ru
FROM country_, region_, city_
WHERE country_.id_country = region_.id_country
AND region_.id_region = city_.id_region
GROUP BY region_.id_region
ORDER BY region_.id_region ASC , city_.id_city ASC');
                       
while ($row = $res->fetch_assoc())
{
    $strig = 'http://mamba.ru/mamba.phtml?m=search_place&_json_=Y&value='.$row['coid'].'_'.$row['roid'].'_'.$row['toid'].'_0&version=2';
    $arr = json_decode(file_get_contents($strig));
    

    
    if (count($arr->d->LS->options) > 1)
    {
        foreach ($arr->d->LS->options as $object)
        {
            list(,,,$moid) = explode('_', $object->value);
            
             $db->query('INSERT INTO metro_ VALUES(NULL, ?i, ?i, ?i, "?s", "?s")', $row['toid'], $row['roid'], $row['coid'], $moid, iconv('utf-8', 'windows-1251//ignore', $object->text), '');
        }
    }
    
    sleep('0.2');
}*/
?>