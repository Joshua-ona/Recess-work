<?php $__env->startSection('dash-body'); ?>
<div class="max-w-7xl mx-auto p-4">
    <h1 class="text-xl font-bold mb-4"><?php echo e($group->name); ?></h1>
    <div class="grid grid-cols-12 gap-4 h-[70vh]">
        
        <div class="col-span-12 md:col-span-3 bg-white rounded-lg border flex-col">
            <div class="p-3 border-b font-semibold text-sm">Members
                (<?php echo e($members->count() + 1); ?>)</div>
            <div class="p-3 space-y-2 overflow-y-auto">
                
                <div class="flex items-center gap-2 text-sm font-semibold pb-2 border-b">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center">A
                    </div>
                    <div>
                        <?php echo e($admin->name); ?>

                        <div class="text-xs text-blue-600 font-normal">Admin</div>
                    </div>
                </div>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <div class="w-7 h-7 rounded-full bg-gray-200"></div>
                    <?php echo e($m->name); ?>

                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
        
        <div class="col-span-12 md:col-span-9 bg-white rounded-lg border flex-col">
            <div class="p-3 border-b font-semibold text-sm">
                Group Chat
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="chat-box">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="text-sm">
                    <span class="font-semibold text-blue-700"><?php echo e($c->user->name); ?>:</span>
                    <span class="text-gray-800"><?php echo e($c->body); ?></span>
                    <div class="text-[10px] text-gray-400"><?php echo e($c->created_at->diffForHumans()); ?></div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-gray-500 text-center mt-10">No messages yet. Say hi
                    👋
                </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            
            <form method="POST" action="<?php echo e(route('student.groups.message', $group)); ?>" class="p-3 border-t">
                <?php echo csrf_field(); ?>
                <div class="flex gap-2">
                    <input name="body" placeholder="Type a message..." class="flex-1 border
rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required maxlength="1000">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 rounded-lg text-sm font-semibold">
                        Send
                    </button>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </form>
        </div>
    </div>
</div>
<?php echo $__env->make('student.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Recess-work\resources\views/student/groups/show.blade.php ENDPATH**/ ?>