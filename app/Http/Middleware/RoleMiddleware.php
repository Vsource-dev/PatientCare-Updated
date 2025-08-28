<?php
//Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
  public function handle(Request $request, Closure $next, $role)
{
    if (! Auth::check()) {
        // not even logged in
        return redirect('/login');
    }

    if (Auth::user()->role !== $role) {
        // If the user role does not match, abort or redirect
        return abort(403, 'Unauthorized');
    }

    return $next($request);
}

}
