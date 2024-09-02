<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\Discussion as Discussions;
use App\Models\DiscussionSocialActivity;
use App\Models\Lab;
use App\Models\Project;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Discussion extends Command
{
    protected $signature = 'migrate-old-data:discussion';
    protected $description = 'Migrate old comments and comments reply data to new DB structure.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info('Migrating old data for comments and comments reply table.');
            DB::beginTransaction();

            $this->migrateComments();
            $this->migrateCommentReplies();

            DB::commit();
            $this->info('Migration of old data for comments and replies completed.');
        } catch (\Exception $e) {
            DB::rollback();
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
        }
    }
// Migrating the comments tables.
    private function migrateComments()
    {
        $comments = DB::connection('mysql2')->table('comments')->get();
        if ($comments->isEmpty()) {
            $this->error('No comments found.');
            return;
        }

        foreach ($comments as $comment) {
            if (!User::find($comment->user_id)) continue;

            $model = $this->getModelForType($comment->type);
            if (!$model::find($comment->reference_id)) continue;

            $discussion = new Discussions();
            $discussion->fill([
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'module_id' => $comment->reference_id,
                'module_type' => $this->getModuleType($comment->type),
                'comments' => $comment->comment,
                'attachment' => $this->convertToJson($comment->attachement),
            ])->save();

            $this->handleLikes($comment->id, $comment->u_like, '1');
            $this->handleLikes($comment->id, $comment->u_unlike, '2');
        }
    }
// Migrating comment reply tables
    private function migrateCommentReplies()
    {
        $commentReplies = DB::connection('mysql2')->table('comment_replies')->get();
        foreach ($commentReplies as $reply) {
            if (!User::find($reply->user_id)) continue;

            $parentComment = Discussions::find($reply->comment_id);
            if (!$parentComment) continue;

            $discussion = new Discussions();
            $discussion->fill([
                'user_id' => $reply->user_id,
                'module_id' => $parentComment->module_id,
                'module_type' => $parentComment->module_type,
                'comments' => $reply->comment,
                'attachment' => $this->convertToJson($reply->attachement),
                'comment_id' => $reply->comment_id,
            ])->save();

            $this->handleLikes($reply->id, $reply->u_like, '1');
            $this->handleLikes($reply->id, $reply->u_unlike, '2');
        }
    }
// Getting Model base on type
    private function getModelForType($type)
    {
        return match ($type) {
            'labs' => Lab::class,
            'project' => Project::class,
            'challenge' => Challenge::class,
            default => null,
        };
    }

    private function getModuleType($type)
    {
        return match ($type) {
            'labs' => '0',
            'project' => '1',
            'challenge' => '2',
            default => '',
        };
    }
    // Converted Value to json
    private function convertToJson($value)
    {
        return empty($value) ? null : json_encode($value);
    }
    // Saved data in table
    private function handleLikes($commentId, $likes, $likeDislike)
    {
        if (!empty($likes)) {
            foreach (json_decode($likes) as $userId) {
                (new DiscussionSocialActivity([
                    'comment_id' => $commentId,
                    'user_id' => $userId,
                    'like_dislikes' => $likeDislike,
                ]))->save();
            }
        }
    }
}
