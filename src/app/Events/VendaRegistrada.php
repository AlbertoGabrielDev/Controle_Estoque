<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Products\Models\Produto;

class VendaRegistrada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $produto;
    public function __construct(Produto $produto)
    {
        $this->produto = $produto;
    }

    public function broadcastOn()
    {
        return new Channel('vendas');
    }
}
