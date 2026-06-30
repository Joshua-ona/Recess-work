<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\PrivateComm;
use App\Events\MessageSent;

new class extends Component {

    public $users;
    public $selectedUser = null;
    public $message;
    public $messages = [];      
    public $loginId;

    public function mount()
    {
        $this->users = User::where('id', '!=', Auth::id())->get();
         $this->message= '';
         $this->loginId= Auth::id();
    }

   public function selectUser($id)
{
    $this->selectedUser = User::findOrFail($id);

    $this->messages = PrivateComm::where(function ($query) use ($id) {

        $query->where('sender_id', auth()->id())
              ->where('receiver_id', $id);

    })->orWhere(function ($query) use ($id) {

        $query->where('sender_id', $id)
              ->where('receiver_id', auth()->id());

    })
    ->orderBy('created_at')
    ->get();
}

   public function sendMessage()
{
    if (!$this->selectedUser || trim($this->message) == '') {
        return;
    }

    $message = PrivateComm::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $this->selectedUser->id,
        'content' => $this->message,
    ]);
    $this->messages->push($message);
    $this->message = '';
     $this->dispatch('message-sent');
   broadcast(new MessageSent($message))->toOthers();
    
}
    public function getListeners(){
        return[
            "echo-private:chat.{$this->loginId},MessageSent" => "newChatMessageNotification"
        ];
    }

   public function newChatMessageNotification($message)
{
    if (!$this->selectedUser) {
        return;
    }

    if ($message['sender_id'] == $this->selectedUser->id) {

        $messageObj = PrivateComm::find($message['id']);

        if ($messageObj) {
            $this->messages->push($messageObj);
        }
    }
}
    public function rendered()
{
    $this->dispatch('scroll-to-bottom');
}

}; ?>

<div class="chat-wrapper">

    {{-- Users --}}
    <div class="chat-users">

        <div class="chat-users-header">

            <input
                type="text"
                placeholder="Search users..."
                class="chat-search">

        </div>

        <div class="chat-users-list">

            @foreach($users as $user)

                <button
                    wire:click="selectUser({{ $user->id }})"
                    class="chat-user {{ $selectedUser && $selectedUser->id == $user->id ? 'active' : '' }}">

                    <div class="chat-avatar">
                      {{ strtoupper(substr($user->first_name, 0, 1)) }}
                    </div>

                    <div class="chat-user-info">

                        <div class="chat-user-name">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </div>

                        <div class="chat-user-email">
                            {{ $user->email }}
                        </div>

                    </div>

                </button>

            @endforeach

        </div>

    </div>


    {{-- Conversation --}}
    <div class="chat-main">

        {{-- Header --}}
        <div class="chat-header">

            @if($selectedUser)

                <div class="chat-avatar">
                    {{ strtoupper(substr($selectedUser->first_name,0,1)) }}
                </div>

                <div>

                    <div class="chat-user-name">
                        {{ $selectedUser->first_name }} {{ $selectedUser->last_name }}
                    </div>

                    <div class="chat-status">
                        Online
                    </div>

                </div>

            @else

                <div class="chat-empty-header">
                    Select a conversation
                </div>

            @endif

        </div>


        {{-- Messages --}}
<div id="chat-box" class="chat-messages overflow-y-auto">

@foreach($messages as $msg)

    @if($msg->sender_id == auth()->id())

        {{-- SENT --}}
        <div class="message-row sent">

            <div class="message-bubble sent-bubble">

                {{ $msg->content }}

                <div class="message-time">
                    {{ $msg->created_at->format('H:i') }}
                </div>

            </div>

        </div>


    @else

        {{-- RECEIVED --}}
        <div class="message-row received">

            <div class="message-bubble received-bubble">

                {{ $msg->content }}

                <div class="message-time">
                    {{ $msg->created_at->format('H:i') }}
                </div>

            </div>

        </div>

    @endif


@endforeach

</div>

        {{-- Input --}}
        <form
            wire:submit.prevent="sendMessage"
            class="chat-input">
<input
    type="text"
    id="message"
    name="message"
    wire:model="message"
    class="chat-input-field"
    placeholder="Type a message..."
    autocomplete="off">

            <button
                class="chat-send">

                Send

            </button>

        </form>

    </div>

</div>
<script>
document.addEventListener('livewire:init', () => {

    Livewire.on('scroll-to-bottom', () => {
        setTimeout(() => {
            const box = document.getElementById('chat-box');

            if (box) {
                box.scrollTop = box.scrollHeight;
            }
        }, 50);
    });

});
</script>