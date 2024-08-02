<?php

namespace App\Http\Controllers\Maestro\EmailTemplate;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\Maestro\LanguageService;
use App\Traits\Maestro\EmailTemplate\EmailTemplateTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

/*-----------------------------------------------------------------------------------------
@description: This controller is for handle email templates
@functions: show,create,edit,store,update,destroy
-----------------------------------------------------------------------------------------*/

class EmailTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    use EmailTemplateTrait;

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
            $templates = $this->getEmailTemplates();
            if (request()->ajax()) {
                return DataTables::eloquent($templates)
                    ->addColumn('action', static function (EmailTemplate $template) {
                        return '<a href="'.route('email-templates.edit', $template->id).'" class="mr-25" data-toggle="tooltip" data-original-title="Edit" data-id="'.$template->id.'"><i class="fas fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" onclick="deleteEmailTemplate
                        (\''.route('email-templates.destroy', $template->id).'\')"> <i class="fas fa-trash"></i></a>';
                    })
                    ->editColumn('content', static function (EmailTemplate $template) {
                        // return Html::decode($template->content);
                        return strip_tags($template->body_content);
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
                ['data' => 'body_content', 'name' => 'body_content', 'title' => 'Content'],
                ['data' => 'action', 'name' => 'Action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
            ]);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.email-template.index', compact('html', 'languages'));
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for share view to create email template
    @Output: return the view for create email template
    -------------------------------------------------------------------------------------------- */
    public function create()
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.email-template.create', compact('languages'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for share view of  edit email template
    @input:id
    -------------------------------------------------------------------------------------------- */
    public function edit($id)
    {
        try {
            $template = $this->getEmailTemplatesById($id);
            $languages = LanguageService::getAllActiveLanguages();

            return view('maestro.email-template.edit', compact('template', 'languages'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for save new  email template
    @input: identifier, subject, content
    @Output: Save new email template in database
    -------------------------------------------------------------------------------------------- */
    public function store(Request $request)
    {
        try {
            $this->construct();
            if ($this->createEmailTemplate($request)) {
                return redirect()->route('email-templates.index')->with('success', 'Template has created successfully');
            }

            return redirect()->route('email-templates.index')->with(['error' => 'Something went wrong.']);
        } catch (Exception $e) {
            return redirect()->route('email-templates.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for update  email template
    @input: id, identifier, subject, content
    @Output: update email template in database
    -------------------------------------------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        try {
            $this->construct();
            if ($this->updateEmailTemplateById($id, $request)) {
                return redirect()->route('email-templates.index')->with('success', 'Email Template has Updated successfully');
            }

            return redirect()->route('email-templates.index')->with(['error' => 'Something went wrong']);
        } catch (Exception $e) {
            return redirect()->route('email-templates.index')->with(['error' => 'Something went wrong.']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for delete  email template
    @input: id
    @Output: delete email template in database
    -------------------------------------------------------------------------------------------- */
    public function destroy($id)
    {
        try {
            $this->construct();
            if ($this->deleteEmailTemplateById($id)) {
                return response()->json(['status' => 'success', 'message' => 'Email Template deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Upload image from ck-editor.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $original = $request->file('upload')->getClientOriginalName();

            $fileName = pathinfo($original, PATHINFO_FILENAME);

            $extension = $request->file('upload')->getClientOriginalExtension();

            $fileName = $fileName.'_'.time().'.'.$extension;

            $url = $request->file('upload')->store('uploads', 's3');

            return response()->json(['fileName' => $fileName, 'uploaded' => true, 'url' => \Config::get('app.CloudFrontUrl').'/'.$url]);
        }
    }
}
