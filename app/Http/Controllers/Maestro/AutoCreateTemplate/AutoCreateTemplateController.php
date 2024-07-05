<?php

namespace App\Http\Controllers\Maestro\AutoCreateTemplate;

use App\Http\Controllers\Controller;
use App\Traits\Maestro\AutoCreateTemplate\AutoCreateTemplateTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class AutoCreateTemplateController extends Controller
{
    use AutoCreateTemplateTrait;

    public function __construct()
    {
        $this->middleware('web');
    }

    public function index(Builder $builder)
    {
        try {
            $roles=$this->getRole();
            return view('maestro.autocreatetemplate.index', compact('roles'));
        }catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }
}
