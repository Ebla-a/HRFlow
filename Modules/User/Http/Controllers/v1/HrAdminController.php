<?php

namespace Modules\User\Http\Controllers\v1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Services\v1\HrService;

class HrAdminController extends Controller
{
    public function createRole(Request $request,HrService $service)
    {
        $service->createRole($request->validated());
        return response()->json(['messsage'=>"done"]);
    }

    public function deleteRole(Request $request,HrService $service)
    {
        $service->deleteRole($request->validated());
        return response()->json(['messsage'=>"done"]);
    }


    public function createPermission(Request $request,HrService $service)
    {
        $service->createPermission($request->validated());
        return response()->json(['messsage'=>"done"]);
    }

    public function deletePermission(Request $request,HrService $service)
    {
        $service->deletePermission($request->validated());
        return response()->json(['messsage'=>"done"]);
    }

    
    public function GrantRole(Request $request,HrService $service)
    {
        $service->GrantRole($request->validated());
        return response()->json(['messsage'=>"done"]);
    }

        public function revokeRole(Request $request,HrService $service)
    {
        $service->revokeRole($request->validated());
        return response()->json(['messsage'=>"done"]);
    }

        public function GrantPermission(Request $request,HrService $service)
    {
        $service->GrantPermission($request->validated());
        return response()->json(['messsage'=>"done"]);
    }

    public function revokePermission(Request $request,HrService $service)
    {
        $service->revokePermission($request->validated());
        return response()->json(['messsage'=>"done"]);
    }


}
