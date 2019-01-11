<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

<!-- Solo para supersien -->
@if (Auth::user()->hasRole('Admin'))
<!-- Users, Roles Permissions -->
  <li class="treeview">
    <a href="#"><i class="fa fa-group"></i> <span>Users, Roles, Permissions</span> <i class="fa fa-angle-left pull-right"></i></a>
    <ul class="treeview-menu">
      <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user') }}"><i class="fa fa-user"></i> <span>Users</span></a></li>
      <li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/role') }}"><i class="fa fa-group"></i> <span>Roles</span></a></li>
      <!--<li><a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/permission') }}"><i class="fa fa-key"></i> <span>Permissions</span></a></li>-->
    </ul>
     <li><a href="{{ url(config('backpack.base.route_prefix').'/cicloescolar') }}"><i class="glyphicon glyphicon-calendar"></i> <span>Ciclo Escolar</span></a></li>
     <li><a href="{{ url(config('backpack.base.route_prefix').'/curso') }}"><i class="glyphicon glyphicon-calendar"></i> <span>Cursos</span></a></li>
     <li><a href="{{ url(config('backpack.base.route_prefix').'/planestudio') }}"><i class="glyphicon glyphicon-calendar"></i> <span>Planes de Estudio</span></a></li>
     <li><a href="{{ url(config('backpack.base.route_prefix').'/escuelanormal') }}"><i class="glyphicon glyphicon-calendar"></i> <span>Escuelas normales</span></a></li>
     <li><a href="fechasevaluacion"><i class="glyphicon glyphicon-calendar"></i> <span>Fechas de Evaluación</span></a></li>
  </li>
@endif
<!-- Menu Administrativo -->
@if (Auth::user()->hasRole('ADMINISTRATIVO'))
     <!-- Profesores -->
	<!-- <li><a href="{{ url(config('backpack.base.route_prefix').'/profesor') }}"><i class="glyphicon glyphicon-user"></i> <span>Docentes</span></a></li> -->
     <li><a href="{{ url(config('backpack.base.route_prefix').'/profesor') }}"><i class="glyphicon glyphicon-user"></i> <span>Docentes</span></a></li>
	<!--Alumnos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/alumno/create') }}"><i class="glyphicon glyphicon-education"></i> <span>Añadir Alumno</span></a></li>
     <!-- Grupos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/grupo') }}"><i class="glyphicon glyphicon-th-list"></i> <span>Grupos</span></a></li>
     <!-- Certificados -->
    <li><a href="{{ url(config('backpack.base.route_prefix').'/alumno?certificado=1') }}"><i class="glyphicon glyphicon-qrcode"></i> <span>Certificados</span></a></li>
@endif


<!-- Menu Administrativo -->
@if (Auth::user()->hasRole('SUBDIRECTOR ACADÉMICO'))
	<!-- Cursos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/curso') }}"><i class="glyphicon glyphicon-list-alt"></i> <span>Cursos</span></a></li>
	<!-- Grupos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/grupo') }}"><i class="glyphicon glyphicon-th-list"></i> <span>Grupos</span></a></li>
	<!-- Asigna Docente Curso -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/asignadocentecurso') }}"><i class="glyphicon glyphicon-education"></i> <span>Asignar docentes a cursos</span></a></li>
	<li><a href="{{ url(config('backpack.base.route_prefix').'/administrativo') }}"><i class="glyphicon glyphicon-list-alt"></i> <span>Administrativos</span></a></li>
@endif

@if (Auth::user()->hasRole('DOCENTE'))
    <!-- Grupos -->
	<li><a href="/public/admin/cargahoraria/{{Auth::user()->id}}/1/"><i class="glyphicon glyphicon-th-list"></i> <span>Horario</span></a></li>
	<!-- Grupos 
	<li><a href="{{ url(config('backpack.base.route_prefix').'/grupo') }}"><i class="glyphicon glyphicon-th-list"></i> <span>Grupos</span></a></li>-->
     <!-- Cursos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/curso') }}"><i class="glyphicon glyphicon-list-alt"></i> <span>Cursos Asignados</span></a></li>
@endif


     @if (Auth::user()->hasRole('DIRECTOR'))
     <!--Profesores -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/profesor') }}"><i class="glyphicon glyphicon-user"></i> <span>Docentes</span></a></li> 
	<!--Alumnos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/alumno') }}"><i class="glyphicon glyphicon-education"></i> <span>Alumnos</span></a></li>
     <!-- Grupos -->
	<li><a href="{{ url(config('backpack.base.route_prefix').'/grupo') }}"><i class="glyphicon glyphicon-th-list"></i> <span>Grupos</span></a></li>

     @endif
