<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Closure;
use Auth;

class Permission
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$user = Auth::user();
		$user_type = $user->user_type;

		if ($user_type == 'customer') {
			if (!$request->ajax()) {
				return back()->with('error', _lang('Permission denied !'));
			} else {
				return new Response('<h4 class="text-center text-danger">' . _lang('Permission denied !') . '</h4>');
			}
		}

		if ($user_type != 'admin') {
			$route_name = \Request::route()->getName();

			if ($route_name != '') {

				$segments = explode(".", $route_name);
				if (count($segments) > 1) {
					if ($segments[1] == "update") {
						$route_name = $segments[0] . ".edit";
					} else if ($segments[1] == "store") {
						$route_name = $segments[0] . ".create";
					}
				}
				if (!has_permission($route_name)) {
					if (!$request->ajax()) {
						return back()->with('error', _lang('Permission denied !'));
					} else {
						return new Response('<h4 class="text-center text-danger">' . _lang('Permission denied !') . '</h4>');
					}
				}
			}

		}

		return $next($request);
	}
}
