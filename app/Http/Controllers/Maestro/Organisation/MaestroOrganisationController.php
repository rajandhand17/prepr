<?php

namespace App\Http\Controllers\Maestro\Organisation;

use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Models\Organisation;
use App\Helpers\Maestro\Users\MaestroOrganisationsHelper;

class MaestroOrganisationController extends HomeController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = MaestroOrganisationHelper::getUserList();
        return view('maestro.users.users-list',['users' => $users ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
