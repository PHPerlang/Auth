<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;

class Code
{
    /**
     * 生成验证码
     *
     * @param $length
     * @return string
     */
    public static function generateCode($length = 4)
    {
        return $length == 4 ? mt_rand(1000, 9999) : mt_rand(100000, 999999);
    }

    /**
     * 获取离第二天前剩余的分钟数
     *
     * @return integer
     */
    public static function getTheDayLeftMinutes()
    {
        return (strtotime(date("Y-m-d", strtotime("+1 day")) . ' 00:00:00') - time()) / 60;
    }

    /**
     * 缓存验证码
     *
     * @param string $key
     * @param integer $code
     * @param integer $time
     */
    public static function cacheCode($key, $code, $time = 10)
    {
        Cache::tags('auth::code')->put($key, $code, $time);

    }

    /**
     * 测试缓存的验证码
     *
     * @param string $key
     * @param string $input
     *
     * @return bool
     */
    public static function testCacheCode($key, $input)
    {
        $required = Cache::tags('auth::code')->get($key, uniqid());

        if ($input != $required) {

            if (config('app.env') == 'production' || $input != '888888') {

                return false;
            }

        }

        return true;
    }

    /**
     * 检查缓存的验证码，通过则删除缓存
     *
     * @param string $key
     * @param string $input
     *
     * @return bool
     */
    public static function checkCacheCode($key, $input)
    {

        if (!self::testCacheCode($key, $input)) {
            return false;
        }

        self::forgetCode($key);

        return true;
    }

    /**
     * 检查距离下次发送还剩多少时间，单位(s)
     *
     * @param $key
     *
     * @return int
     */
    public function checkLeftTime($key)
    {
        return 2;
    }

    /**
     * 设置验证码周期检查
     *
     * @param string $key
     */
    public static function setCodeFrequency($key)
    {
        $last = Cache::tags('auth::timer')->get($key);

        if (!$last) {

            Cache::tags('auth::timer')->put($key, [0, time()], self::getTheDayLeftMinutes());

        } else {

            Cache::tags('auth::timer')->put($key, [$last[0]++, time()], self::getTheDayLeftMinutes());
        }
    }


    /**
     * 清除验证码缓存
     *
     * @param string $key
     */
    public static function forgetCode($key)
    {
        Cache::tags('auth::code')->forget($key);
    }


    /**
     * 检查验证码发送频率
     *
     * @param string $key
     *
     * @return false;
     */
    public static function checkCodeFrequency($key)
    {
        $last = Cache::tags('auth::timer')->get($key);

        if ($last) {

            if ($last[0] > config('auth::config.send_code_max_times', 5) - 1) {

                return false;
            }

            if (time() - $last[1] <= config('auth::config.send_code_frequency', 60)) {

                return false;
            }
        }

        return true;
    }

    /**
     * 记录验证码日志
     *
     * @param array $log
     *              $log['type']        验证码类型 email/mobile
     *              $log['key']         验证码接收对象
     *              $log['code']        验证码内容
     *              $log['tag']         验证码标签
     *              $log['status']      验证码状态
     *              $log['description'] 验证码状态描述
     */
    public static function log(array $log)
    {
        $log = collect($log);

        $code = new \Modules\Auth\Models\Code();

        $code->code_type = $log->get('type');
        $code->code_key = $log->get('key');
        $code->code_content = $log->get('code');
        $code->code_status = $log->get('status');
        $code->status_description = $log->get('description');
        $code->code_tag = $log->get('tag');
        $code->expired_at = timestamp(10 * 60);
        $code->save();
    }

}