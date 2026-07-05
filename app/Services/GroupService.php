<?php
namespace App\Services;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class GroupService{
    public function create(array $data, User $creator): Group
    {
        if(Group::whereRaw('LOWER(name) = ?', [strtolower($data['name'])])->exists()){
            throw ValidationException::withMessages([
                'name' => 'A group named "'.$data['name'].'" already exists.',
            ]);
        }

        return DB::transaction(function() use ($data, $creator) {
            $group = Group::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'admin_id' =>$creator->id,
                'status' => 'pending',
            ]);

            $group->users()->attach($creator->id, ['role' => 'admin']);
            return $group;
        });
    }

    public function approve(Group $group): void 
    {
        $group->update(['status' => 'approved']);
    }

     public function reject(Group $group): void 
    {
        $group->update(['status' => 'rejected']);
    }

    public function getDiscoverableGroupsFor(User $user): Collection
    {
        return Group::where('status', 'approved')
                            ->whereDoesntHave('users', fn($q) => $q->where('user_id', $user->id))
                            ->latest()->get();
    }

    public function getMyGroups(User $user): Collection
    {
        return $user->groups()->withPivot('role')->latest()->get();
    }

    public function join(Group $group,User $user): void 
    {
        if($group->status !== 'approved'){
            abort(403, 'This group is not approved yet');
        }

        $group->users()->syncWithoutDetaching([
            $user->id => ['role'=>'member']
        ]);
    }

    public function isMember(Group $group, User $user): bool 
    { 
        return $group->users()->where('user_id', $user->id)->exists();
    }

}