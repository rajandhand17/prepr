<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *		title		= "Preprlabs Central APIs",
 *		version		= "1.0.0",
 *		description	= "<p>Preprlabs Central APIs documentation is intended only for Developers.</p><p>Below are the steps you can use to export these API documentations on Postman.</p><ol><li>Copy the URL of swagger API docs JSON <a href='/docs/api-docs.json'>api-docs.json</a>.</li><li>Open Postman and go to Import from link (Top-Left corner).</li><li>Use default settings & click Import.</li></ol>",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
