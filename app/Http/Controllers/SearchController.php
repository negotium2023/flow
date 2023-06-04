<?php

namespace App\Http\Controllers;

use App\Client;
use App\Document;
use App\RelatedParty;
use App\Template;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->result_count = 6;
        $this->actions = collect([
            [
                'name' => 'Create Client',
                'route' => route('clients.create'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'Create Referrer',
                'route' => route('referrers.create'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'Create Document',
                'route' => route('documents.create'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'Create Template',
                'route' => route('templates.create'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Recents',
                'route' => route('recents'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Dashboard',
                'route' => route('dashboard'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Calendar',
                'route' => route('calendar'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Clients',
                'route' => route('clients.index'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Referrers',
                'route' => route('referrers.index'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Documents',
                'route' => route('documents.index'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Templates',
                'route' => route('templates.index'),
                'type' => 'Shortcut'
            ],
            [
                'name' => 'View Insight',
                'route' => route('insight.index'),
                'type' => 'Shortcut'
            ]
        ]);
        $this->actions->zip(['type' => 'Shortcut']);
    }

    public function getResults(Request $request)
    {
        if ($request->input('q') == '') {
            return null;
        }

        $search_term = $request->input('q');

        $actions = $this->actions->filter(function ($value) use ($search_term) {
            return str_contains(strtolower($value['name']), strtolower($search_term));
        });

        $actions = $actions->take($this->result_count);

        $results = collect();

        $results = $results->merge($actions);

        $client = new Client();
        $client->unHide();

        if ($results->count() < $this->result_count) {
            $results = $results->merge(
                Client::where('company', 'LIKE', '%' . $search_term . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $search_term . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_term . '%')
                    ->select(DB::raw("IF(company IS NULL, CONCAT(first_name,' ',last_name),company) as name"), DB::raw("CONCAT('" . route('clients.show', null) . "/',`id`,'/overview/',`process_id`,'/',`step_id`) as route"))
                    ->limit($this->result_count - $results->count())
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'Client';
                        return $item;
                    })
            );
        }

        if ($results->count() < $this->result_count) {
            $results = $results->merge(
                User::withTrashed()->where(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,''))"), 'LIKE', '%' . $search_term . '%')
                    ->select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) as name"), DB::raw("CONCAT('" . route('profile', null) . "/',`id`) as route"))
                    ->limit($this->result_count - $results->count())
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'User';
                        return $item;
                    })
            );
        }

        if ($results->count() < $this->result_count) {
            $results = $results->merge(
                Template::where('name', 'LIKE', '%' . $search_term . '%')
                    ->select('name', DB::raw("CONCAT('" . route('templates.show', null) . "/',`id`) as route"))
                    ->limit($this->result_count - $results->count())
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'Template';
                        return $item;
                    })
            );
        }

        if ($results->count() < $this->result_count) {
            $results = $results->merge(
                Document::where('name', 'LIKE', '%' . $search_term . '%')
                    ->select('name', DB::raw("CONCAT('" . route('documents.show', null) . "/',`id`) as route"))
                    ->limit($this->result_count - $results->count())
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'Document';
                        return $item;
                    })
            );
        }

        return $results;
    }
}
