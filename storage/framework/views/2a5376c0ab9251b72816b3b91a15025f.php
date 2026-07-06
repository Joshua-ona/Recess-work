<?php $__env->startSection('title', 'Messages'); ?>

<?php $__env->startSection('body'); ?>

<div class="dash-wrap">

    <?php echo $__env->make('layouts.sidebar', [
    'role' => auth()->user()->role,
    'user' => auth()->user(),
    'enrolledCourses' => collect(),
    'unreadCount' => 0,
    'notifCount' => 0,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="dash-main">

        <header class="dash-header">
            <div>
                <div class="dash-header-title">
                    Messages
                </div>

                <div class="dash-header-sub">
                    Chat with students and lecturers
                </div>
            </div>
        </header>

        <div class="dash-body">

            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('chat', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-4266539739-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

        </div>

    </main>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Personal\Downloads\D\xampp\htdocs\Forum\resources\views\livewire/chat.blade.php ENDPATH**/ ?>