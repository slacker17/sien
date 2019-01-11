<?php if(backpack_auth()->check()): ?>
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <?php echo $__env->make('backpack::inc.sidebar_user_panel', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
          
          <!-- ================================================ -->
          <!-- ==== Recommended place for admin menu items ==== -->
          <!-- ================================================ -->

          <?php echo $__env->make('backpack::inc.sidebar_content', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

          <!-- ======================================= -->
          
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
<?php endif; ?>
