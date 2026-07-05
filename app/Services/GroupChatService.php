<?php
namespace App\Services;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GroupChatService{

    public function ensureMember(Group $group, User $user): void
    {
        abort_unless($group->users()->where('user_id',$user->id)->exists(), 403, 'Your not a member of this group');
    }

    public function getChatData(Group $group): array
    {

        $admin = $group->admin;
        $members = $group->users()->where('user_id', '!=', $group->admin_id)->get();
        $messages = $group->messages()->with('user')->get();

        return compact('admin','members','messages');
        
    }

    public function postMessage(Group $group,User $user, string $body): void
    {
        $this->ensureMember($group,$user);
        $group->messages()->create([
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }

}