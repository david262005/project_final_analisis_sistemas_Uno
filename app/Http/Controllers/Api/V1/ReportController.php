<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->attributes->get('tenant');

        $query = User::query()->where('tenant_id', $tenant->id)->with('roles', 'tenant');

        if ($request->filled('role')) {
            $role = $request->get('role');
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        if ($request->get('export') === 'csv') {
            $filename = 'report_users_' . now()->format('Ymd_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($users) {
                $output = fopen('php://output', 'w');
                fputcsv($output, ['id', 'name', 'email', 'roles', 'tenant', 'created_at']);

                foreach ($users as $u) {
                    $roles = $u->roles->pluck('name')->join('|');
                    fputcsv($output, [$u->id, $u->name, $u->email, $roles, $u->tenant->name ?? '', $u->created_at]);
                }

                fclose($output);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json(['data' => $users]);
    }
}
