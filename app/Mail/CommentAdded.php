<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommentAdded extends Mailable
{
    use Queueable, SerializesModels;
    public $post;
    /**
     * Create a new message instance.
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    public function build()
    {
        return $this->subject('New Comment Added')
        ->html('<p>A new comment has been added to your post titled: ' . htmlspecialchars($this->post->title) .'</p>');
    }

}
