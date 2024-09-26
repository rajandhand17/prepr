<?php

namespace App\Http\Controllers\Maestro\VendorManagement;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Traits\Maestro\VendorManagement\VendorManagementTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class VendorManagementController extends Controller
{
    use VendorManagementTrait;


    public function index(Builder $builder, Request $request)
    {
        try {
            $vendor = $this->getAllVendorData();
            if ($vendor) {
                if ($request->ajax()) {
                    return DataTables::eloquent($vendor)
                        ->addColumn('action', function (Vendor $vendor) {
                            return '<a style="padding-left:10px" class="mr-10" href="'.route('vendor-management.show', ['vendor_management' => $vendor->id]).'"><i class="fas fa-eye"></i></a>

                            <a style="padding-left:50px" class="mr-10" href="'.route('vendor-management.edit', ['vendor_management' =>$vendor->id]).'"><i class="fas fa-edit"></i></a>
                            <a style="padding-left:50px" href="javascript:void(0)" onclick="deleteVendor(\''.route('vendor-management.destroy', ['vendor_management' => $vendor->id]).'\')"><i class="fas fa-trash"></i></a>';
                        })
                        ->editColumn('is_active', function (Vendor $vendor) {
                            return $vendor->is_active == '1'
                                ? "<span class='badge badge-success'>Active</span>"
                                : "<span class='badge badge-danger'>Inactive</span>";
                        })
                        ->addIndexColumn()
                        ->rawColumns(['is_active', 'action'])
                        ->make(true);
                }

                $html = $builder->columns([
                    ['data' => 'id', 'name' => 'DT_Row_Index', 'width' => '5%', 'orderable' => false, 'searchable' => false],
                    ['data' => 'name', 'name' => 'name', 'title' => 'Name', 'width' => '25%'],
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email', 'width' => '25%'],
                    ['data' => 'api_key', 'name' => 'api_key', 'title' => 'API Key', 'width' => '25%'],
                    ['data' => 'is_active', 'name' => 'is_active', 'title' => 'Status', 'width' => '5%'],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'width' => '15%', 'orderable' => false, 'searchable' => false],
                ])->parameters(['order' => [0, 'desc']]);

                return view('maestro.vendor.index', compact('html'));
            } else {
                return response()->json(['error' => 'No vendor data found'], 404);
            }
        } catch (\Exception $e) {
            return redirect()->route('maestro.vendor.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $vendor = $this->getVendorById($id);
            if (!$vendor->exists) {
                return redirect()->route('vendor-management.index')->with(['error' => 'This Vendor not found.']);
            }

            return view('maestro.vendor.edit', compact('vendor'));
        } catch (\Exception $e) {
            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function create()
    {
        try {
            return view('maestro.vendor.create');
        } catch (\Exception $e) {
            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function store(Request $request)
    {
        try {
            $creatVendor = $this->createVendor($request);
            if ($creatVendor) {
                return redirect()->route('vendor-management.index')->with('success', 'Vendor created successfully');
            }

            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        } catch (\Exception $e) {
            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function show(string $id)
    {
        try {
            $vendor = $this->getVendorById($id);
            if (!$vendor->exists) {
                return redirect()->route('vendor.index')->with(['error' => 'Vendor not found.']);
            }

            return view('maestro.vendor.view', compact('vendor'));
        } catch (\Exception $e) {
            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $update = $this->updateVendorById($id, $request);
            if ($update) {
                return redirect()->route('vendor-management.index')->with('success', 'User Updated successfully');
            }

            return redirect()->route('vendor-management.index')->with(['error' => 'Something want wrong']);
        } catch (\Exception $e) {
            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $checkVendorExistsOrNot = $this->checkVendorExists($id);
            if (!$checkVendorExistsOrNot) {
                return response()->json(['status' => 'failed', 'message' => 'Record not found']);
            }
            if ($this->deleteVendorById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Record deleted successfully']);
            }

            return response()->json(['status' => 'failed', 'message' => 'Record deleted failed']);
        } catch (\Exception $e) {
            return redirect()->route('vendor-management.index')->with(['error' => 'Oops! Something went wrong. Please try again later.']);
        }
    }
}
