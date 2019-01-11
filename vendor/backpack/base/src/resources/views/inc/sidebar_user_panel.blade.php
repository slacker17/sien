<div class="user-panel">
  <a class="pull-left image" href="{{ route('backpack.account.info') }}">
    <!--<img src="{{ backpack_avatar_url(backpack_auth()->user()) }}" class="img-circle" alt="User Image">-->
<?php if(Auth::user()->idescuelanormal == 1): ?>
      <img src="<?php echo e(asset('logos/cam.png')); ?>"  alt="User Image">

    <?php elseif(Auth::user()->idescuelanormal == 2): ?>
      <img src="<?php echo e(asset('logos/NormalPreescolar.png')); ?>"  alt="User Image">

    <?php elseif(Auth::user()->idescuelanormal == 3): ?>
      <img src="<?php echo e(asset('logos/NormalPrimariaTeacalco.png')); ?>"  alt="User Image">

    <?php elseif(Auth::user()->idescuelanormal == 4): ?>
      <img src="<?php echo e(asset('logos/NormalRural.jpg')); ?>"  alt="User Image">

    <?php elseif(Auth::user()->idescuelanormal == 5): ?>
      <img src="<?php echo e(asset('logos/NormalUrbanaFederal.png')); ?>" alt="User Image">
    <?php else: ?>
      <img src="<?php echo e(backpack_avatar_url(backpack_auth()->user())); ?>"  alt="User Image">
    <?php endif; ?>
  </a>
  <div class="pull-left info">
    <p><a href="{{ route('backpack.account.info') }}">{{ backpack_auth()->user()->name }}</a></p>
    
     <?php if(Auth::user()->hasRole('DIRECTOR')): ?>
  		<p>DIRECTOR</p>
  	<?php endif; ?>
  	<?php if(Auth::user()->hasRole('ADMINISTRATIVO')): ?>
  		<p>ADMINISTRATIVO</p>
  	<?php endif; ?>
  	<?php if(Auth::user()->hasRole('DOCENTE')): ?>
  		<p>DOCENTE</p>
  	<?php endif; ?>
  	<?php if(Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO')): ?>
  		<p>SUBDIRECTOR <br>ACADÉMICO</p>
  	<?php endif; ?>

    <!--<small><small><a href="{{ route('backpack.account.info') }}"><span><i class="fa fa-user-circle-o"></i> {{ trans('backpack::base.my_account') }}</span></a> &nbsp;  &nbsp; <a href="{{ backpack_url('logout') }}"><i class="fa fa-sign-out"></i> <span>{{ trans('backpack::base.logout') }}</span></a></small></small>-->
  </div>
</div>
