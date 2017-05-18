<?php

/**
 * 安全地获取路由参数
 *
 * @param null $key
 *
 * @return null
 */
function get_query($key = null)
{
    if ($key) {

        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    return $_GET;
}


/**
 * 安全地获取数组指定值
 *
 * @param array $array
 * @param mixed $key
 * @param null $default
 *
 * @return mixed|null
 */
function array_value(array $array, $key, $default = null)
{
    return isset($array[$key]) ? $array[$key] : $default;
}


/**
 * 生成唯一标识符
 *
 * @param string $identify
 *
 * @return string
 */
function id($identify = '')
{
    return md5(sha1(uniqid($identify . mt_rand(1, 1000000) . time())));
}

/**
 * 数据验证函数
 *
 * @param array $data 验证数据
 * @param array $rule 验证规则
 * @param array $message 自定制响应消息
 *
 */
function validate(array $data, array $rule, array $message = [])
{
    $validator = \Illuminate\Support\Facades\Validator::make($data, $rule, $message);

    if ($validator->fails()) {

        exception(1000, $validator->errors());
    }

}

/**
 * 抛出一个异常状态
 *
 * @param int $code
 * @param mixed $data
 *
 * @throws \Modules\Core\Exceptions\StatusException
 */
function exception($code, $data = null)
{
    throw new \Modules\Core\Exceptions\StatusException($code, $data);
}


/**
 * 获取资源的数据表对象
 *
 * @param null $name 指定表明
 *
 * @return \Illuminate\Support\Facades\DB
 */
function table($name)
{
    return \Illuminate\Support\Facades\DB::table($name);
}


/**
 * 连接其他数据库服务器
 *
 * @param string $name 配置的名称
 *
 * @return \Illuminate\Support\Facades\DB
 */
function connection($name)
{
    return \Illuminate\Support\Facades\DB::connection($name);
}


/**
 * 添加数据库连接
 *
 * 如果两个连接命名相同,且配置不同,则会抛出异常。
 *
 * @param string $name
 * @param array $value
 * @throws Exception
 */
function add_connection($name, array $value)
{

    $connections = config('database.connections');

    if (array_value($connections, $name) === null) {

        \Illuminate\Support\Facades\Config::set('database.connections.' . $name, $value);

    } else {

        if (count(array_diff($connections[$name], $value)) > 0) {

            throw new \Exception("Database connection \"$name\" has exists.");
        }

    }

}


/**
 * 资源查询构造器.
 *
 * 支持`order`,`offset`,`limit`,`fields`,`group`,`count`,`first`等条件筛选.
 *
 *
 * @param mixed $table 数据表名称
 * @param array $params 查询参数
 *
 * @return mixed 数据库查询结果
 *
 */
function selector($table, array $params = [])
{

    $query = is_string($table) ? table($table) : $table;

    foreach ($params as $action => $param) {

        switch ($action) {

            case 'order':
                $options = explode(':', $param);
                $query = $query->orderBy($options[0], $options[1]);
                break;

            case 'offset':
                $query = $query->skip($param);
                break;

            case 'limit':
                $query = $query->take($param);
                break;

            case 'fields':
                $query = $query->lists($param);
                break;

            case 'group':
                $query = $query->groupBy($param);
                break;

            case 'like':
                $options = explode(':', $param);
                $query = $query->where($options[0], 'like', $options[1]);
                break;

            case 'count':
            case 'first':
                break;

            default:
                $options = explode(':', $param);
                $query = count($options) == 2 ? $query->where($action, $options[0], $options[1]) : $query->where($action, $param);

        }
    }

    return isset($params['count']) ? $data = $query->count() : $data = $query->get();
}

/**
 * 过滤掉数据的多余字段.
 *
 * @param  array $data 原始数据
 * @param  array $fields 保留字段
 * @param  array $ignore 强制过滤字段
 *
 */
function filter_fields(array &$data, array $fields, array $ignore = [])
{
    $fields = array_diff($fields, $ignore);

    foreach ($data as $k => $v) {

        if (!in_array($k, $fields)) {

            unset($data[$k]);
        }

    }

}

/**
 * 数据库事务处理
 *
 * 在闭包函数中使用`DB`或`Eloquent`作数据库,监听闭包函数异常,操作数据库事务.
 *
 * @param $callback
 * @return mixed 闭包函数的返回结果,或者是操作事务操作失败信息
 *
 * @throws \Exception
 */
function transaction($callback)
{
    DB::beginTransaction();

    try {

        $status = $callback();

        DB::commit();

    } catch (\Exception $error) {

        DB::rollBack();

        throw $error;
    }

    return $status;
}

/**
 * 获取当前日期
 *
 * @return string
 */
function timestamp()
{
    return date('Y-m-d H:i:s');
}


/**
 * 获取指定目录的文件，可递归获取
 *
 * @param string $path
 * @param bool $recursion
 *
 * @return array
 */
function list_files($path, $recursion = false)
{
    $files = array();

    if (is_dir($path)) {

        if ($handler = opendir($path)) {

            while (($file = readdir($handler)) !== false) {

                $temp = $path . DIRECTORY_SEPARATOR . $file;

                if ($file != "." && $file != "..") {

                    if (is_file($temp)) {

                        array_push($files, $temp);

                    }
                    if (is_dir($temp)) {

                        if ($recursion) {

                            $files = array_merge($files, list_files($temp, $recursion = true));
                        }

                    }
                }
            }
            closedir($handler);
        }
    }

    return $files;
}


/**
 * 获取指定模块的目录，可递归获取
 *
 * @param string $path
 * @param bool $recursion
 *
 * @return array
 */
function list_dirs($path, $recursion = false)
{
    $paths = array();

    if (is_dir($path)) {

        if ($handler = opendir($path)) {

            while (($dir = readdir($handler)) !== false) {

                $target = $path . DIRECTORY_SEPARATOR . $dir;

                if ((is_dir($target)) && $dir !== "." && $dir !== "..") {

                    array_push($paths, $target);

                    if ($recursion) {

                        $paths = array_merge($paths, list_dirs($target, $recursion = true));
                    }
                }
            }

            closedir($handler);
        }
    }

    return $paths;
}


/**
 * 获取数组最大维度
 *
 * @param $array
 *
 * @return int
 */
function array_depth($array)
{
    if (!is_array($array)) return 0;

    $max_depth = 1;

    foreach ($array as $value) {

        if (is_array($value)) {

            $depth = array_depth($value) + 1;

            if ($depth > $max_depth) {

                $max_depth = $depth;
            }
        }
    }
    return $max_depth;
}

/**
 * 生成资源的字段信息
 *
 * @param $model
 * @param $field
 *
 * @return string
 */
function resource_field($model, $field)
{
    return $model . '::' . $field;
}








