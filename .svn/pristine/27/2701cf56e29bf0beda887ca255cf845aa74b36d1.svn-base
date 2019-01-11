<div class="box">
    <div class="box-body box-profile">
	    <img class="profile-user-img img-responsive img-circle" src="<?php echo e(backpack_avatar_url(backpack_auth()->user())); ?>">
	    <h3 class="profile-username text-center"><?php echo e(backpack_auth()->user()->name); ?></h3>
	</div>

	<hr class="m-t-0 m-b-0">

	<ul class="nav nav-pills nav-stacked">

	  <li role="presentation"
		<?php if(Request::route()->getName() == 'backpack.account.info'): ?>
	  	class="active"
	  	<?php endif; ?>
	  	><a href="<?php echo e(route('backpack.account.info')); ?>"><?php echo e(trans('backpack::base.update_account_info')); ?></a></li>

	  <li role="presentation"
		<?php if(Request::route()->getName() == 'backpack.account.password'): ?>
	  	class="active"
	  	<?php endif; ?>
	  	><a href="<?php echo e(route('backpack.account.password')); ?>"><?php echo e(trans('backpack::base.change_password')); ?></a></li>

	</ul>
</div>
