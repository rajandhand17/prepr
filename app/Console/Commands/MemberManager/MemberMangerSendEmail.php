<?php

namespace App\Console\Commands\MemberManager;

use App\Helpers\SendMailHelper;
use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use DB;
use Illuminate\Console\Command;

class MemberMangerSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member-manger:send-email';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to send email invitation to members from member management.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $member_manger_list = MemberManagement::get()->where('email_status', '0');
            foreach ($member_manger_list as $list) {
                $user = new \stdClass();
                $user->email = $list->email;
                $data = ['subject' => 'Invite Users', 'first_name' =>'first_name', 'last_name' =>'last_name', 'otp' =>'otp'];
                $mail = SendMailHelper::sendMail($user, 'email.member_manager_invite_users', $data);
                if ($mail) {
                    $update_member_management = MemberManagement::where('email', $list->email)->update(['email_status'=>'1', 'invite_status'=>'2']);
                    if ($update_member_management) {
                        DB::commit();
                        $this->info('Member manger emails send');
                    }
                }
            }
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Member manger emails not send');
        }
    }
}
