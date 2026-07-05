<?php $__env->startSection('title','Create Group'); ?>


<?php $__env->startSection('body'); ?>

<div style="display:flex; min-height:100vh, flex-direction:column;">


       
    <?php echo $__env->make('layouts.sidebar', [
        'role'            => 'student',
        'user'            => auth()->user(),
        'enrolledCourses' => $enrolledCourses ?? collect(),
        'unreadCount'     => $unreadCount ?? 0,
        'notifCount'      => $notifCount  ?? 0,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<h1>Create New Group</h1>


<form method="POST" action="/groups">

<?php echo csrf_field(); ?>


<input 
type="text"
name="name"
placeholder="Group name"
style="padding:10px;width:300px;"
>


<br><br>


<textarea
name="description"
placeholder="Group description"
style="padding:10px;width:300px;height:100px;"
></textarea>


<br><br>


<button type="submit"
style="background:#1a3c8f;color:white;padding:10px 20px;border:none;">
Create
</button>


</form>

</div>
@endsectio
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\fave\resources\views/groups/create.blade.php ENDPATH**/ ?>