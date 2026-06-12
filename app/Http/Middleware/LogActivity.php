<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    protected ActivityLogService $logger;

    public function __construct(ActivityLogService $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = auth()->user();
        if ($user) {
            $action = null;
            $routeName = $request->route()?->getName();

            if ($routeName === 'login') {
                $action = 'Login';
            } elseif ($routeName === 'logout') {
                $action = 'Logout';
            } elseif ($request->isMethod('post') && $request->is('arsip*')) {
                $action = 'Tambah Arsip';
            } elseif ($request->isMethod('put') && $request->is('arsip*')) {
                $action = 'Edit Arsip';
            } elseif ($request->isMethod('delete') && $request->is('arsip*')) {
                $action = 'Hapus Arsip';
            } elseif ($request->isMethod('post') && $request->is('pinjam*')) {
                $action = 'Peminjaman Arsip';
            } elseif ($request->isMethod('put') && $request->is('kembali*')) {
                $action = 'Pengembalian Arsip';
            } elseif ($request->isMethod('post') && $request->is('master*')) {
                $action = 'Perubahan Master Data';
            }

            if ($action) {
                $this->logger->log($action, json_encode([
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'input' => $request->except(['password', 'password_confirmation', '_token', '_method']),
                ], JSON_UNESCAPED_UNICODE));
            }
        }

        return $response;
    }
}
