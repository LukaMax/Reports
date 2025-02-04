<div>
    <input type="text" wire:model="search" placeholder="Search users..."/>

    <table>
        <thead>
        <tr>
            <th wire:click="sortBy('name')">Name</th>
            <th wire:click="sortBy('email')">Email</th>
        </tr>
        </thead>
        <tbody>
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($user->name); ?></td>
                <td><?php echo e($user->email); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </tbody>
    </table>
</div>
<?php /**PATH C:\OSPanel\home\reports.ru\resources\views/livewire/users-table.blade.php ENDPATH**/ ?>