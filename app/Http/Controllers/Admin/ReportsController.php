<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index($type, Request $request)
    {
        $range = Carbon::now()->subDays(30);

        if ($type == 'last30dayusers') {
            $stats = DB::table('users')
                ->where('created_at', '>=', $range)
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get([
                    DB::raw('Date(created_at) as date'),
                    DB::raw('COUNT(*) as value'),
                ]);
        } elseif ($type == 'last30news') {
            $stats = DB::table('posts')
                ->where('language', get_buzzy_query_locale())
                ->where('created_at', '>=', $range)
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get([
                    DB::raw('Date(created_at) as date'),
                    DB::raw('COUNT(*) as news'),
                ]);
        }
        if ($type == 'last30SPOTStotal') {
            $totalnews = Post::query()->byLanguage()->byType('news')->where('created_at', '>=', $range)->count();
            $totallists = Post::query()->byLanguage()->byType('list')->where('created_at', '>=', $range)->count();
            $totalpolls = Post::query()->byLanguage()->byType('poll')->where('created_at', '>=', $range)->count();
            $totalquizzes = Post::query()->byLanguage()->byType('quiz')->where('created_at', '>=', $range)->count();
            $totalvideos = Post::query()->byLanguage()->byType('video')->where('created_at', '>=', $range)->count();

            $stats = [
                ['label' => trans('admin.Newsin30Days'),  'value' => $totalnews],
                ['label' => trans('admin.Listsin30Days'),  'value' => $totallists],
                ['label' => trans('admin.Quizzesin30Days'),  'value' => $totalquizzes],
                ['label' => trans('admin.Pollsin30Days'),  'value' => $totalpolls],
                ['label' => trans('admin.Videosin30Days'),  'value' => $totalvideos],
            ];
        }

        return $stats;
    }
}
