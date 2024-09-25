<?php

namespace App\Http\Controllers\Maestro\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Maestro\SettingService;
use App\Traits\Maestro\Setting\SettingTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class SettingController extends Controller
{
    use SettingTrait;
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->middleware('auth-check');
        $this->settingService = $settingService;
    }

    public function index(Builder $builder, Request $request)
    {
        try {
            // Getting Settings tables all records
            $settingInfo = $this->getSettings();
            if (!empty($settingInfo)) {
                if ($request->ajax()) {
                    // Adding html table's raws with data
                    return DataTables::eloquent($settingInfo)
                        ->editColumn('module_type', static function (Setting $settingInfo) {
                            switch ($settingInfo->module_type) {
                                case '0':
                                    $html = "<span class='badge badge-success'>BOOLEAN</span>";
                                    break;
                                case '1':
                                    $html = "<span class='badge badge-success'>NUMBER</span>";
                                    break;
                                case '2':
                                    $html = "<span class='badge badge-success'>DATE</span>";
                                    break;
                                case '3':
                                    $html = "<span class='badge badge-success'>TEXT</span>";
                                    break;
                                case '4':
                                    $html = "<span class='badge badge-success'>SELECT</span>";
                                    break;
                                case '5':
                                    $html = "<span class='badge badge-success'>FILE</span>";
                                    break;
                                case '6':
                                    $html = "<span class='badge badge-success'>TEXTAREA</span>";
                                    break;
                            }

                            return $html;
                        })
                        ->addColumn('action', static function (Setting $settingInfo) {
                            return '<a style="padding-left:50px" class="mr-10" href="'.route('setting.edit', ['setting' => $settingInfo->id]).'"><i class="fas fa-edit"></i></a>';
                        })
                        ->addIndexColumn()
                        ->rawColumns(['module_type', 'action'])
                        ->make(true);
                }
            }

            $html = $builder->columns([
                ['data' => 'id', 'name' => 'DT_Row_Index', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                ['data' => 'code', 'name' => 'code', 'title' => 'Code', 'width' => '5%'],
                ['data' => 'module_type', 'name' => 'type', 'title' => 'Type', 'width' => '10%'],
                ['data' => 'label', 'name' => 'label', 'title' => 'Label', 'width' => '10%'],
                ['data' => 'value', 'name' => 'value', 'title' => 'Value', 'width' => '10%'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'width' => '15%', 'orderable' => false, 'searchable' => false],
            ])->parameters(['order' => [0, 'desc']]);

            return view('maestro.setting.index', compact('html'));
        } catch (\Exception $e) {
            return redirect()->route('setting.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function edit(string $id)
    {
        try {
            $setting = $this->getSettingById($id);
            if (!$setting->exists) {
                return redirect()->route('setting.index')->with(['error' => 'This Id is not exists in the database']);
            }

            return view('maestro.setting.edit', compact('setting'));
        } catch (\Exception $e) {
            return redirect()->route('setting.index')->with(['error'=>'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            if ($this->updateSettingById($id, $request)) {
                return redirect()->route('setting.index')->with('success', 'Your settings have been updated successfully');
            }

            return redirect()->route('setting.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (\Exception $e) {
            return redirect()->route('setting.index')->with(['error'=>'Oops! Something went wrong. Please try again later.']);
        }
    }
}
