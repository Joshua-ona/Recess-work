<?php
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\PrivateComm;
use App\Events\MessageSent;
?>

<div class="chat-wrapper">

    
    <div class="chat-users">

        <div class="chat-users-header">
            <input type="text" placeholder="Search users..." class="chat-search">
        </div>

        <div class="chat-users-list">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

                <button
                    wire:click="selectUser(<?php echo e($user->id); ?>)"
                    class="chat-user <?php echo e($selectedUser && $selectedUser->id == $user->id ? 'active' : ''); ?>">

                    <div class="chat-avatar">
                        <?php echo e(strtoupper(substr($user->first_name, 0, 1))); ?>

                    </div>

                    <div style="flex:1; min-width:0;">
                        <div class="chat-user-name">
                            <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>

                        </div>
                        <div class="chat-user-email">
                            <?php echo e($user->email); ?>

                        </div>
                    </div>

                </button>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        </div>

    </div>

    
    <div class="chat-main">

        
        <div class="chat-header">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedUser): ?>

                <div class="chat-avatar">
                    <?php echo e(strtoupper(substr($selectedUser->first_name,0,1))); ?>

                </div>

                <div>
                    <div class="chat-user-name">
                        <?php echo e($selectedUser->first_name); ?> <?php echo e($selectedUser->last_name); ?>

                    </div>
                    <div style="font-size:13px; color:#2b8c4a;">Online</div>
                </div>

            <?php else: ?>

                <div style="color:#777; font-size:16px;">Select a conversation</div>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>

        
        <div id="chat-box" class="chat-messages overflow-y-auto">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($msg->sender_id == auth()->id()): ?>

                    
                    <div class="message-row sent">
                        <div class="message-bubble sent-bubble">
                            <?php echo e($msg->content); ?>

                            <div class="message-time">
                                <?php echo e($msg->created_at->format('H:i')); ?>

                            </div>
                        </div>
                    </div>

                <?php else: ?>

                    
                    <div class="message-row received">
                        <div class="message-bubble received-bubble">
                            <?php echo e($msg->content); ?>

                            <div class="message-time">
                                <?php echo e($msg->created_at->format('H:i')); ?>

                            </div>
                        </div>
                    </div>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        </div>

        
        <form wire:submit.prevent="sendMessage" class="chat-input">

            <input
                type="text"
                id="message"
                name="message"
                wire:model="message"
                placeholder="Type a message..."
                autocomplete="off">

            <button class="chat-send">
                Send
            </button>

        </form>

    </div>

</div><?php /**PATH C:\xampp\htdocs\fave\storage\framework\views/livewire/views/bb5fdb75.blade.php ENDPATH**/ ?>