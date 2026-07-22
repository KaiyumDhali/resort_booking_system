<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public string $action // 'created' | 'updated' | 'completed'
    ) {}

    public function via($notifiable)
    {
        return ['database']; // আপাতত শুধু database, চাইলে পরে 'mail' যোগ করা যাবে
    }

    public function toDatabase($notifiable)
{
$user = match ($this->action) {
    'created'   => optional($this->task->creator)->name,
    'updated'   => optional($this->task->updater)->name,
    'approved'  => optional($this->task->approver)->name,
    'completed' => optional($this->task->completer)->name,
    default     => 'System',
};

    $messages = [
        'created'   => "A new task has been created: \"{$this->task->title}\". Created by {$user}.",
        'updated'   => "The task has been updated: \"{$this->task->title}\". Updated by {$user}.",
        'approved' => "The task has been approved: \"{$this->task->title}\". Approved by {$user}.",
        'completed' => "Task completed successfully: \"{$this->task->title}\". Actual Cost: ৳ "
                        . number_format($this->task->actual_cost, 0)
                        . ". Completed by {$user}.",
    ];

    return [
        'task_id' => $this->task->id,
        'title'   => $this->task->title,
        'action'  => $this->action,
        'message' => $messages[$this->action],
        'status'  => $this->task->status,
    ];
}
}