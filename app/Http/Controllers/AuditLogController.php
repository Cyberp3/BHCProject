<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    //
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->latest('id');

        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->input('action')}%");
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%");
                    });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $modules = AuditLog::select('module')->distinct()->pluck('module');

        return view('audit-logs.index', compact('logs', 'modules'));
    }
}
