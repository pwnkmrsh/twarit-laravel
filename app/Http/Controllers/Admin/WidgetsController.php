<?php

namespace App\Http\Controllers\Admin;

use App\Widgets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WidgetsController extends MainAdminController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('DemoAdmin', ['only' => ['delete', 'addnew']]);
    }

    public function index(Request $request, Widgets $widgets)
    {
        $widgeta = "";

        if ($request->query('edit')) {
            $widgeta = Widgets::findOrFail($request->query('edit'));
        }

        $PostPageSidebar = $widgets->where('type', 'PostPageSidebar')->get();
        $CategoriesPageSidebar = $widgets->where('type', 'CatSide')->get();
        $HeaderBelow = $widgets->where('type', 'HeaderBelow')->get();
        $PostBelow = $widgets->where('type', 'PostBelow')->get();
        $PostBetween2nd3rdentry = $widgets->where('type', 'Post2nd3rdentry')->get();
        $Footer = $widgets->where('type', 'Footer')->get();
        $PostShareBw = $widgets->where('type', 'PostShareBw')->get();
        $Homencolfirst = $widgets->where('type', 'Homencolfirst')->get();
        $Homencolsec = $widgets->where('type', 'Homencolsec')->get();
        $IndexAmp = $widgets->where('type', 'IndexAmp')->get();
        $PostShareBwAmp = $widgets->where('type', 'PostShareBwAmp')->get();
        $PostBelowAmp = $widgets->where('type', 'PostBelowAmp')->get();

        return view('_admin.pages.widgets', compact(
            'widgets',
            'widgeta',
            'PostPageSidebar',
            'CategoriesPageSidebar',
            'HeaderBelow',
            'PostShareBw',
            'Homencolfirst',
            'Homencolsec',
            'PostBelow',
            'PostBetween2nd3rdentry',
            'Footer',
            'IndexAmp',
            'PostShareBwAmp',
            'PostBelowAmp',
        ));
    }


    public function delete($id)
    {
        $pages = Widgets::findOrFail($id);
        $pages->delete();

        Session::flash('success.message', trans("admin.Deleted"));

        return redirect()->route('admin.widgets');
    }

    public function addnew(Request $request)
    {

        $inputs = $request->all();
        $v = Validator::make($inputs, [
            'key' => 'required',
            'text' => 'required',
            'type' => 'required',
            'display' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput($inputs);
        }

        if (!empty($inputs['id'])) {
            $cat = Widgets::findOrFail($inputs['id']);
        } else {
            $cat = new Widgets;
        }

        $cat->key = $inputs['key'];
        $cat->text = $inputs['text'];
        $cat->type = $inputs['type'];
        $cat->display = $inputs['display'];
        $cat->showweb = $inputs['showweb'];
        $cat->showmobile = $inputs['showmobile'];
        $cat->save();

        Cache::forget('widget_ad_' . $cat->type);

        if (!empty($inputs['id'])) {
            Session::flash('success.message', trans("admin.ChangesSaved"));
        } else {
            Session::flash('success.message', trans("admin.SuccesfulyCreateted"));
        }

        return redirect()->route('admin.widgets');
    }
}
