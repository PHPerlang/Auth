<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ResolveClientMiddleware
{

    /**
     * The request client id.
     *
     * @var string
     */
    protected $client_id;


    /**
     * The request client version.
     *
     * @var string
     */
    protected $client_version;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $client = strtolower($request->header('X-App-Id'));

        if (!$request->route()->open) {

            if (!$client) {

                exception(900);
            }

            if (!preg_match('/.+:[0-9]+(\.[0-9]+)*/', $client)) {

                exception(901);
            }

            list($this->client_id, $this->client_version) = explode(':', $client);

            $request->client = new \StdClass();
            $request->client->id = $this->client_id;
            $request->client->version = $this->client_version;
            $request->client->group = $this->getClientGroup($this->client_id);

        }

        return $next($request);
    }

    /**
     * Get client Group by client id.
     *
     * @param $client_id
     *
     * @return string
     */
    protected function getClientGroup($client_id)
    {

        foreach (config('client') as $group => $clients) {

            if (in_array($client_id, $clients)) {

                return $group;
            }
        }

        exception(902);
    }
}
