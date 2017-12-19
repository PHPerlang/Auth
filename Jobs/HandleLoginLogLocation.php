<?php

namespace Modules\Auth\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Modules\Auth\Models\LoginLog;
use Modules\Auth\Models\Member;
use Modules\Log\Models\IPCache;
use Requests;


class HandleLoginLogLocation implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    protected $log;

    protected $member;

    protected $amap_key;


    public function __construct(Member $member, LoginLog $log)
    {
        $this->log = $log;
        $this->member = $member;
        $this->amap_key = env('AMAP_KEY');
    }

    public function handle()
    {
        if ($this->log->ip) {
            if ($address = IPCache::getAddress($this->log->ip)) {
                $this->log->address = $address->address;
                $this->log->province = $address->province;
                $this->log->city = $address->city;
                $this->log->save();
            } else if ($this->amap_key) {

                $url = 'http://restapi.amap.com/v3/ip?key=' . $this->amap_key . '&ip=' . $this->log->ip;
                $request = Requests::get($url);

                if ($request->status_code == 200) {

                    $response = json_decode($request->body);
                    if ($response->status == 1 && is_string($response->province)) {
                        $this->log->address = is_string($response->province) && is_string($response->city) && $response->province != $response->city ? $response->province . $response->city : $response->province;
                        $this->log->province = is_string($response->province) ? $response->province : '未知';;
                        $this->log->city = is_string($response->city) ? $response->city : '未知';
                        $this->log->save();
                        IPCache::setAddress($this->log->ip, $this->log->address, $response->province, $response->city);
                    } else {
                        $this->log->error = $response->info;
                        $this->log->save();
                    }

                } else {
                    $this->log->error = '接口请求网络错误';
                    $this->log->save();
                }
            }
        }
    }
}
