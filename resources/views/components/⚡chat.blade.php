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

    foreach ($this->users as $user) {
        $user->unread_count = PrivateComm::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    $this->users = $this->users->sortByDesc('unread_count')->values();

    $this->message = '';
    $this->loginId = Auth::id();
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
  // Mark received messages as read
    PrivateComm::where('sender_id', $id)
        ->where('receiver_id', Auth::id())
        ->where('is_read', false)
        ->update([
            'is_read' => true
        ]);

        // Force scroll when opening a conversation
        $this->dispatch('force-scroll-to-bottom');
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
                'is_read' => false,
        ]);

        $this->messages = $this->messages->push($message);
        $this->message = '';

        // Auto‑scroll (only if near bottom)
        $this->dispatch('scroll-to-bottom');

        //broadcast(new MessageSent($message))->toOthers();
    }
   public function updateNewMessage()
{
    if (!$this->selectedUser) {
        return;
    }

    $this->dispatch(
        "userTyping",
        userID: $this->loginId,
        userName: Auth::user()->first_name,
        selectedUserId: $this->selectedUser->id
    );
}
    public function getListeners()
    {
        return [
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

            // Mark it as read immediately
            $messageObj->update([
                'is_read' => true
            ]);

            $this->messages = $this->messages->push($messageObj);

            $this->dispatch('scroll-to-bottom');
        }
    }
}
}
?>
<div class="chat-wrapper" data-login-id="{{ $loginId }}">

    {{-- Users --}}
    <div class="chat-users">

        <div class="chat-users-header">
            <input type="text" placeholder="Search users..." class="chat-search">
        </div>

        <div class="chat-users-list">

            @foreach($users as $user)

                <button
                    wire:click="selectUser({{ $user->id }})"
                    class="chat-user {{ $selectedUser && $selectedUser->id == $user->id ? 'active' : '' }}">

                    <div class="chat-avatar">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                    </div>

                   <div style="flex:1; min-width:0;">
    <div class="chat-user-name">
        {{ $user->first_name }} {{ $user->last_name }}
    </div>

    <div class="chat-user-email">
        {{ $user->email }}
    </div>
</div>

@if($user->unread_count > 0)
    <span class="sidebar-badge">
        {{ $user->unread_count }}
    </span>
@endif

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
                    <div style="font-size:13px; color:#2b8c4a;">Online</div>
                </div>

            @else

                <div style="color:#777; font-size:16px;">Select a conversation</div>

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
        <div id="typing-indicator" class=""></div>

        {{-- Input --}}
        <form wire:submit.prevent="sendMessage" class="chat-input">

            <input
                type="text"
                id="message"
                name="message"
                wire:model.live="message"
                 wire:input="updateNewMessage"
                placeholder="Type a message..."
                autocomplete="off">

            <button class="chat-send">
                Send
            </button>

        </form>

    </div>

</div>

<script>
    console.log('Script started');

 console.log("Livewire object", window.Livewire);
    const chatBox = document.getElementById('chat-box');

    function isNearBottom() {
        if (!chatBox) return true;
        const threshold = 100;
        const distanceToBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight;
        return distanceToBottom < threshold;
    }

    function scrollToBottom() {
        if (!chatBox) return;
        chatBox.scrollTo({
            top: chatBox.scrollHeight,
            behavior: 'smooth'
        });
    }

    // Auto‑scroll on new message – only if near bottom (WhatsApp style)
    Livewire.on('scroll-to-bottom', () => {
        if (isNearBottom()) {
            setTimeout(() => {
                scrollToBottom();
            }, 100);
        }
    });

    // Force scroll when opening a conversation
    Livewire.on('force-scroll-to-bottom', () => {
        setTimeout(() => {
            scrollToBottom();
        }, 150);
    });
  

   
console.log('Setting up Echo...');

const loginId = document.querySelector('.chat-wrapper').dataset.loginId;

console.log("Current login ID:", loginId);

window.Echo.private(`chat.${loginId}`)
    .listenForWhisper('typing', (event) => {

        const t = document.getElementById('typing-indicator');

        if (t) {
            t.innerText = `${event.userName} is typing...`;

            setTimeout(() => {
                t.innerText = '';
            }, 2000);
        }

        console.log(event);
    });
    Livewire.on('userTyping', (event) => {

    console.log("Typing event received from Livewire:", event);



    console.log("Whisper sent to:", `chat.${event.selectedUserId}`);});


console.log('Setting up done');
</script>