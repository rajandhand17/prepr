<?php

namespace App\Services\Chat;

use App\Helpers\FileUploadHelper;
use App\Jobs\ProcessMessageSent;
use App\Models\Message;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChatService
{

    public function __construct(private ConversationService $conversationService)
    {
    }

    /**
     * @throws Exception
     */
    private function storeChatFiles(): array
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
    private function storeChat(array $data): Model|Builder
    {
        try {
            DB::beginTransaction();
            $chatFiles = $this->storeChatFiles();

            $chat = Message::create([
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

    public function listChat(int $conversationId): LengthAwarePaginator
    {
        return Message::with('sender', 'seenUsers')
            ->where('conversation_id', $conversationId)
            ->paginate(30);
    }

    /**
     * @throws Exception
     */
    public function sendChat(array $data): Model|Builder
    {
        try {
            DB::beginTransaction();
            $chat = $this->storeChat($data);
            // the message you sent is seen by you.
            $this->conversationService->markConversationAsSeen($data['conversation_id'], auth()->user()->id, $chat->id);
            dispatch(new ProcessMessageSent($chat, $data['conversation_id']))->onQueue('chat');

            DB::commit();
            return $chat;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception);
        }
    }
}
