<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Fpdf;
use \Fpdf\makefont\makefont;
use URL;
use App\Http\Controllers\FpdfTableController;
use App\Models\Alumno;
use App\Calificacion;
use App\Models\Grupo;
use App\Models\Curso;
use App\User;
use App\CalificacionFinal;
use App\Models\Grupo_curso_user;
use App\Models\Escuelanormal;
use Auth;
use Barryvdh\DomPDF\Facade as PDF;
use DB;
use \QRCode;
use App\Http\Controllers\PdfHtmlController;
use App\Http\Controllers\CargaHorariaController;
use DateTime;
use DateTimeZone;

class FormatosController extends Controller
{

        
    /*
    * Formato de inscripcion / reinscripcion
    * @paramns idAlumno: identificador de alumno
    */
    public function inscripcion($idAlumno = 27){
        $alumno = Alumno::find($idAlumno); // Obtenemos Alumno
        $normal = Escuelanormal::find($alumno->idescuelanormal);
        $logo = 'cam.jpg';

	$licenciatura = $normal->licenciatura;

        // Sacamos logo
        if($alumno->idescuelanormal == 1){ // CAM
            $logo = 'cam.jpg';
	    $licenciatura = " CON ".$alumno->grupos->licenciatura;            
        }else if($alumno->idescuelanormal == 2) // Normal Preescolar
            $logo = 'NormalPreescolar.jpg';            
        else if($alumno->idescuelanormal == 3) // Normal Primaria Teacalco
            $logo = 'NormalPrimariaTeacalco.jpg';            
        else if($alumno->idescuelanormal == 4) // Normal rural
            $logo = 'NormalRural.jpg';            
        else if($alumno->idescuelanormal == 5) // Normal Urbana Federal
            $logo = 'NormalUrbanaFederal.jpg';            
	
        // Obtenemos subdirector academico
        $rol = "SUBDIRECTOR ACADÉMICO";
        $subdirector = User::where('idescuelanormal', $alumno->idescuelanormal)
            ->whereHas('roles',
            function($query) use ($rol){
                $query->where('name', $rol);
            })
            ->first();
            
            // Variables
    	$encabezado1 = "UNIDAD DE SERVICIOS EDUCATIVOS DEL ESTADO DE TLAXCALA";
    	$encabezado2 = "DIRECCIÓN DE EDUCACIÓN TERMINAL";
    	$encabezado3 = strpos($normal->nombre, "NORMAL") ? "ESCUELA ".$normal->nombre : $normal->nombre;
    	$encabezado4 = $normal->nombre_personaje != null ? "\"".mb_strtoupper($normal->nombre_personaje)."\"" : "";
    	$encabezado5 = "CONTROL ESCOLAR";
    	$encabezado6 = "LICENCIATURA EN EDUCACIÓN ".$licenciatura.", MODALIDAD ESCOLARIZADA";
    	$encabezado7 = "FORMATO DE INSCRIPCIÓN";
        $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = utf8_decode(strtoupper($normal->municipio_estado)." A "." ".mb_strtoupper(date('d')." de ".$meses[date('n')-1]. " del ".date('Y')));
    	
    	// TRextos de enmedio
    	$text1 = "NORMAS DE CONTROL ESCOLAR PARA LAS INSTITUCIONES FORMADORAS DE DOCENTES EN LAS LICENCIATURAS DE EDUCACIÓN BÁSICA, MODALIDAD ESCOLARIZADA, INSCRIPCIÓN";
    	$text2 = "Serán sujetos de reinscripción al semestre inmediato superior:";
    	$text3 = "a) Los alumnos regulares.";
    	$text4 = "b) Los alumnos irregulares que después del primer periodo de regularización inmediato al término del semestre, adeuden como máximo dos asignaturas.";
    	$text5 = "c) Los alumnos en situación de baja temporal, que no exceda de un ciclo escolar y sean de la misma escuela.";
    	$text6 = "7. El alumno de primero a sexto semestre que acumule de tres a cuatro asignaturas sin acreditar, después del primer periodo de regularización inmediato al término del semestre, causará baja temporal de dos semestres consecutivos y podrá solicitar su reinscripción, concluido el plazo establecido, siempre y cuando se haya regularizado totalmente, con base en las oportunidades que se otorgan de acuerdo con el porcentaje de asistencias. En caso de acumular cinco o más asignaturas, será dado de baja definitiva.";
    	$text7 = "Para el caso de séptimo y octavo semestres, la baja temporal se aplicará cuando el alumno no acredite el Trabajo Docente y, por consiguiente, la asignatura correspondiente al análisis del trabajo docente, o viceversa, ya que ambas asignaturas se complementan para el logro de sus propósitos, por lo que deberá regularizarlas mediante el recursamiento y la acreditación en el periodo correspondiente en el entendido que debe cursar séptimo y octavo semestres en forma consecutiva.";
    	$text8 = "8. Los alumnos que adeuden asignaturas de primero a sexto semestre no podrán ser reinscritos a séptimo semestre, dado que los seis primeros semestres son formativos para la práctica intensiva que se cursa durante el 7° y el 8° semestres.";
    	$text9 = "9. Una vez acreditado el séptimo semestre el alumno no podrá solicitar la baja temporal a fin de darle continuidad a la secuencia pedagógica en la elaboración de su documento recepcional y el logro de los propósitos formativos de séptimo y octavo semestres.";
    	$text10 = "10. El alumno que no asista durante dos semestres consecutivos sin causa justificada, será dado de baja definitiva.";
    	$text11 = "11. Es responsabilidad del alumno notificar por escrito a la escuela el periodo y las razones para ausentarse de la misma, a fin de que no se aplique lo dispuesto en la norma anterior.";

    	$pdf = new Fpdf('P', 'mm', 'letter');
 		$pdf::AddPage();
 		$pdf::SetFont('Arial','B',8);
 		$pdf::Image('logos/sepe.jpg',8,6,50);
 		$pdf::Image('logos/'.$logo,185,6,13);
 		$pdf::setXY(50,6);
 		$pdf::cell(117,4,$encabezado1,0,1,"C");
 		$pdf::setXY(50,10);
 		$pdf::cell(117,4,utf8_decode($encabezado2),0,1,"C");
 		$pdf::setXY(50,14);
 		$pdf::cell(117,4,utf8_decode($encabezado3),0,1,"C");
 		$pdf::setXY(50,18);
 		$pdf::cell(117,4,utf8_decode($encabezado4),0,1,"C");
 		$pdf::setXY(50,22);
 		$pdf::cell(117,4,utf8_decode($encabezado5),0,1,"C");
 		$pdf::setXY(50,28);
 		$pdf::cell(117,4,utf8_decode($encabezado6),0,1,"C");
 		$pdf::setXY(50,34);
 		$pdf::SetFont('Arial','B',10);
 		$pdf::cell(117,4,utf8_decode($encabezado7),0,1,"C");
 		$pdf::setXY(50,44);
 		$pdf::SetFont('Arial','',8);
 		$pdf::cell(150,4,utf8_decode($fecha),0,1,"R");

 		//$pdf::setXY(50,44);
 		// Fotografia
 		$pdf::Rect(10,30,22,25);
 		//$pdf::cell(55,4,utf8_decode('FOTOGRAFÍA'),1,1,"C");

 		$saltoy = 10;
 		// Nmobre
 		$pdf::setXY(37,42+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(55,4,utf8_decode($alumno->nombre),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(37,43+$saltoy);
 		$pdf::cell(163,4,'________________________________________________________________________________________________________',0,1,"C");
 		$pdf::setXY(37,47+$saltoy);
 		$pdf::cell(55,4,utf8_decode('NOMBRE(S)'),0,1,"L");
 		// APP
 		$pdf::setXY(92,42+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(55,4,utf8_decode($alumno->app),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(92,47+$saltoy);
 		$pdf::cell(55,4,utf8_decode('APELLIDO PATERNO'),0,1,"L");
 		// APM
 		$pdf::setXY(147,42+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(55,4,utf8_decode($alumno->apm),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(147,47+$saltoy);
 		$pdf::cell(55,4,utf8_decode('APELLIDO MATERNO'),0,1,"L");

 		$pdf::setXY(8,53+$saltoy);
 		$pdf::cell(193,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		// curp
 		$pdf::setXY(8,52+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->curp),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,57+$saltoy);
 		$pdf::cell(39,4,utf8_decode('CURP'),0,1,"L");
 		//mATRICULA
 		$pdf::setXY(47,52+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->matricula),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(47,57+$saltoy);
 		$pdf::cell(39,4,utf8_decode('MATRICULA'),0,1,"L");
 		//ciclo escolar
 		$pdf::setXY(86,52+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->grupos->cicloescolar->descripcion),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(86,57+$saltoy);
 		$pdf::cell(39,4,utf8_decode('CICLO ESCOLAR'),0,1,"L");
 		//SEMESTRE
 		$pdf::setXY(125,52+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->grupos->semestre),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(125,57+$saltoy);
 		$pdf::cell(39,4,utf8_decode('SEMESTRE'),0,1,"L");
 		//GRUPO
 		$pdf::setXY(164,52+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->grupos->grupo),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(164,57+$saltoy);
 		$pdf::cell(39,4,utf8_decode('GRUPO'),0,1,"L");

 		$pdf::setXY(8,63+$saltoy);
 		$pdf::cell(193,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		// CALLE Y NUMERO
 		$pdf::setXY(8,62+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(100,4,utf8_decode($alumno->domicilio),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,67+$saltoy);
 		$pdf::cell(100,4,utf8_decode('CALLE Y NÚMERO'),0,1,"L");
 		// tel celular
 		$pdf::setXY(108,62+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(45,4,utf8_decode($alumno->celular),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(108,67+$saltoy);
 		$pdf::cell(45,4,utf8_decode('TEL. CELULAR'),0,1,"L");
 		// telefono
 		$pdf::setXY(153,62+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(45,4,$alumno->telefono,0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(153,67+$saltoy);
 		$pdf::cell(45,4,utf8_decode('TELÉFONO'),0,1,"L");

 		$pdf::setXY(8,73+$saltoy);
 		$pdf::cell(193,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		// colonia
 		$pdf::setXY(8,72+$saltoy);$pdf::SetFont('Arial','B',7);
 		$pdf::cell(39,4,utf8_decode(mb_strtoupper($alumno->localidad)),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,77+$saltoy);
 		$pdf::cell(39,4,utf8_decode('COLONIA'),0,1,"L");
 		//poblacion
 		$pdf::setXY(47,72+$saltoy);$pdf::SetFont('Arial','B',7);
 		$pdf::cell(39,4,utf8_decode(mb_strtoupper($alumno->municipio)),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(47,77+$saltoy);
 		$pdf::cell(39,4,utf8_decode('POBLACIÓN'),0,1,"L");
 		//municipio
 		$pdf::setXY(86,72+$saltoy);$pdf::SetFont('Arial','B',7);
 		$pdf::cell(39,4,utf8_decode(mb_strtoupper($alumno->municipio)),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(86,77+$saltoy);
 		$pdf::cell(39,4,utf8_decode('MUNICIPIO'),0,1,"L");
 		//estado
 		$pdf::setXY(125,72+$saltoy);$pdf::SetFont('Arial','B',7);
 		$pdf::cell(39,4,utf8_decode(mb_strtoupper($alumno->estado)),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(125,77+$saltoy);
 		$pdf::cell(39,4,utf8_decode('ESTADO'),0,1,"L");
 		//email
 		$pdf::setXY(164,72+$saltoy);$pdf::SetFont('Arial','B',6);
 		$pdf::cell(39,4,utf8_decode($alumno->correo),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(164,77+$saltoy);
 		$pdf::cell(39,4,utf8_decode('E-MAIL'),0,1,"L");

 		$pdf::setXY(8,83+$saltoy);
 		$pdf::cell(193,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		// estado civil
 		$pdf::setXY(8,82+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(48,4,utf8_decode($alumno->estado_civil),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,87+$saltoy);
 		$pdf::cell(48,4,utf8_decode('ESTADO CIVIL'),0,1,"L");
 		// sexo
 		$pdf::setXY(56,82+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(48,4,utf8_decode($alumno->sexo),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(56,87+$saltoy);
 		$pdf::cell(48,4,utf8_decode('SEXO'),0,1,"L");
 		// SERVICIO MEDICO
 		$pdf::setXY(104,82+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(48,4,utf8_decode($alumno->servicio_medico),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(104,87+$saltoy);
 		$pdf::cell(48,4,utf8_decode('SERVICIO MÉDICO'),0,1,"L");
 		//otro
 		$pdf::setXY(152,82+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(48,4,utf8_decode(''),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(152,87+$saltoy);
 		$pdf::cell(48,4,utf8_decode('OTRO'),0,1,"L");

 		$pdf::setXY(8,93+$saltoy);
 		$pdf::cell(193,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		// eN CASO DE ACCIDENTE AVISAR A
 		$pdf::setXY(8,92+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(150,4,utf8_decode($alumno->contacto_accidente),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,97+$saltoy);
 		$pdf::cell(55,4,utf8_decode('EN CASO DE ACCIDENTE AVISAR A'),0,1,"L");

 		$pdf::setXY(8,103+$saltoy);
 		$pdf::cell(193,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		// telefono
 		$pdf::setXY(8,102+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(28,4,$alumno->tel_accidente,0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,107+$saltoy);
 		$pdf::cell(28,4,utf8_decode('TELÉFONO'),0,1,"L");
 		// DIRECCION
 		$pdf::setXY(36,102+$saltoy);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(100,4,utf8_decode($alumno->direccion_accidente),0,1,"L");
 		$pdf::SetFont('Arial','',8);
 		$pdf::setXY(36,107+$saltoy);
 		$pdf::cell(100,4,utf8_decode('DIRECCIÓN'),0,1,"L");

 		// Parrafos
 		$pdf::setXY(8,126);
 		$pdf::MultiCell(195,3,utf8_decode($text1),0,"C");
 		$pdf::SetFont('Arial','',8);
		$pdf::setXY(8,136);
 		$pdf::MultiCell(195,3,utf8_decode($text2."\n".$text3."\n".$text4."\n".$text5),0,"L");
 		$x = $pdf::GetX(); $y = $pdf::GetY(); 
 		$pdf::setXY(8,$y+2);
 		$pdf::MultiCell(195,3,utf8_decode($text6),0,"L");
 		$y = $pdf::GetY(); 
 		$pdf::setXY(8,$y+2);
 		$pdf::MultiCell(195,3,utf8_decode($text7."\n".$text8."\n".$text9."\n".$text10),0,"L");
 		$y = $pdf::GetY(); 
 		$pdf::setXY(8,$y+2);
 		$pdf::MultiCell(195,3,utf8_decode($text11),0,"L");
 		
 		$yres = $pdf::GetY();
 		$pdf::setXY(8,$y+20);
 		// Firmas Vo. Bo.
 		$pdf::cell(97,4,'Alumno',0,1,"C");
 		$pdf::setXY(105,$y+15);
		$puesto = $subdirector->puesto != null ? $subdirector->puesto : "SUBDIRECTOR ACADÉMICO";
 		$pdf::MultiCell(97,3,utf8_decode("Vo. Bo.\n".$subdirector->name." ".$subdirector->app." ".$subdirector->apm."\n".$puesto),0,"C");

 		$y = $yres;
 		$pdf::setXY(8,$y+15);
 		$pdf::cell(195,4,'____________________________________________________________________________________________________________________________',0,1,"C");


 		$y = $pdf::GetY();
 		$pdf::Image('logos/sepe.jpg',8,$y+2,50);
 		$pdf::Image('logos/'.$logo,185,$y+2,13);

 		// Segundo encabezado
 		$pdf::SetFont('Arial','B',8);
 		$pdf::setXY(50,$y+3);$y = $pdf::GetY();
 		$pdf::cell(117,4,$encabezado1,0,1,"C");
 		$pdf::setXY(50,$y+3);$y = $pdf::GetY();
 		$pdf::cell(117,4,utf8_decode($encabezado2),0,1,"C");
 		$pdf::setXY(50,$y+3);$y = $pdf::GetY();
 		$pdf::cell(117,4,utf8_decode($encabezado3),0,1,"C");
 		$pdf::setXY(50,$y+3);$y = $pdf::GetY();
 		$pdf::cell(117,4,utf8_decode($encabezado4),0,1,"C");
 		$pdf::setXY(50,$y+3);$y = $pdf::GetY();
 		$pdf::cell(117,4,utf8_decode($encabezado5),0,1,"C");
 		$pdf::setXY(50,$y+4);$y = $pdf::GetY();
 		$pdf::cell(117,4,utf8_decode($encabezado6),0,1,"C");
 		$pdf::setXY(50,$y+4);$y = $pdf::GetY();$pdf::SetFont('Arial','B',9);
 		$pdf::cell(117,4,utf8_decode($encabezado7),0,1,"C");$y = $pdf::GetY();
 		$pdf::setXY(50,$y+2);
 		$pdf::SetFont('Arial','',8);
 		$pdf::cell(150,4,utf8_decode($fecha),0,1,"R");

 		////////////////////////////////////////////////////////////////////
 		$y = $pdf::GetY();
 		$y = $y+5;
 		$pdf::setXY(8,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(65,4,utf8_decode($alumno->nombre),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,$y);
 		$pdf::cell(195,4,'____________________________________________________________________________________________________________________________',0,1,"C");
 		$pdf::setXY(8,$y+4);
 		$pdf::cell(65,4,utf8_decode('NOMBRE(S)'),0,1,"L");
 		// APP
 		$pdf::setXY(73,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(65,4,utf8_decode($alumno->app),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(73,$y+4);
 		$pdf::cell(65,4,utf8_decode('APELLIDO PATERNO'),0,1,"L");
 		// APM
 		$pdf::setXY(138,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(65,4,utf8_decode($alumno->apm),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(138,$y+4);
 		$pdf::cell(65,4,utf8_decode('APELLIDO MATERNO'),0,1,"L");

 		$y = $pdf::GetY();
 		$y = $y+4;

 		$pdf::setXY(8,$y);
 		$pdf::cell(195,4,'___________________________________________________________________________________________________________________________',0,1,"C");
 		
 		// curp
 		$pdf::setXY(8,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->curp),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(8,$y+4);
 		$pdf::cell(39,4,utf8_decode('CURP'),0,1,"L");
 		//mATRICULA
 		$pdf::setXY(47,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->matricula),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(47,$y+4);
 		$pdf::cell(39,4,utf8_decode('MATRICULA'),0,1,"L");
 		//ciclo escolar
 		$pdf::setXY(86,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->grupos->cicloescolar->descripcion),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(86,$y+4);
 		$pdf::cell(39,4,utf8_decode('CICLO ESCOLAR'),0,1,"L");
 		//SEMESTRE
 		$pdf::setXY(125,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->grupos->semestre),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(125,$y+4);
 		$pdf::cell(39,4,utf8_decode('SEMESTRE'),0,1,"L");
 		//GRUPO
 		$pdf::setXY(164,$y-1);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(39,4,utf8_decode($alumno->grupos->grupo),0,1,"L");$pdf::SetFont('Arial','',8);
 		$pdf::setXY(164,$y+4);
 		$pdf::cell(39,4,utf8_decode('GRUPO'),0,1,"L");
 		
 		$pdf::Output();
 		exit;
    }
    
    /*
    * Impresion de tira de calificaciones semestral (principalmente para docentes)
    * @params idGrupo: Grupo a buscar, idCurso: El curso a mostrar
    */
    public function asistenciaYevaluacion($idGrupo, $idCurso){
        $logo = 'cam.jpg';
        // Sacamos logo
        if(Auth::user()->idescuelanormal == 1) // CAM
            $logo = 'cam.jpg';            
        if(Auth::user()->idescuelanormal == 2) // Normal Preescolar
            $logo = 'NormalPreescolar.jpg';            
        if(Auth::user()->idescuelanormal == 3) // Normal Primaria Teacalco
            $logo = 'NormalPrimariaTeacalco.jpg';            
        if(Auth::user()->idescuelanormal == 4) // Normal rural
            $logo = 'NormalRural.jpg';            
        if(Auth::user()->idescuelanormal == 5) // Normal Urbana Federal
            $logo = 'NormalUrbanaFederal.jpg';            
        

        // Obtenemos el listado de los alumnos que pertenecen al grupo seleccionado
    	$alumnos = Alumno::where('idgrupo', (int)$idGrupo)
                                ->orderBy('app')->orderBy('apm')->orderBy('nombre')
                                ->get();
        $grupo = Grupo::find($idGrupo); // Get grupo data
        $curso = Curso::find($idCurso); // Get curso data

        $calificaciones = array();// Array final que contendra las calificaciones

        // Obtenemos calificaciones de cada alumno (recorriendo el array de alumnos)
        // Para eso necesitamos el id_curso y sus unidades (parciales)
        foreach ($alumnos as $alumno) {
            // Calificaciones del alumno actual (id_alumno, id_curso)
            $current_alumno_califs = Calificacion::where('id_alumno', (int) $alumno->id_Alumno)
                                               ->where('id_curso', (int) $idCurso)
                                               ->orderBy('unidad')->get();

            $array_califs = [];
            $array_asis = [];
            // Llenamos incialmente array de calificaciones
            for($i=1; $i <= $curso->numeroUnidades; $i++){
                // Variable auxiliar que ayuda a verificar si se inserto la calificacion de la unidad actual
                $existe_calif_actual_registrada = false;
                $existe_asis_actual_registrada = false;
                // Recorremos las calificcaciones que puedan estar registradas del alumno actual
                foreach($current_alumno_califs as $calif){
                    // Si estamos en la unidad actual (representada pr la variable $i)
                    if($i == $calif->unidad){
                        // Sabemos que hay registros de esa unidad, ahora hay que checar si hay registro de calif, asistencia o ambos
                        if($calif->calificacion != null){ // si hay calif registrada
                            $array_califs[$i] =  (int)$calif->calificacion; //
                            $existe_calif_actual_registrada = true;
                        }
                        if($calif->asistencia != null){
                            $array_asis[$i] =  (int)$calif->asistencia; //
                            $existe_asis_actual_registrada = true;
                        }
                            
                         // Decimos q se inserto la unidad actual
                        break; // Rompemos bucle
                    }
                }

                // Sino existe registro de calif actual, insertamos un cero
                if(!$existe_calif_actual_registrada)
                    $array_califs[$i] = 0;
                // Sino existe registro de asistencia actual, insertamos un cero
                if(!$existe_asis_actual_registrada)
                    $array_asis[$i] = 0;
            }

            // Sacamospromedio general
            $promedio=array_sum($array_califs)/count($array_califs);
            // Sacamos porcentaje de asistencia
            $asistencia=array_sum($array_asis)/count($array_asis);

            // Extraemos los decimales del porcentaje de asistencia
            $cadasis = explode('.', $asistencia);

            // Extraemos los decimales del promedio para generar las letras
            $cadprom = explode('.', (string)$promedio);

            if(sizeof($cadasis) > 1){ // Si hay punto decimal
                if(strlen($cadasis[0]) <= 1){
                    $asistencia = $cadasis[0].$cadasis[1][1];
                }else if(strlen($cadasis[0]) > 1){
                    $asistencia = $cadasis[0];
                }
            }else{ // Si no hay punto decimal
                if(strlen($cadasis[0]) <= 1){
                    $asistencia = $cadasis[0]."0";
                }else if(strlen($cadasis[0]) > 1){
                    $asistencia = $cadasis[0];
                }
            }
            /*
            if(strlen($cadasis[0]) <= 1){
                $asistencia = $cadasis[0].$cadasis[1][1];
            }else if(strlen($cadasis[0]) > 1){
                $asistencia = $cadasis[0];
                }*/

            $evaluacion_letra = ''; // Evaluacion global en letra
            
            // Generamos proemdio en letra
            if(sizeof($cadprom) > 1){ // Si hay punto decimal
                $evaluacion_letra = $this->generarNumero($cadprom[0][0]).
                    " PUNTO ".
                    $this->generarNumero($cadprom[1][0]);
                $promedio = $cadprom[0].".".$cadprom[1][0];
            }else{// Si no
                $evaluacion_letra = $this->generarNumero($cadprom[0]);
                $promedio = $cadprom[0];
            }
            
            // Agregamos las calificaciones del alumno actual a la global
            array_push($calificaciones, [
                $alumno->app." ".$alumno->apm." ".$alumno->nombre,
                $array_califs,
                $asistencia."%",
                $promedio,
                $evaluacion_letra
            ]); 
        }

        
    	// variables
    	$no_unidades = $curso->numeroUnidades;
        $alumnos = $calificaciones;
        
    	$encabezado1 = "SECRETARÍA DE EDUCACIÓN PÚBLICA";
    	$encabezado2 = "SUBDIRECCIÓN GENERAL DE EDUCACIÓN TERMINAL";
    	$encabezado3 = "ESCUELA NORMAL URBANA FEDERAL DE TLAXCALA";
    	$encabezado4 = "\"LIC. EMILIO SANCHEZ PIEDRAS\"";
    	$encabezado5 = "SUBDIRECCIÓN ACADÉMICA";
    	$encabezado6 = "LICENCIATURA EN EDUCACIÓN PRIMARIA";
    	$encabezado7 = "CONTROL ESCOLAR";
    	$encabezado8 = "CICLO ESCOLAR 2017-2018";
    	$encabezado9 = "REGISTRO DE ASISTENCIA Y EVALUACIÓN";

    	// Especificaciones
    	$especificacion1 = "1.SI EL CURSO CONSTA DE MENOS DE CUATRO UNIDADES DE APRENDIZAJE, SE CANCELARÁN LOS ESPACIOS EN BLANCO DEL RESTO DE UNIDADES.";
    	$especificacion2 = "2.LA EQUIVALENCIA NÚMERICA DE NIVEL DE DESEMPEÑO SE PLASMARÁ CON ENTEROS.";
    	$especificacion3 = "3.LA ACREDITACIÓN DE CADA UNIDAD DE APRENDIZAJE SERÁ CONDICIÓN PARA QUE EL ESTUDIANTE TENGA DERECHO A LA EVALUACIÓN GLOBLAL.";
    	$especificacion4 = "4.EL ESTUDIANTE DEBERÁ DEMOSTRAR EL LOGRO DE LAS COMPETENCIAS DE CADA UNIDAD DE APRENDIZAJE CON MÍNIMO DE DESEMPEÑO DE 7.";
    	$especificacion5 = "5.LA EVALUACIÓN GLOBAL TENDRÁ UN VALOR DEL 100% Y SERÁ ACREDITADO CUANDO OBTENGA COMO MÍNIMO 6.";
    	$especificacion6 = "6.EL ALUMNO TENDRÁ DERECHO A LA ACREDITACIÓN DEL CURSO CUANDO ASISTA COMO MÍNIMO EL 85% DEL TIEMPO ESTABLECIDO PARA EL MISMO.";


    	$pdf = new Fpdf('P', 'mm', 'letter');
 		$pdf::AddPage();
 		$pdf::SetFont('Arial','B',10);
 		$pdf::Image('logos/sepe.jpg',8,10,50);
 		$pdf::Image('logos/'.$logo,185,10,10);

 		$pdf::setXY(60,10);
 		$pdf::MultiCell(97,3,utf8_decode($encabezado1."\n".$encabezado2."\n".$encabezado3."\n".$encabezado4),0,"C");
 		$pdf::SetFont('Arial','',10);
 		$pdf::setXY(8,30);
 		$pdf::MultiCell(97,3,utf8_decode($encabezado5."\n".$encabezado6),0,"L");
 		$pdf::setXY(105,30);
 		$pdf::MultiCell(97,3,utf8_decode($encabezado7."\n".$encabezado8),0,"R");
 		$pdf::setXY(60,38);
 		$pdf::SetFont('Arial','B',11);
 		$pdf::MultiCell(97,3,utf8_decode($encabezado9),0,"C");

 		$pdf::SetFont('Arial','',9);
 		$pdf::setXY(8,45);
 		$pdf::cell(22,4,utf8_decode('ASIGNATURA:'),0,1,"L");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(30,45);
 		$pdf::cell(95,4,'______________________________________________________',0,1,"C");
 		$pdf::setXY(30,45);$pdf::SetFont('Arial','B',9);
 		$pdf::cell(95,4,utf8_decode($curso->descripcionCurso),0,1,"L");$pdf::SetFont('Arial','',9);

 		$pdf::setXY(125,45);
 		$pdf::cell(20,4,utf8_decode('SEMESTRE:'),0,1,"L");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(145,45);$pdf::SetFont('Arial','B',9);
 		$pdf::cell(10,4,utf8_decode(''),0,1,"C");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(145,45);
 		$pdf::cell(10,4,'______',0,1,"C");

 		$pdf::setXY(155,45);
 		$pdf::cell(15,4,utf8_decode('GRUPO:'),0,1,"L");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(170,45);$pdf::SetFont('Arial','B',9);
 		$pdf::cell(32,4,utf8_decode($grupo->descripcion),0,1,"L");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(170,45);
 		$pdf::cell(32,4,'__________________',0,1,"C");

 		$pdf::setXY(8,50);
 		$pdf::cell(16,4,utf8_decode('BLOQUE:'),0,1,"L");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(24,50);
 		$pdf::cell(81,4,'______________________________________________',0,1,"C");
 		$pdf::setXY(24,50);$pdf::SetFont('Arial','B',9);
 		$pdf::cell(81,4,utf8_decode('ALGUN BLOQUE O DATO'),0,1,"L");$pdf::SetFont('Arial','',9);

 		$pdf::setXY(105,50);
 		$pdf::cell(17,4,utf8_decode('PERIODO:'),0,1,"L");
 		$pdf::setXY(122,50);
 		$pdf::cell(80,4,'_____________________________________________',0,1,"C");
 		$pdf::setXY(122,50);$pdf::SetFont('Arial','B',9);
 		$pdf::cell(80,4,utf8_decode('ALGUN PERIODO O DATO'),0,1,"L");$pdf::SetFont('Arial','',9);

 		$pdf::setXY(8,55);
 		$pdf::cell(70,4,utf8_decode('PROFESOR QUE IMPARTE LA ASIGNATURA:'),0,1,"L");$pdf::SetFont('Arial','',9);
 		$pdf::setXY(78,55);
 		$pdf::cell(124,4,'______________________________________________________________________',0,1,"C");
 		$pdf::setXY(78,55);$pdf::SetFont('Arial','B',9);
 		$pdf::cell(124,4,utf8_decode(Auth::user()->name." ".Auth::user()->app." ".Auth::user()->apm),0,1,"L");
        $pdf::SetFont('Arial','',9);

 		// Tabla con valores
 		// Encabezados
 		$pdf::setXY(8,62);$pdf::SetFont('Arial','B',8);
 		$pdf::cell(7,12,utf8_decode('No.'),1,1,"C");
 		$pdf::setXY(15,62);
 		$pdf::cell(70,12,utf8_decode('NOMBRE DEL ALUMNO'),1,1,"C");
 		$pdf::setXY(85,62);
 		$pdf::cell(45,6,utf8_decode('UNIDADES'),1,1,"C");
 		// aQUI EMPEZARIA BUCLE DE NUMERO DE UNIDADES
 		$xh = 85;
 		$rango_salto = 45 / $no_unidades;
 		for($k=1; $k <= $no_unidades; $k++){
 			$pdf::setXY($xh,68);
 			$pdf::cell($rango_salto,6,utf8_decode($k),1,1,"C");
 			$xh = $xh + $rango_salto;
 		}
 		
 		// Porcentaje de asistencia
 		$pdf::setXY(130,62);
 		$pdf::MultiCell(23,6,utf8_decode("ASISTENCIA\n%"),1,"C");
 		// Evaluacion global
 		$pdf::setXY(153,62);
 		$pdf::MultiCell(49,4,utf8_decode("EVALUACIÓN\nGLOBAL"),1,"C");
 		// Numero
 		$pdf::setXY(153, 70);
 		$pdf::cell(15,4,utf8_decode('NÚMERO'),1,1,"C");
 		// Letra
 		$pdf::setXY(168, 70);
 		$pdf::cell(34,4,utf8_decode('LETRA'),1,1,"C");	
 		$pdf::setXY(8,62);$pdf::SetFont('Arial','',7);

 		$y = 74;
 		$cont = 1;
 		foreach($alumnos as $alumno){
 			$pdf::setXY(15, $y);
 			$oldy = $y;
 			// Primero se inserta el nombre
 			$pdf::MultiCell(70,4,utf8_decode($alumno[0]),1,"L");
 			// Ahora calculamos valores e insertamos el no consecuente
            
 			$x = 8; $y = $pdf::getY(); 
 			$pdf::setXY($x, $oldy);
 			$pdf::cell(7,$y-$oldy,utf8_decode($cont),1,1,"C");
 			// Ahora metemos calificaciones de unidadfes

 			// recorremos array de calificaciones de cada alumno
 			$x = 85;
 			
 			foreach ($alumno[1] as $calificacion) {
 				$pdf::setXY($x, $oldy);
 				$pdf::cell($rango_salto,$y-$oldy,utf8_decode($calificacion),1,1,"C");
 				$x = $x + $rango_salto;
 			}

 			// Rabngo de x de porcentaje hasta letra
 			// X = 135, X = 202
 			// Diferencia de rango (tamaño) = 67
 			// es decir 67 / 3 para las casillas restantes

 			//porcentaje de asistencia
 			$x = 130;
 			$pdf::setXY($x, $oldy);
 			$pdf::cell(23,$y-$oldy,utf8_decode($alumno[2]),1,1,"C");
 			// Evaluacion global
 			//Numero
 			$pdf::setXY(153, $oldy);
 			$pdf::cell(15,$y-$oldy,utf8_decode($alumno[3]),1,1,"C");
 			// Letra
 			$pdf::setXY(168, $oldy);
 			$pdf::cell(34,$y-$oldy,utf8_decode($alumno[4]),1,1,"C"); 	
 			$cont++;		
 		}
        // Fecha de Entrega
        $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $pdf::setXY(47,230);
 		$pdf::SetFont('Arial','',10);
 		$pdf::MultiCell(100,3,utf8_decode($dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y')),0,"L");
        
        $pdf::setXY(8,231);
 		$pdf::SetFont('Arial','B',10);
 		$pdf::MultiCell(192,3,utf8_decode("FECHA DE ENTREGA:___________________________________"),0,"L");
        
        // Firma
        $pdf::setXY(8,244);
 		$pdf::SetFont('Arial','B',10);
 		$pdf::MultiCell(192,3,utf8_decode("_______________________________________________"),0,"C");
        $pdf::setXY(8,249);
 		$pdf::SetFont('Arial','B',10);
 		$pdf::MultiCell(192,3,utf8_decode("FIRMA"),0,"C");
 		
 		// Especificaciones
 		$pdf::setXY(8,255);
 		$pdf::SetFont('Arial','',6);
 		$pdf::MultiCell(195,3,utf8_decode("ESPECIFICACIONES:\n".$especificacion1."\n".$especificacion2."\n".$especificacion3."\n".
        $especificacion4."\n".$especificacion5."\n".$especificacion6),0,"L");

 		$pdf::Output();
 		exit;
    }

    /* KARDEX de un alumno
     * @params idAlumno: Identificador de alumno
     */
    public function kardexAlumno($idAlumno){
        $alumno = Alumno::find($idAlumno); // Obtenemos Alumno
        $normal = Escuelanormal::find($alumno->idescuelanormal);
        $logo = 'cam.jpg';
        // Sacamos logo
        if($alumno->idescuelanormal == 1) // CAM
            $logo = 'cam.jpg';            
        if($alumno->idescuelanormal == 2) // Normal Preescolar
            $logo = 'NormalPreescolar.jpg';            
        if($alumno->idescuelanormal == 3) // Normal Primaria Teacalco
            $logo = 'NormalPrimariaTeacalco.jpg';            
        if($alumno->idescuelanormal == 4) // Normal rural
            $logo = 'NormalRural.jpg';            
        if($alumno->idescuelanormal == 5) // Normal Urbana Federal
            $logo = 'NormalUrbanaFederal.jpg';            

        $encabezado1 = "SISTEMA EDUCATIVO NACIONAL";
    	$encabezado2 = "EDUCACIÓN NORMAL";
    	$encabezado3 = "KARDEX DEL ALUMNO";

        // Extraemos datos del alumno
        $array_final = [];
        
        // Obtenemos los cursos de los cuales el alumno ya ha cursado o tiene asignadas calificOBaciones
        $cursos_cursados = DB::table('calificaciones')
            ->join('cursos', 'calificaciones.id_curso', '=', 'cursos.id_curso')
            ->distinct()->select('calificaciones.id_curso')
            ->where('calificaciones.id_alumno', '=', $idAlumno)
            ->orderBy('calificaciones.id_curso')
            ->get();

        // Obtenemos el plan de estudios al que pertenece
        // primero obtenemos el grupo al que pertenece dicho alumno
        $grupo = Grupo::find((int)$alumno->idgrupo);
        
        //$calificacion = Calificacion::where('id_alumno', $idAlumno)
        //    ->first();
        //$plan_id = $calificacion->curso->plan->id;
        
        // Obtenemos todos los cursos
        $cursos = Curso::where('licenciatura', $normal->licenciatura)
            ->where('plan_id', $grupo->plan_id)
            ->get();
        
        // Hacemos distinct para saber que semestres son los que estan registrados
        $semestress = DB::select(
            'select distinct semestre
             from cursos where licenciatura = :lic and plan_id = :plann',
            [
                'lic' => $normal->licenciatura,
                'plann' => $grupo->plan_id
            ]
        );

	$prom_final = 0;
        $cont_pm = 0;

        
        // Recorremos cursos
        foreach($cursos as $curso){
            // Si el curso actual esta en el array de $cursos_cursados procedemos a generar el promedio gneral
            if($this->estaEn($curso->id_curso, $cursos_cursados)){
                // Sacamos promedio del curso actual con otra query
                $promedio = DB::select(
                    'select 
                  sum(calificaciones.calificacion) as promedio,
                  sum(calificaciones.asistencia) as asistencia
                  from calificaciones join cursos 
                  where id_alumno = :idalumno 
                  and calificaciones.id_curso = cursos.id_curso
                  and calificaciones.id_curso = :idcurso ',
                    [
                        'idalumno' => $idAlumno,
                        'idcurso'  => $curso->id_curso
                    ]);
                
                // Convertimos el numero de asistencias en formato entendible normal
                $prom = round( ($promedio[0]->asistencia / $curso->numeroUnidades), 1 );
                $pp = explode(".", (string)$prom);
                
                if(count($pp) > 1) // Si hay punto decimal
                    $prom = $pp[0]."".$pp[1]."%";
                else // si no solo se le agrega el 0%
                    $prom = $pp[0]."0%";
                
                // Agregamos al array final los datos de cada linea
                array_push($array_final, [
                    $curso->id_curso, // clave
                    $curso->descripcionCurso, // asignatura
                    round( ($promedio[0]->promedio / $curso->numeroUnidades), 1 ), // calif final
                    $prom, // porcentaje
                    $curso->semestre // Auxiliar semestre
                ]);
		$prom_final += round( ($promedio[0]->promedio / $curso->numeroUnidades), 1 ); // calif final                                                            
                $cont_pm++;
            }else{
                array_push($array_final, [
                    $curso->id_curso, // clave
                    $curso->descripcionCurso, //asignatura
                    '-',
                    '-',
                    $curso->semestre // auxiliar semestre
                ]);
            }
        }

        //// ************ Calculo de Ciclos escolares *************** ////                                                                                              
        $ciclo_escolar_actual_alumno = $alumno->grupos->cicloescolar;
        $anio_ciclo = explode("-",$ciclo_escolar_actual_alumno->descripcion);
        $res = $anio_ciclo;
        $semestre = $alumno->grupos->semestre;
        $ciclos_finales = array();

	// Bucles ciclos escolares anteriores al actual (depende si los hubiese)                                                                                        
        for($i = $semestre; $i >= 1; $i -= 2){
            if($i %2 == 0 ){ // Par (Enero - Junio)                                                                                                                     
                $ciclos_finales[$i] = $anio_ciclo[0]."-".(int)$anio_ciclo[1];
                $ciclos_finales[$i-1] = $anio_ciclo[0]."-".(int)$anio_ciclo[1];
                $anio_ciclo[0] = (int)$anio_ciclo[0]-1; $anio_ciclo[1] = (int)$anio_ciclo[1]-1;
            }else{ // Non (Julio - Diciembre)                                                                                                                           
                $ciclos_finales[$i]   = ((int)$anio_ciclo[0])."-".$anio_ciclo[1];
                $ciclos_finales[$i-1] = ((int)$anio_ciclo[0]-1)."-".((int)$anio_ciclo[1]-1);
                $anio_ciclo[0] = (int)$anio_ciclo[0]-1; $anio_ciclo[1] = (int)$anio_ciclo[1]-1;
            }
        }

        $anio_ciclo = $res;

        // Bucles ciclos escolares posteriores al actual (depende si los hubiese)                                                                                       
        for($i = $semestre; $i <= 8; $i += 2){
            if($i %2 == 0 ){ // Par (Enero - Junio)                                                                                                                     
		$ciclos_finales[$i] = $anio_ciclo[0]."-".(int)$anio_ciclo[1];
                $ciclos_finales[$i-1] = $anio_ciclo[0]."-".(int)$anio_ciclo[1];
                $anio_ciclo[0] = (int)$anio_ciclo[0]+1; $anio_ciclo[1] = (int)$anio_ciclo[1]+1;
            }else{ // Non (Julio - Diciembre)                                                                                                                           
                $ciclos_finales[$i]   = ((int)$anio_ciclo[0])."-".$anio_ciclo[1];
                $ciclos_finales[$i+1] = ((int)$anio_ciclo[0])."-".((int)$anio_ciclo[1]);
                $anio_ciclo[0] = (int)$anio_ciclo[0]+1; $anio_ciclo[1] = (int)$anio_ciclo[1]+1;
            }
        }        
        /*******************************************************/


        // Generacion de PDF
        $pdf = new Fpdf('P', 'mm', 'letter');
 		$pdf::AddPage();
 		$pdf::SetFont('Arial','B',10);
 		$pdf::Image('logos/sepe.jpg',8,10,50);
 		$pdf::Image('logos/'.$logo,185,10,10);

 		$pdf::setXY(60,10);
 		$pdf::MultiCell(97,4,utf8_decode($encabezado1."\n".$encabezado2."\n".$encabezado3),0,"C");
 		$pdf::SetFont('Arial','B',8);
        // nOMBRE DE LA ESCUELA
        
        $pdf::setXY(8,23);
        $pdf::cell(195,7,utf8_decode(''),1,1,"C");

        $pdf::setXY(8,23);
	$personaje = $normal->nombre_personaje != null ? "\"".$normal->nombre_personaje."\"" : "";
        $pdf::cell(148,7,utf8_decode($normal->nombre." ".$personaje),0,1,"C");

        $pdf::setXY(156,23);
        $pdf::cell(47,7,utf8_decode($normal->cct),0,1,"C");

        $pdf::SetFont('Arial','',6);
        $pdf::setXY(8,28);
        $pdf::cell(148,7,utf8_decode("NOMBRE DE LA ESCUELA"),0,1,"C");

        $pdf::setXY(156,28);
        $pdf::cell(47,7,utf8_decode("CLAVE DEL CCT"),0,1,"C");
        
        
        // Entidad federativa
                
        $pdf::setXY(8,30);
        $pdf::cell(195,7,utf8_decode(''),1,1,"C");
        $pdf::SetFont('Arial','B',8);
        $pdf::setXY(156,32);
        $pdf::cell(47,7,utf8_decode($alumno->curp),0,1,"C");

        $pdf::setXY(8,32);
        $pdf::cell(74,7,utf8_decode("TLAXCALA"),0,1,"C");

        $pdf::setXY(82,32);
        $pdf::cell(74,7,utf8_decode("TLAXCALA"),0,1,"C");

        $pdf::SetFont('Arial','',6);
        
        $pdf::setXY(8,35);
        $pdf::cell(74,7,utf8_decode("MUNICIPIO O DELEGACIÓN POLÍTICA"),0,1,"C");
        
        $pdf::setXY(82,35);
        $pdf::cell(74,7,utf8_decode("ENTIDAD FEDERATIVA"),0,1,"C");

        $pdf::setXY(156,35);
        $pdf::cell(47,7,utf8_decode("CURP"),0,1,"C");

        // DATOS DEL ALUMNO
        $pdf::setXY(8,37);
        $pdf::cell(195,7,utf8_decode(''),1,1,"C");
        $pdf::SetFont('Arial','B',8);

        $pdf::setXY(8,39);
        $pdf::cell(65,7,utf8_decode($alumno->app),0,1,"C");

        $pdf::setXY(73,39);
        $pdf::cell(65,7,utf8_decode($alumno->apm),0,1,"C");

        $pdf::setXY(138,39);
        $pdf::cell(65,7,utf8_decode($alumno->nombre),0,1,"C");

        $pdf::SetFont('Arial','',6);

        $pdf::setXY(8,42);
        $pdf::cell(65,7,utf8_decode("PRIMER APELLIDO"),0,1,"C");

        $pdf::setXY(73,42);
        $pdf::cell(65,7,utf8_decode("SEGUNDO APELLIDO"),0,1,"C");

        $pdf::setXY(138,42);
        $pdf::cell(65,7,utf8_decode("NOMBRE(S)"),0,1,"C");

        // LICENCIATURA Y MODALIDAD Y MATRICULA
        $pdf::setXY(8,44);
        $pdf::cell(195,7,utf8_decode(''),1,1,"C");
        $pdf::SetFont('Arial','B',8);

        $pdf::setXY(8,46);
        $pdf::cell(130,7,utf8_decode("EN EDUCACIÓN ".$normal->licenciatura." ESCOLARIZADA"),0,1,"C");
        
        $pdf::setXY(138,46);
        $pdf::cell(65,7,utf8_decode($alumno->matricula),0,1,"C");

        $pdf::SetFont('Arial','B',6);

        $pdf::setXY(8,49);
        $pdf::cell(130,7,utf8_decode("LICENCIATURA Y MODALIDAD"),0,1,"C");
        
        $pdf::setXY(138,49);
        $pdf::cell(65,7,utf8_decode("MATRICULA"),0,1,"C");
        
        //
        $pdf::setXY(8,51);
        $pdf::cell(195,3,utf8_decode(''),1,1,"C");

        // Encabezados semestre

        $altura = 60;

        for($k=1;$k<=count($semestress);$k++){
	    $cont_clave_curso = 1;
            if($k == 6){
                $pdf::AddPage();
                $altura = 30;
            }

            // fecha de baja
            $pdf::setXY(135,$altura-4);
            $pdf::cell(22,3,utf8_decode('FECHA DE BAJA:'),0,1,"C");
            
            $pdf::setXY(181,$altura);
            $pdf::cell(22,33,utf8_decode(''),1,1,"C");

            $pdf::setXY(181,$altura);
            $pdf::MultiCell(22,3,utf8_decode("REVISADO Y\nCONFRONTADO"),0,"C");

            $pdf::setXY(181,$altura+30);
            $pdf::MultiCell(22,3,utf8_decode("AÑO MES DIA"),0,"C");
            
            ////
            
            $pdf::setXY(8,$altura);
            $pdf::cell(20,3,utf8_decode('SEMESTRE: '.$k),1,1,"C");
            
            $pdf::setXY(28,$altura);
            $pdf::cell(61,3,utf8_decode('CICLO ESCOLAR '.$ciclos_finales[$k]),1,1,"C");
            
            $pdf::setXY(89,$altura); //Grupo A
            $pdf::cell(21,3,utf8_decode(''),1,1,"L");
            
            $pdf::setXY(110,$altura);
            $pdf::cell(35,3,utf8_decode('TEMPORAL'),1,1,"L");
            
            $pdf::setXY(145,$altura);
            $pdf::cell(34,3,utf8_decode('DEFINITIVA'),1,1,"L");
            
            $altura += 3;
            $pdf::setXY(8,$altura);
            $pdf::cell(10,6,utf8_decode('CLAVE'),1,1,"C");
            
            $pdf::setXY(18,$altura);
            $pdf::cell(71,6,utf8_decode('ASIGNATURAS'),1,1,"C");
            
            $pdf::setXY(89,$altura);
            $pdf::MultiCell(10,3,utf8_decode("CALIF\nFINAL"),1,"C");
            
            $pdf::setXY(99,$altura);
            $pdf::MultiCell(11,3,utf8_decode("% DE\nASIST"),1,"C");
            
            $pdf::setXY(110,$altura);
            $pdf::cell(69,3,utf8_decode('PERIODOS DE REGULARIZACIÓN'),1,1,"C");
            
            $pdf::setXY(110,$altura+3);
            $pdf::cell(13,3,utf8_decode('FECHA'),1,1,"C");
            
            $pdf::setXY(123,$altura+3);
            $pdf::cell(10,3,utf8_decode('CALIF.'),1,1,"C");
            
            $pdf::setXY(133,$altura+3);
            $pdf::cell(13,3,utf8_decode('FECHA'),1,1,"C");
            
            $pdf::setXY(146,$altura+3);
            $pdf::cell(10,3,utf8_decode('CALIF.'),1,1,"C");
            
            $pdf::setXY(156,$altura+3);
            $pdf::cell(13,3,utf8_decode('FECHA'),1,1,"C");
            
            $pdf::setXY(169,$altura+3);
            $pdf::cell(10,3,utf8_decode('CALIF.'),1,1,"C");
            
            //$pdf::setXY(181,$altura+3);
            //$pdf::cell(22,3,utf8_decode('FECHA'),1,1,"C");
            
            $pdf::SetFont('Arial','',7);
            $altura += 6;
            
            /***** Semestres *****/
            //while($k == $array_final[4]){ // Mientras estemos en el semestre actual

            foreach($array_final as $row){
                if($row[4] == $k){ // si el elemento actual es del semestre iterado actual
                $pdf::setXY(8,$altura);
                //$pdf::cell(10,3,utf8_decode($row[0]),1,1,"C");
		$pdf::cell(10,3,utf8_decode($k."".$cont_clave_curso),1,1,"C"); //Clave del curso
		$cont_clave_curso++; // COntador de clave                

                $pdf::setXY(18,$altura);
                $pdf::cell(71,3,utf8_decode($row[1]),1,1,"L");
                
                $pdf::setXY(89,$altura);
                $pdf::cell(10,3,utf8_decode($row[2]),1,1,"C");
                
                $pdf::setXY(99,$altura);
                $pdf::cell(11,3,utf8_decode($row[3]),1,1,"C");
                
                $pdf::setXY(110,$altura);
                $pdf::cell(13,3,utf8_decode('-'),1,1,"C");
                
                $pdf::setXY(123,$altura);
                $pdf::cell(10,3,utf8_decode('-'),1,1,"C");
                
                $pdf::setXY(133,$altura);
                $pdf::cell(13,3,utf8_decode('-'),1,1,"C");
                
                $pdf::setXY(146,$altura);
                $pdf::cell(10,3,utf8_decode('-'),1,1,"C");
                
                $pdf::setXY(156,$altura);
                $pdf::cell(13,3,utf8_decode('-'),1,1,"C");
            
                $pdf::setXY(169,$altura);
                $pdf::cell(10,3,utf8_decode('-'),1,1,"C");

                $altura+=3;
                }
            }
                        
            $altura+=10;    
        }

        // altura
        $y = $pdf::getY();
        $pdf::setXY(15, $y+50);
        $pdf::SetFont('Arial','B',10);
        $pdf::cell(50,3,utf8_decode('______________________________'),0,1,"C");

        $pdf::setXY(15, $y+54);
        $pdf::cell(50,3,utf8_decode('DIRECTOR DE LA ESCUELA'),0,1,"C");

        $pdf::setXY(85, $y+54);
        $pdf::MultiCell(30,3,utf8_decode("SELLO\nDE LA\nESCUELA"),0,"C");

        $pdf::setXY(125, $y+52);
        $pdf::SetFont('Arial','B',8);
        $pdf::MultiCell(38,3,utf8_decode("PROMEDIO GENERAL DE\nAPROVECHAMIENTO"),0,"L");

        $pdf::setXY(168, $y+52);
        $pdf::MultiCell(25,5,utf8_decode($cont_pm == 0 ? 0 : round(($prom_final / $cont_pm), 2 )),1,"L");
        
        $pdf::Output();
 		exit;
    }
    
    /*
    * Transforma numero en letra
    * @params numero: Numero a convertir en letra
    */
    public function generarNumero($numero){
        switch($numero){
        case '1':
            return 'UNO';
            break;
        case '2':
            return 'DOS';
            break;
        case '3':
            return 'TRES';
            break;
        case '4':
            return 'CUATRO';
            break;
        case '5':
            return 'CINCO';
            break;
        case '6':
            return 'SEIS';
            break;
        case '7':
            return 'SIETE';
            break;
        case '8':
            return 'OCHO';
            break;
        case '9':
            return 'NUEVE';
            break;
        case '10':
            return 'DIEZ';
            break;
        default: return 'CERO';
        }
    }

    // Funcion auxliar que llena la matriz de la carga horaria (para horarios)
    public function llenaMatriz($cargas, $horas_select){
    	$array_final = array();

    	for($i=0; $i < 13; $i++){ // filas
    		$array_temporal = array();
    		array_push($array_temporal, array($horas_select[$i].'-'.$horas_select[$i+1], 'h') ); // Agregamos primera posicion de cada fila (hora)
    		for($j=1; $j < 8; $j++){ // columnas empezamos de 1 ya que la posicion 0 es de la hora ya asignada arriba
    			//array_push($array_temporal, '--');
    			$existio = false;
    			foreach($cargas as $carga){
    				if( $carga->dia == $j 
    					and 
    						// comprobamos rango de horas
    						// Si $horas_select[i] esta entre $carga->hora_inicio y $carga->hora_fin
    						($horas_select[$i] >= $carga->hora_inicio and $horas_select[$i+1] <= $carga->hora_fin)){
    						//$carga->hora_inicio == $horas_select[$i] ) { // Si es del mismo dia y la misma hora
    						//array_push($array_temporal, $carga->curso->descripcionCurso." - ".$carga->idCargaHoraria.
    						//	" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm);

    						// Comparacion para mostrar boton
    						// Si es para mostrar el boton
    						if($horas_select[$i+1] == $carga->hora_fin){
    							array_push($array_temporal, array($carga->curso->descripcionCurso." - ".
    								" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm, $carga->idCargaHoraria) );
    						}else{
    							array_push($array_temporal, array($carga->curso->descripcionCurso." - ".
    								" ".$carga->user->name." ".$carga->user->app." ".$carga->user->apm, '-') );
    						}
    						$existio = true;
    						break;
    				}
    			}

    			if(!$existio)
    				array_push($array_temporal, array('--', '-'));
    		}

    		array_push($array_final, $array_temporal);
    	}


    	return $array_final;
    }

    // Genracion de la vista (matriz de horario)
    // Recibimos el id del grupo
    public function cargaHoraria($idGrupo){

    	// Obtenemos nombre del grupo
    	$grupo = Grupo::find( (int)$idGrupo );

    	// Obtenemos todas las cargas horarias al grupo seleccionado
    	$cargas_horarias = Grupo_curso_user::where('grupo_id', '=', $idGrupo )->get();//->orderBy('dia')->orderBy('hora_inicio');

    	// Encabezado de tabla
    	$dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    	// Horas de los horarios
    	$horas = ['7:00-8:00', '8:00-9:00', '9:00-10:00', '10:00-11:00', 
    			  '11:00-12:00', '12:00-13:00', '13:00-14:00', '14:00-15:00',
    			  '15:00-16:00', '16:00-17:00', '17:00-18:00', '18:00-19:00', '19:00-20:00']; 

    	// Las horas para el select
    	$horas_select = [
    						'07:00:00',
    						'08:00:00',
    						'09:00:00',
    						'10:00:00',
    						'11:00:00',
    						'12:00:00',
    						'13:00:00',
    						'14:00:00',
    						'15:00:00',
    						'16:00:00',
    						'17:00:00',
    						'18:00:00',
    						'19:00:00',
    						'20:00:00'
    					];

    	// Matriz a mostrar 
    	$matriz = $this->llenaMatriz($cargas_horarias, $horas_select);

    	// Retornamos la vista
    	//return view('carga_horaria',
        $pdf = PDF::loadView('carga_horaria_pdf',
    				[
    					'grupo'  => $grupo->descripcion,
    					'dias'   => $dias,
    					'horas'  => $horas,
    					'cursos' => $grupo->curso,
    					'horas_select' => $horas_select,
    					'idgrupo' => $idGrupo,
    					'cargas' => $cargas_horarias,
    					'matriz' => $matriz,
                        ]);

        //$pdf = PDF::loadView('', compact('products'));
        $pdf->setPaper('A4', 'landscape');        
        return $pdf->download('cargaHoraria_'.$grupo->descripcion.'.pdf');
    }


    public function califs(){
        $calificaciones = DB::table('calificaciones')
            ->join('cursos', 'calificaciones.id_curso', '=', 'cursos.id_curso')
            ->select(
                'cursos.semestre',
                'cursos.numeroUnidades as unidades',
                'cursos.descripcionCurso',
                'calificaciones.id_calificacion',
                'calificaciones.id_alumno',
                'calificaciones.id_curso',
                'calificaciones.calificacion',
                'calificaciones.unidad',
                'calificaciones.asistencia'
            )
            ->where('id_alumno', '=', 40)
            ->orderBy('cursos.semestre')
            ->orderBy('cursos.id_curso')
            ->orderBy('unidad')
            ->get();

        return view('calificaciones', ["calificaciones" => $calificaciones]);
    }

    
    public function testKardex($idAlumno = 40){
        $array_final = [];
        
        // Obtenemos los cursos de los cuales el alumno ya ha cursado o tiene asignadas calificOBaciones
        $cursos_cursados = DB::table('calificaciones')
            ->join('cursos', 'calificaciones.id_curso', '=', 'cursos.id_curso')
            ->distinct()->select('calificaciones.id_curso')
            ->where('calificaciones.id_alumno', '=', $idAlumno)
            ->orderBy('calificaciones.id_curso')
            ->get();
                    
        // Obtenemos todos los cursos
        $cursos = Curso::all();//->orderBy('semestre')->orderBy('id_curso')->get();

        // Recorremos cursos
        foreach($cursos as $curso){
            // Si el curso actual esta en el array de $cursos_cursados procedemos a generar el promedio gneral
            if($this->estaEn($curso->id_curso, $cursos_cursados)){
                // Sacamos promedio del curso actual con otra query
                $promedio = DB::select(
                    'select 
                  sum(calificaciones.calificacion) as promedio
                  from calificaciones join cursos 
                  where id_alumno = :idalumno 
                  and calificaciones.id_curso = cursos.id_curso
                  and calificaciones.id_curso = :idcurso ',
                    [
                        'idalumno' => $idAlumno,
                        'idcurso'  => $curso->id_curso
                    ]);

                array_push($array_final, [$curso->descripcionCurso, round( ($promedio[0]->promedio / $curso->numeroUnidades), 1 )  ]);
            }else{
                array_push($array_final, [$curso->descripcionCurso, 'No Cursado']);
            }
        }

        return view('kar', ["calificaciones" => $array_final]);        
    }

    // Metodo auxiliar que busca si el curso actual esta en los cursos que ya curso el alumno
    // Usado para sacar el cardex
    private function estaEn($buscar, $en){
        foreach($en as $elemento){
            if((int)$buscar == (int)$elemento->id_curso)
                return true;
        }

        return false;
    }



    /**  Certificado **/
    public function certificado($idAlumno = 40){

        // Inicializamos variable de alumno
        //$alumno = Alumno::find($idAlumno);
        $alumno = 0;
        // Comprobamos si recibimos id del alumno o curp
        // Si el id es numerico es id, sino es por CURP
        if(is_numeric($idAlumno)){
            $alumno = Alumno::find($idAlumno);
        }else{
            $alumno = Alumno::where('curp', '=', $idAlumno)->first();
        }
        
        // Si no existe alumno mandamos error 404
        if(! $alumno ){
            return response()->view('errors.404',[],404);
        }
// Obetenemos datos escuela
        $escuela = Escuelanormal::find($alumno->idescuelanormal);
        
        
        $pdf = new Fpdf('P', 'mm', 'letter');
        $pdf2 = new Fpdf('P', 'mm', 'letter');
 		$pdf::AddPage();
 		//$pdf::SetFont('Arial','',6);
        $pdf::AddFont('SoberanaSans-Regular','','SoberanaSans-Regular.php');
        $pdf::AddFont('SoberanaSans-Regular','B','SoberanaSans-Regular.php');
        
        $pdf::SetFont('SoberanaSans-Regular','',7);
        $pdf::Image('img/CERTIFICADO EDUCACION NORMAL TLAXCALA.jpg',0,0,209);

        // Encabezado
        $enc1 = "La Secretaría de Educación Pública de Tlaxcala a través de la ESCUELA ".$escuela->nombre." \"".$escuela->nombre_personaje."\", con Clave de Centro de Trabajo";
        $enc2 = $escuela->cct." CERTIFICA que ".$alumno->nombre." ".$alumno->app." ".$alumno->apm." con CURP ".$alumno->curp.", cursó los estudios completos de Licenciado en Educación";
        $enc3 = "Primaria, en la modalidad Escolarizada, conforme al Plan de Estudios 2012, con clave de carrera 241302 y clave de Institución 290010, según constancias que";
        $enc4 = "obran en el Área de Control Escolar.";

        $enc5 = "Este documento ampara cincuenta y seis cursos de un total de cincuenta y seis cursos, que integran el Plan de Estudios respectivo, correspondientes a 291 creditos.";

        $enc6 = "El presente se extiende en TLAXCALA, TLAXCALA, a los veintiocho días del mes de julio de dos mil diecisiete.";

        $y = 38;
        
        $pdf::setXY(9, $y);
        //$pdf::writehtml($enc1." ".$enc2." ".$enc3." ".$enc4."\n\n".$enc5."\n\n".$enc6);
        $pdf::MultiCell(192,3, utf8_decode($enc1." ".$enc2." ".$enc3." ".$enc4."\n".$enc5."\n\n".$enc6),0,"J");
        
        // Obtenemos datos
        // Extraemos datos del alumno
        $array_final = [];
        
        // Obtenemos los cursos de los cuales el alumno ya ha cursado o tiene asignadas calificOBaciones
        $cursos_cursados = DB::table('calificaciones')
            ->join('cursos', 'calificaciones.id_curso', '=', 'cursos.id_curso')
            ->distinct()->select('calificaciones.id_curso')
            ->where('calificaciones.id_alumno', '=', $alumno->id_Alumno)
            ->orderBy('calificaciones.id_curso')
            ->get();
                    
        // Obtenemos todos los cursos
        $cursos = Curso::all();//->orderBy('semestre')->orderBy('id_curso')->get();

        // Recorremos cursos
        foreach($cursos as $curso){
            // Si el curso actual esta en el array de $cursos_cursados procedemos a generar el promedio gneral
            if($this->estaEn($curso->id_curso, $cursos_cursados)){
                // Sacamos promedio del curso actual con otra query
                $promedio = DB::select(
                    'select 
                  sum(calificaciones.calificacion) as promedio,
                  sum(calificaciones.asistencia) as asistencia
                  from calificaciones join cursos 
                  where id_alumno = :idalumno 
                  and calificaciones.id_curso = cursos.id_curso
                  and calificaciones.id_curso = :idcurso ',
                    [
                        'idalumno' => $alumno->id_Alumno,
                        'idcurso'  => $curso->id_curso
                    ]);

                // Agregamos al array final los datos de cada linea
                array_push($array_final, [
                    $curso->descripcionCurso, // asignatura
                    round( ($promedio[0]->promedio / $curso->numeroUnidades), 1), // calif final
                    $curso->semestre // Auxiliar semestre
                ]);
            }else{
                array_push($array_final, [
                    $curso->descripcionCurso, //asignatura
                    '-',
                    $curso->semestre // auxiliar semestre
                ]);
            }
        }

        $array_ce = [
            "PRIMER SEMESTRE (2012-2013)",
            "SEGUNDO SEMESTRE (2012-2013)",
            "TERCER SEMESTRE (2013-2014)",
            "CUARTO SEMESTRE (2013-2014)",
            "QUINTO SEMESTRE (2014-2015)",
            "SEXTO SEMESTRE (2014-2015)",
            "SÉPTIMO SEMESTRE (2015-2016)",
            "OCTAVO SEMESTRE (2015-2016)",
        ];

        $sumprom = 0;
        $contp = 0;
        
        $pdf::SetFont('SoberanaSans-Regular','',6);
        $y = 77;
        $x = 10;

        for($i = 1; $i <= 8; $i++){
            $pdf::setXY($x, $y-4);
            $pdf::cell(83, 4, utf8_decode($array_ce[$i-1]), 0,1,"C");
            
            $xini = $x; // Auxiliares iniciales de x y y
            $yini = $y;
            foreach($array_final as $row){
                $pdf::setXY($x, $y);
                if($i == $row[2]){ // Si estamos en el semestre atual
                    $pdf::cell(83, 4, utf8_decode(mb_strtoupper($row[0])), 0,1,"L");
                    $pdf::setXY($x+83, $y);
                    $pdf::cell(12, 4, utf8_decode($row[1]), 0,1,"C");
                    $sumprom += (float)$row[1]; // sUMA DEL PROMEDIO
                    if($row[1] != '-')
                        $contp+=1;
                    $y = $y + 3;
                }
            }
            
            // Si no es salto de linea arrimamos a la derecha las coordenadas
            if( $i%2 != 0){
                $x = $x+95;
                $y = $yini; // Reinicamos Y
                $pdf::setXY($x, $y);
            }
            
            // Si va salto de linea
            if( $i%2 == 0){
                $x = 10;
                $y = $y + 8;
                $pdf::setXY($x, $y);
            }
        }

        $promedio = round(($sumprom / $contp), 1);
        // Promedio general
        $pdf::setXY(14, 202);
        $pdf::cell(16, 6, utf8_decode($promedio), 0,1,"C");

        $autoridad_educativa = "MANUEL CAMACHO HIGAREDA SECRETARIO DE EDUCACIÓN PÚBLICA DE TLAXCALA";
        // Autoridad educativa
        $pdf::setXY(60, 197);
        $pdf::cell(100, 3, utf8_decode($autoridad_educativa), 0,1,"C");

        // Sello digital
        $sd = "pKM+7waFqXN67LZ283WMe7u3IQQ9d8Bk+OuNYN9ENPEe8aGKdoHK5P23Uq/NijCIhtFC/jlDI/vYzWEYoVung+aD1fdiAP7CvIXQ9wW6VYdqDoqnLRWhttZxPPjHqS3afOUs3kg0cr7Ah5fjdT479MWqxlje1z7iUbxdJZRoz+M04MeEzOpNCBIeeA7Nc36ddjdYzZ54fV6uDiXe7CgfkGSOfUEQEVkGIFD4JOdrZi+gb61jwCHK+hJc5TJI3S5+9VpZSDIxM06z9jIWEC4PTyDrFST6iCvINWC0lfcwJvdPdvtz/UdKkcfyX2UZJzzhHTmMd1KNAqmPZ9jx2XQbw==";
        $pdf::setXY(45, 206);
        $pdf::MultiCell(155, 2, utf8_decode($sd),0,"J");
        
        // Ponemos codigo QR
        $pdf::Image("http://127.0.0.1/usuarios/public/qr-code/".$alumno->id_Alumno,6,210,33,0,'PNG');

        $clave = "CE291700001";
        $pdf::setXY(20, 249);
        $pdf::cell(15, 3, utf8_decode($clave), 0,1,"C");
        
        $pdf::Output();
 		exit;
    }

    // Funcion que rretornara el codigo qr a traves de url
    public function generarQR($idAlumno = 40){
        $alumno = Alumno::find($idAlumno);
        $nombre = $alumno->nombre." ".$alumno->app." ".$alumno->apm;
        $au = "MANUEL CAMACHO HIGAREDA";
        $clave = $alumno->folio_certificado;
        
        $code = "https://sien.septlaxcala.gob.mx/public/certificacion/".$clave;
        return QRCode::text($code)->png();
    }


    // Comprobacion de certificado
    public function comprobacionCertificado($idAlumno = ''){
	if($idAlumno == '')
		return view('consulta_certificacion');
	
        $alumno = Alumno::where('folio_certificado', $idAlumno)->first();
        
	$clave_carrera = "241302";
       
	return view('certificacion_verificacion', ['alumno' => $alumno, 'clave_carrera' => $clave_carrera]);
       
    }

    // Recibe el folio de certificado y lo busca
    public function existeFolioCertificado(){
	

    }

    /** Metodo para mostrar los alumnos a certificar **/
    
    /** Metodo de prueba para probar la lectura de ficheros excel **/
    
    public function excel(){
        $reader = \Asan\PHPExcel\Excel::load('primariateacalco.xlsx', function(\Asan\PHPExcel\Reader\Xlsx $reader) {
            // Set row limit
            $reader->setRowLimit(1000);
            
            // Set column limit
            $reader->setColumnLimit(1000);
            
            // Ignore emoty row
            $reader->ignoreEmptyRow(true);
        });

        $lineas = []; // Array final
        $count = $reader->count(); // Numero de lineas
        
        $inicio = 3;

        for( $i = $inicio ; $i <= $count ; $i++ ){
            // skip to row $i 
            $reader->seek($i);
            
            array_push($lineas, $reader->current());
        }

        $normal = 5;

        $guardado = false;
        /*
        // inserccion en la bd
        foreach($lineas as $linea){
            // Primero insertamos al alumno en la bd campos (2,3,4,5)
            $current_alumno = new Alumno; // Instanciamos al nuevo alumno
            $current_alumno->nombre          = $linea[4]; // Nombre del alumno
            $current_alumno->app             = $linea[2]; // Apellido paterno
            $current_alumno->apm             = $linea[3]; // Apellido materno
            $current_alumno->curp            = $linea[5]; // CURP
            $current_alumno->idescuelanormal = $normal;   // Escuela normal
            $current_alumno->egresado        = 1;      // Ya es egresado

            if($current_alumno->save()) // Guardamos alumno
                $guardado = true;
            else{
                $guardado = false;
                break;
            }

            // Posiciones de cada calificacion
            $posiciones_calificaiones = [
                [6,7,8,9,10,11,12,13,14],
                [15,16,17,18,19,20,21,22],
                [23,24,25,26,27,28,29,30,31],
                [32,33,34,35,36,37,38,39,40],
                [41,42,43,44,45,46,47,48,49],
                [50,51,52,53,54,55,56,57,58],
                [59,60,61,62,63,64,65,66],
                [67,68,69]
            ];

            $id_cal = 1;
            // Recorremos las calificaciones para meterlas en la bd
            foreach($posiciones_calificaiones as $pos){
                //$calif = new CalificacionFinal;
                
                // Recorremos cada semestre
                for($i = $pos[1]; $i <= end($pos); $i++){
                    $calif = new CalificacionFinal;
                    $calif->alumno_id = $current_alumno->id_Alumno;
                    $calif->curso_id = $id_cal;
                    $calif->calificacion_final = (int)$linea[$i];
                    $calif->ciclo = $linea[$pos[0]];
                    if($calif->save()) // Guardamos la calificacion actual
                        $guardado = true;
                    else{
                        $guardado = false;
                        break;
                    }
                    $id_cal++; // contador id calificacion
                }
            }
        */  
            // Insertamos las calificaciones en la tabla de calificaciones
            /*$cursos = Curso::where('plan_id', 1)
                ->where('licenciatura', 'PRIMARIA')
                ->orderBy('id_curso')
                ->get();*/
            
            
//        }

        return view('excel', ['guardado' => $guardado, 'lineas' => $lineas]);
    }

    public function certificadoPorEscuela($idEscuela = 5){
        // Obtenemnos la escuela normal
        $normal = Escuelanormal::find($idEscuela);

        // Obtenemos todos los alumnos egresados de la escuela normal
        $alumnos = Alumno::where('egresado', 1)
            ->where('idescuelanormal', $normal->id)
            ->orderBy('app')
            ->orderBy('apm')
            ->orderBy('nombre')
            ->get();

        
        // Obtenemos los cursos depdendiendo de la licenciatura
        $cursos = Curso::where('licenciatura', $normal->licenciatura)
            ->where('plan_id', 1)
            ->get();
        
        
        $pdf = new Fpdf('P', 'mm', 'letter');
	$pdf::SetMargins(10, 10 , 10); 
        $pdf2 = new Fpdf('P', 'mm', 'letter');

        // Generamos todo el pdf
        foreach($alumnos as $alumno){
            
            $pdf::AddPage();
            //$pdf::SetFont('Arial','',6);

	    $pdf::AddFont('SoberanaSans-Regular','','SoberanaSans-Regular.php');
            $pdf::AddFont('SoberanaTitular-Bold','B','SoberanaTitular-Bold.php');
            $pdf::AddFont('SoberanaTitular-Regular','','SoberanaTitular-Regular.php');
            
            
            $pdf::AddFont('SoberanaSans-Regular','B','SoberanaSans-Bold.php');
            
            $pdf::SetFont('SoberanaSans-Regular','',7);
            $pdf::Image('img/CERTIFICADO EDUCACION NORMAL TLAXCALA.jpg',0,0,209);
           
	    $licenciado = "Licenciado";
	    if($alumno->escuelasnormales->id == 4)
		$licenciado = "Licenciada"; 
            // Encabezado
            $enc1 = "La Secretaría de Educación Pública de Tlaxcala a través de la Escuela ".ucwords(strtolower($normal->nombre))." \"".$normal->nombre_personaje."\", con Clave de Centro de Trabajo";
            $enc2 = $normal->cct." <b><strong>CERTIFICA</strong></b> que <b>".trim($alumno->nombre)." ".trim($alumno->app)." ".trim($alumno->apm)."</b> con CURP <b>".$alumno->curp."</b>, cursó los estudios completos de ".$licenciado." en Educación";
            $enc3 = ucwords(strtolower($normal->licenciatura)).", en la modalidad Escolarizada, conforme al Plan de Estudios 2012, con clave de carrera ".$alumno->escuelasnormales->clave_carrera." y clave de Institución ".$alumno->escuelasnormales->clave_institucion.", según constancias que";
            $enc4 = "obran en el Área de Control Escolar.";
            // Variable auxiliar de cursos
	    $total_cursos_letra = "cincuenta y seis"; // Licenciatura Primaria
	    $creditos = "291";
	    if($alumno->escuelasnormales->licenciatura == 'PREESCOLAR'){
		$total_cursos_letra = "cincuenta y cuatro";
		$creditos = "282";           
	    }

            $enc5 = "Este documento ampara ".$total_cursos_letra." cursos de un total de ".$total_cursos_letra." cursos, que integran el Plan de Estudios respectivo, correspondientes a ".$creditos." créditos.";
            
            $enc6 = "El presente se expide en ".strtoupper(utf8_decode($alumno->escuelasnormales->municipio_estado)).", a los doce días del mes de julio de dos mil dieciocho.";
            
            $y = 38;
            
            $pdf::setXY(10, $y);
            //$pdf::MultiCell(192,3, utf8_decode($enc1."\n".$pdf::WriteHtml(utf8_decode($enc2))." ".$enc3." ".$enc4."\n\n".$enc5."\n\n".$enc6),0,"J");

            //$pdf::WriteHtmlCell(192,utf8_decode($enc1)." ".utf8_decode($enc2)." ".utf8_decode($enc3)." ".utf8_decode($enc4)."<br>".utf8_decode($enc5)."<br>".utf8_decode($enc6));           
	    //$pdf::MultiCell(191,3, utf8_decode($enc1), 1, "J");
	    //$pdf::setXY(10, $y+3);
	    $pdf::MultiCell(192, 3, $pdf::WriteHtml(utf8_decode($enc1." ".$enc2." ".$enc3." ".$enc4."<br>".$enc5."<br>".$enc6)), 0, "J");

	    /*
	    $pdf::AddFont('SoberanaSans-Regular','','SoberanaSans-Regular.php');
            $pdf::AddFont('SoberanaSans-Regular','B','SoberanaSans-Regular.php');
            
            $pdf::SetFont('SoberanaSans-Regular','',7);
            $pdf::Image('img/CERTIFICADO EDUCACION NORMAL TLAXCALA.jpg',0,0,209);
            
            // Encabezado
            $enc1 = "La Secretaría de Educación Pública de Tlaxcala a través de la Escuela ".ucwords(strtolower($normal->nombre))." \"".$normal->nombre_personaje."\", con Clave de Centro de Trabajo";
            $enc2 = $normal->cct." CERTIFICA que ".trim($alumno->nombre)." ".trim($alumno->app)." ".trim($alumno->apm)." con CURP ".$alumno->curp.", cursó los estudios completos de Licenciado en Educación";
            $enc3 = ucwords(strtolower($normal->licenciatura)).", en la modalidad Escolarizada, conforme al Plan de Estudios 2012, con clave de carrera ".$alumno->escuelasnormales->clave_carrera." y clave de Institución ".$alumno->escuelasnormales->clave_institucion.", según constancias que";
            $enc4 = "obran en el Área de Control Escolar.";
	    // Variable auxiliar de cursos
	    $total_cursos_letra = "cincuenta y seis"; // Licenciatura Primaria
	    $creditos = "291";
	    if($alumno->escuelasnormales->licenciatura == 'PREESCOLAR'){
		$total_cursos_letra = "cincuenta y cuatro";
		$creditos = "282";           
	    }
 
            $enc5 = "Este documento ampara ".$total_cursos_letra." cursos de un total de ".$total_cursos_letra." cursos, que integran el Plan de Estudios respectivo, correspondientes a ".$creditos." créditos.";
            
            $enc6 = "El presente se extiende en ".strtoupper(utf8_decode($alumno->escuelasnormales->municipio_estado)).", a los veintiocho días del mes de julio de dos mil dieciocho.";
            
            $y = 38;
            
            $pdf::setXY(9, $y);
            //$pdf::writehtml($enc1." ".$enc2." ".$enc3." ".$enc4."\n\n".$enc5."\n\n".$enc6);
            $pdf::MultiCell(192,3, utf8_decode($enc1." ".$enc2." ".$enc3." ".$enc4."\n\n".$enc5."\n\n".$enc6),0,"J");
            */

            // Obtenemos datos
            // Extraemos datos del alumno
            $array_final = [];
            
            // Obtenemos los cursos de los cuales el alumno ya ha cursado o tiene asignadas calificOBaciones
            /*$cursos_cursados = DB::table('calificaciones')
                ->join('cursos', 'calificaciones.id_curso', '=', 'cursos.id_curso')
                ->distinct()->select('calificaciones.id_curso')
                ->where('calificaciones.id_alumno', '=', $alumno->id_Alumno)
                ->orderBy('calificaciones.id_curso')
                ->get();*/
            
            // Obtenemos todos los cursos
            //$cursos = Curso::all();//->orderBy('semestre')->orderBy('id_curso')->get();
            
            // Recorremos cursos
            foreach($cursos as $curso){
                $promedio = CalificacionFinal::where('curso_id', $curso->id_curso) // Que sea el mismo curso
                    ->where('alumno_id', $alumno->id_Alumno) // que sea del alumno actual
                    ->first(); // Obtenemos primer elemento
                
                // Agregamos al array final los datos de cada linea
                array_push($array_final, [
                    $curso->descripcionCurso, // asignatura
                    $promedio->calificacion_final,
                    $curso->semestre, // Auxiliar semestre
                    $promedio->ciclo // Ciclo escolar
                ]);
            }
            
            $array_ce = [
                "PRIMER SEMESTRE",
                "SEGUNDO SEMESTRE",
                "TERCER SEMESTRE",
                "CUARTO SEMESTRE",
                "QUINTO SEMESTRE",
                "SEXTO SEMESTRE",
                "SÉPTIMO SEMESTRE",
                "OCTAVO SEMESTRE",
            ];
            
            $sumprom = 0;
            $contp = 0;
            
            $pdf::SetFont('SoberanaSans-Regular','',6);
            $y = 77;
            $x = 10;
            
            for($i = 1; $i <= 8; $i++){
                $pdf::setXY($x, $y-4);
                
                //$pdf::cell(83, 4, utf8_decode($array_ce[$i-1]), 0,1,"C"); // Encabezados de semestre
                
                $xini = $x; // Auxiliares iniciales de x y y
                $yini = $y; 
                $ant = $array_final[0][2]; // semestre inicial
                foreach($array_final as $row){
                    
                    $pdf::setXY($x, $y);
                    
                    if($i == $row[2]){ // Si estamos en el semestre atual
                        if($row[3] != $ant){
                            $pdf::setXY($x, $y-4);
                            if($row[3][0] == '(')
                                $pdf::cell(83, 4, utf8_decode($array_ce[$i-1]." ".$row[3]), 0,1,"C"); // Encabezados de semestre
                            else
                                $pdf::cell(83, 4, utf8_decode($array_ce[$i-1]." (".$row[3].")"), 0,1,"C"); // Encabezados de semestre
                            $pdf::setXY($x, $y);
                            
                            $ant = $row[3];
                        }
                        
                        $pdf::cell(83, 4, utf8_decode(mb_strtoupper($row[0])), 0,1,"L");
                        $pdf::setXY($x+83, $y);
                        $calfi = $row[1]; 
                        //if($calfi != 10)
                        //    $calfi = number_format((float)$calfi, 1) ;
                        $pdf::cell(12, 4, $calfi, 0,1,"C");
                        $sumprom += $row[1]; // sUMA DEL PROMEDIO
                        if($row[1] != '-')
                            $contp+=1;
                        $y = $y + 3;
                    }
                }
                
                // Si no es salto de linea arrimamos a la derecha las coordenadas
                if( $i%2 != 0){
                    $x = $x+95;
                    $y = $yini; // Reinicamos Y
                    $pdf::setXY($x, $y);
                }
                
                // Si va salto de linea
                if( $i%2 == 0){
                    $x = 10;
                    $y = $y + 8;
                    $pdf::setXY($x, $y);
                }
            }
            
            $promedio = $sumprom / $contp;
            // Promedio general
            $pdf::setXY(14, 202);
            $pdf::cell(16, 6, utf8_decode(number_format($promedio,1)), 0,1,"C");
            
            $autoridad_educativa = "MANUEL CAMACHO HIGAREDA SECRETARIO DE EDUCACIÓN PÚBLICA DE TLAXCALA";
            // Autoridad educativa
            $pdf::setXY(60, 197);
            $pdf::cell(100, 3, utf8_decode($autoridad_educativa), 0,1,"C");

	    // No. certificado autoridad educativa
	    $pdf::setXY(80.4, 199.2);
	    $pdf::cell(100, 3, '0000100000306602988', 0, 1, "L");

            // Sello digital
            $sd = "pKM+7waFqXN67LZ283WMe7u3IQQ9d8Bk+OuNYN9ENPEe8aGKdoHK5P23Uq/NijCIhtFC/jlDI/vYzWEYoVung+aD1fdiAP7CvIXQ9wW6VYdqDoqnLRWhttZxPPjHqS3afOUs3kg0cr7Ah5fjdT479MWqxlje1z7iUbxdJZRoz+M04MeEzOpNCBIeeA7Nc36ddjdYzZ54fV6uDiXe7CgfkGSOfUEQEVkGIFD4JOdrZi+gb61jwCHK+hJc5TJI3S5+9VpZSDIxM06z9jIWEC4PTyDrFST6iCvINWC0lfcwJvdPdvtz/UdKkcFyX2UZJzzhHTmMd1KNAqmPZ9jx2XQbw==";
            $pdf::setXY(45, 206);
            $pdf::MultiCell(155, 2, utf8_decode($sd),0,"J");
            
	    // Fecha y Hora del timbrado
	    $fecha_hora = date("d/m/Y");
	    $pdf::SetFillColor(255,255,255);
			
	    //$fecha = new DateTime('2000-01-01', new DateTimeZone('America/Mexico_city'));
	    // Limpieza de fecha y hora del timbrado (en caso de ser necesario)
	    $pdf::setXY(45, 212.1);
	    $pdf::cell(40,3, '',0,1,"L",true);
	    //$pdf::setXY(71.5,211.5);
	    //$pdf::cell(40, 3, $fecha_hora, 0, 1, "L");

            // Ponemos codigo QR
            $pdf::Image("https://sien.septlaxcala.gob.mx/public/qr-code/".$alumno->id_Alumno,7,210,32,0,'PNG');
            $clave = $alumno->folio_certificado; 
            
	    // Correccion de liga
	    $pdf::SetFillColor(255,255,255);
	    $pdf::setXY(85.4, 236);
	    $pdf::cell(115, 3, utf8_decode('https://sien.septlaxcala.gob.mx/public/certificacion. De igual manera, podrá verificar el documento electrónico por'), 0, 1, "L", true); 

	    // texto: medio del código QR
	    $pdf::setXY(44.8, 238.9);
	    $pdf::cell(40, 2.2, utf8_decode('medio del código QR.'), 0, 1, "L", true);

	    // Folio unico
	    $pdf::SetFont('SoberanaSans-Regular','',8);

	    $pdf::setXY(21.6, 249.1);
            $pdf::cell(15, 3, utf8_decode($clave), 0,1,"C");

        }
        $pdf::Output();
 		exit;
    }
}
