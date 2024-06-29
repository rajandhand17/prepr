<?php

namespace App\Services\Chat;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Http\Resources\Chat\MessageResource;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Notifications\MessageCreated;
use App\Notifications\MessageDeleted;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MessageService
{
    public function __construct(private readonly ConversationService $conversationService)
    {
    }

    private function storeFiles()
    {
        try {
            $chatFiles = [];
            if (!request()->has('attachment')) {
                return [];
            }

            $files = request()->file('attachment');

            foreach ($files as $item) {
                if (false !== mb_strpos($item->getMimeType(), 'image')) {
                    $files = FileUploadHelper::uploadImageToS3($item, 'chat');
                } elseif (false !== mb_strpos($item->getMimeType(), 'video')) {
                    $files = FileUploadHelper::uploadVideoToS3($item, 'chat');
                } elseif (false !== mb_strpos($item->getMimeType(), 'audio')) {
                    $files = FileUploadHelper::uploadDocToS3($item, 'chat');
                } else {
                    $files = FileUploadHelper::uploadDocToS3($item, 'chat');
                }

                if (!$files) {
                    return false;
                }
                $chatFiles[] = $files;
            }

            return $chatFiles;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function store(array $data)
    {
        try {
            DB::beginTransaction();
            $messageFiles = $this->storeFiles();

            if ($messageFiles === false) {
                DB::rollBack();

                return false;
            }

            $message = ConversationMessage::create([
                'uuid'            => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                'conversation_id' => $data['conversation_id'],
                'message'         => $data['message'],
                'attachments'     => $messageFiles,
                'sender_id'       => auth()->user()->id,
            ]);
            DB::commit();

            return $message;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function list(int $conversationId)
    {
        try {
            return ConversationMessage::with('sender', 'seenUsers')
                ->where('conversation_id', $conversationId)
                ->paginate(config('site-settings.message_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    private function sendNotification($message, $conversationId)
    {
        try {
            $conversation = Conversation::where('id', $conversationId)->first();
            Notification::send($conversation, new MessageCreated(collect(MessageResource::make($message)), $conversationId));

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function send(array $data)
    {
        try {
            DB::beginTransaction();
            $message = $this->store($data);
            // the message you sent is seen by you.
            $this->conversationService->markAsSeen($data['conversation_id'], auth()->user()->id, $message->id);
            $this->sendNotification($message, $data['conversation_id']);
            DB::commit();

            return $message;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);
            DB::rollBack();

            return false;
        }
    }

    public function getByMessageUUID($uuid)
    {
        try {
            $conversationMessage = ConversationMessage::where('uuid', $uuid)->first();
            if ($conversationMessage) {
                return $conversationMessage;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deleteMessage($data)
    {
        try {
            $conversation = $data->conversation()->first();
            ConversationMessage::find($data->id)->delete();
            Notification::send($conversation, new MessageDeleted(['uuid' => $data->uuid], $conversation->id));

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
