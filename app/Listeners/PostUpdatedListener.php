<?php

namespace App\Listeners;

use Exception;
use App\Mail\Post\PostTrashed;
use App\Mail\Post\PostApproved;
use App\Events\PostUpdated;
use App\Mail\Post\PostAwaitingApproval;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostUpdatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  PostUpdated  $event
     * @return void
     */
    public function handle(PostUpdated $event)
    {
        $action = $event->action;
        $post = $event->post;

        try {
            if ($action == 'Approved' && get_buzzy_config('POSTS_SEND_MAIL_APPROVED', true)) {
                Mail::to($post->user)->send(new PostApproved($post));
            } elseif ($action == 'Trash' && get_buzzy_config('POSTS_SEND_MAIL_DELETED', true)) {
                Mail::to($post->user)->send(new PostTrashed($post));
            } elseif ($action == 'Added' && get_buzzy_config('POSTS_SEND_MAIL_AWAIT_APPROVE', true)) {
                if ($post->approve === 'no') {
                    Mail::to(config('mail.from.address'))->send(new PostAwaitingApproval($post));
                }
            }
        } catch (Exception $e) {
            //
        }
    }
}
