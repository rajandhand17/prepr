<?php

namespace App\Services\Chat;

use App\Helpers\FileUploadHelper;
use App\Jobs\ProcessMessageSent;
use App\Models\ConversationMessage;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MessageService
{

    public function __construct(private ConversationService $conversationService)
    {
    }

    /**
     * @throws Exception
     */
    private function storeFiles(): array
    {
        $chatFiles = [];

        if (!request()->has('attachment')) {
            return [];
        }

        $files = request()->file('attachment');

        foreach ($files as $item) {
            $files = FileUploadHelper::uploadImageToS3($item, 'chat');
            if (!$files) {
                throw new Exception("Upload failed");
            }
            $chatFiles[] = $files;
        }

        return $chatFiles;
    }

    /**
     * @throws Exception
     */
    private function store(array $data): Model|Builder
    {
        try {
            DB::beginTransaction();
            $chatFiles = $this->storeFiles();

            $chat = ConversationMessage::create([
                'uuid' => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                "conversation_id" => $data['conversation_id'],
                "message" => $data['message'],
                "attachments" => $chatFiles,
                "sender_id" => auth()->user()->id,
            ]);
            DB::commit();

            return $chat;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function list(int $conversationId): LengthAwarePaginator
    {
        return ConversationMessage::with('sender', 'seenUsers')
            ->where('conversation_id', $conversationId)
            ->paginate(30);
    }

    /**
     * @throws Exception
     */
    public function send(array $data): Model|Builder
    {
        try {
            DB::beginTransaction();
            $chat = $this->store($data);
            // the message you sent is seen by you.
            $this->conversationService->markAsSeen($data['conversation_id'], auth()->user()->id, $chat->id);
            dispatch(new ProcessMessageSent($chat, $data['conversation_id']))->onQueue('chat');

            DB::commit();
            return $chat;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception);
        }
    }
}
