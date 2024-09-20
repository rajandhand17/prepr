<?php

namespace App\Http\Controllers\Maestro\EmailLog;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Services\Maestro\LanguageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

/*-----------------------------------------------------------------------------------------
@description: This controller is for handle email templates
@functions: show,create,edit,store,update,destroy
-----------------------------------------------------------------------------------------*/

class EmailLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //use EmailLogTrait;

    public function construct()
    {
        $this->middleware('web');
    }

    /**
     * Show the application dashboard.
     *
     * @param Builder $builder
     *
     * @return JsonResponse
     */

    /* -----------------------------------------------------------------------------------------
    @Description: Function for show all email templates
    @Output: Show all email templates on admin panel
    -------------------------------------------------------------------------------------------- */
    public function index(Builder $builder)
    {
        try {
            $templates = EmailLog::query();
            if (request()->ajax()) {
                return DataTables::eloquent($templates)

                    ->editColumn('subject', static function (EmailLog $template) {
                        return $template->subject;
                    })
                    ->editColumn('body', static function (EmailLog $template) {
                        $bodyContent = preg_replace('#<style(.*?)>(.*?)</style>|/#is', '', $template->body);
                        $bodyContent = preg_replace('#<script(.*?)>(.*?)</script>|/#is', '', $bodyContent);

                        return strip_tags(html_entity_decode($bodyContent));
                    })
                    ->editColumn('To', static function (EmailLog $template) {
                        return $template->to;
                    })
                    ->editColumn('From', static function (EmailLog $template) {
                        return $template->from;
                    })
                    ->editColumn('created at', static function (EmailLog $template) {
                        return $template->created_at;
                    })
                    ->setRowData([
                        'data-id' => static function ($template) {
                            return 'row-'.$template->id;
                        },
                    ])
                    ->toJson();
            }
            $html = $builder->columns([
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
                ['data' => 'subject', 'name' => 'subject', 'title' => 'Subject'],
                ['data' => 'to', 'name' => 'to', 'title' => 'To'],
                ['data' => 'from', 'name' => 'from', 'title' => 'From'],
                ['data' => 'body', 'name' => 'body', 'title' => 'Body'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
            ]);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.email-log.index', compact('html', 'languages'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return redirect()->back()->with(['error' => 'Something went wrong']);
        }
    }
}
