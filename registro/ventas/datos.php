<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ventas / Registro </title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
    body {
        color: #566787;
		background: #f5f5f5;
		font-family: 'Varela Round', sans-serif;
		font-size: 13px;
	}
	.table-responsive {
        margin: 30px 0;
    }
	.table-wrapper {
		min-width: 1000px;
        background: #fff;
        padding: 20px 25px;
		border-radius: 3px;
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }
	.table-title {        
		padding-bottom: 15px;
		background: #435d7d;
		color: #fff;
		padding: 16px 30px;
		margin: -20px -25px 10px;
		border-radius: 3px 3px 0 0;
    }
    .table-title h2 {
		margin: 5px 0 0;
		font-size: 24px;
	}
	.table-title .btn-group {
		float: right;
	}
	.table-title .btn {
		color: #fff;
		float: right;
		font-size: 13px;
		border: none;
		min-width: 50px;
		border-radius: 2px;
		border: none;
		outline: none !important;
		margin-left: 10px;
	}
	.table-title .btn i {
		float: left;
		font-size: 21px;
		margin-right: 5px;
	}
	.table-title .btn span {
		float: left;
		margin-top: 2px;
	}
    table.table tr th, table.table tr td {
        border-color: #e9e9e9;
		padding: 12px 15px;
		vertical-align: middle;
    }
	table.table tr th:first-child {
		width: 60px;
	}
	table.table tr th:last-child {
		width: 100px;
	}
    table.table-striped tbody tr:nth-of-type(odd) {
    	background-color: #fcfcfc;
	}
	table.table-striped.table-hover tbody tr:hover {
		background: #f5f5f5;
	}
    table.table th i {
        font-size: 13px;
        margin: 0 5px;
        cursor: pointer;
    }	
    table.table td:last-child i {
		opacity: 0.9;
		font-size: 22px;
        margin: 0 5px;
    }
	table.table td a {
		font-weight: bold;
		color: #566787;
		display: inline-block;
		text-decoration: none;
		outline: none !important;
	}
	table.table td a:hover {
		color: #2196F3;
	}
	table.table td a.edit {
        color: #FFC107;
    }
    table.table td a.delete {
        color: #F44336;
    }
    table.table td i {
        font-size: 19px;
    }
	table.table .avatar {
		border-radius: 50%;
		vertical-align: middle;
		margin-right: 10px;
	}
    .pagination {
        float: right;
        margin: 0 0 5px;
    }
    .pagination li a {
        border: none;
        font-size: 13px;
        min-width: 30px;
        min-height: 30px;
        color: #999;
        margin: 0 2px;
        line-height: 30px;
        border-radius: 2px !important;
        text-align: center;
        padding: 0 6px;
    }
    .pagination li a:hover {
        color: #666;
    }	
    .pagination li.active a, .pagination li.active a.page-link {
        background: #03A9F4;
    }
    .pagination li.active a:hover {        
        background: #0397d6;
    }
	.pagination li.disabled i {
        color: #ccc;
    }
    .pagination li i {
        font-size: 16px;
        padding-top: 6px
    }
    .hint-text {
        float: left;
        margin-top: 10px;
        font-size: 13px;
    }    
	/* Custom checkbox */
	.custom-checkbox {
		position: relative;
	}
	.custom-checkbox input[type="checkbox"] {    
		opacity: 0;
		position: absolute;
		margin: 5px 0 0 3px;
		z-index: 9;
	}
	.custom-checkbox label:before{
		width: 18px;
		height: 18px;
	}
	.custom-checkbox label:before {
		content: '';
		margin-right: 10px;
		display: inline-block;
		vertical-align: text-top;
		background: white;
		border: 1px solid #bbb;
		border-radius: 2px;
		box-sizing: border-box;
		z-index: 2;
	}
	.custom-checkbox input[type="checkbox"]:checked + label:after {
		content: '';
		position: absolute;
		left: 6px;
		top: 3px;
		width: 6px;
		height: 11px;
		border: solid #000;
		border-width: 0 3px 3px 0;
		transform: inherit;
		z-index: 3;
		transform: rotateZ(45deg);
	}
	.custom-checkbox input[type="checkbox"]:checked + label:before {
		border-color: #03A9F4;
		background: #03A9F4;
	}
	.custom-checkbox input[type="checkbox"]:checked + label:after {
		border-color: #fff;
	}
	.custom-checkbox input[type="checkbox"]:disabled + label:before {
		color: #b8b8b8;
		cursor: auto;
		box-shadow: none;
		background: #ddd;
	}
	/* Modal styles */
	.modal .modal-dialog {
		max-width: 400px;
	}
	.modal .modal-header, .modal .modal-body, .modal .modal-footer {
		padding: 20px 30px;
	}
	.modal .modal-content {
		border-radius: 3px;
	}
	.modal .modal-footer {
		background: #ecf0f1;
		border-radius: 0 0 3px 3px;
	}
    .modal .modal-title {
        display: inline-block;
    }
	.modal .form-control {
		border-radius: 2px;
		box-shadow: none;
		border-color: #dddddd;
	}
	.modal textarea.form-control {
		resize: vertical;
	}
	.modal .btn {
		border-radius: 2px;
		min-width: 100px;
	}	
	.modal form label {
		font-weight: normal;
	}	
	.enlace-columna {
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
        		font-size: 11px;

}
td{
    max-width: 180px;
  

}

.bg-success{
background-color: #4ca457;

}
.bg-warning{
background-color: #d6cd6c;

}
.ventas-totales{
    text-align: center;
    border: 1px solid black; /* Borde de 1px de ancho y color negro */
    display: inline-block; /* Para que el borde se ajuste al contenido */
    width: 200px; /* Ancho del contenedor */
    margin: 0 auto; /* Centrar horizontalmente */
    border-radius: 10px; /* Añadir bordes redondeados */
    

}
/* Estilos para el loader */
.loader-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Estilos para el contenido principal */
.content {
  display: none;
  /* Estilos adicionales para tu contenido */
}
</style>
<script>


$(document).ready(function(){
	// Activate tooltip
	$('[data-toggle="tooltip"]').tooltip();
	
	// Select/Deselect checkboxes
	var checkbox = $('table tbody input[type="checkbox"]');
	$("#selectAll").click(function(){
		if(this.checked){
			checkbox.each(function(){
				this.checked = true;                        
			});
		} else{
			checkbox.each(function(){
				this.checked = false;                        
			});
		} 
	});
	checkbox.click(function(){
		if(!this.checked){
			$("#selectAll").prop("checked", false);
		}
	});
});




function obtenerValoresDesdeDatosPHP() {
    $.ajax({
        url: 'php/datos.php',
        method: 'GET',
        success: function(response) {
            // Separar los valores utilizando el signo '$'
            var valores = response.split('$');

            // Si hay tres valores obtenidos, asignarlos a elementos HTML
            if (valores.length === 5) {
                var tercerDato = '$' + valores[1]; // El segundo valor corresponde al tercer dato
                var ecuadorPendiente = '$' + valores[2]; // El tercer valor corresponde a Ecuador Pendiente
                var totalHoy = '$' + valores[3]; // El cuarto valor corresponde al Total del día
               var total = '$' + valores[4]; // El cuarto valor corresponde al Total del día


                // Mostrar los valores en los elementos HTML
                $('#pendienteEcuador').html(tercerDato);
                $('#pendientePeru').html(ecuadorPendiente);
                $('#totalHoy').html(totalHoy);
                                $('#total').html(total);

            } else {
                // En caso de un formato incorrecto de la respuesta
                $('#totalHoy').html('Formato de respuesta incorrecto');
            }
        },
        error: function() {
            $('#totalHoy').html('Error al obtener la suma de ventas.');
        }
    });
}


obtenerValoresDesdeDatosPHP();
        
</script>
</head>
<body>
    
    
     <div class="loader-container" id="loader">
    <div class="loader"></div>
  </div>
    
    
        <div class="container-fluid">
            <div class="row">
              
            
    </div>
    </div>

    
    
    
    
    <div class="container">
		<div class="table-responsive">
			<div class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-xs-9">
							 <input type="text" id="buscar" style="width: 200px;
  border: 1px solid #ccc;
  padding: 10px;
    color: white;
    background-color:inherit;
    
  border-radius: 10px;

  outline: none;" placeholder="Buscar..." />
  

  
   <div class=" ventas-totales">
                 <h5> Total <span class="badge bg-success" id="total"></span></h5>
            
    </div>
     <div class=" ventas-totales">
                 <h5> Hoy <span class="badge bg-success" id="totalHoy"></span> 
                 
                 </h5>

            
    </div>
      <div class=" ventas-totales" >
                 <h5>Ecuador <span class="badge bg-success" id="pendienteEcuador"></span>
                 
                 <a href="#"  class="btnEditar delete" id="deleteEcuador" ><i class="material-icons" style="font-size:15px;" data-toggle="tooltip" title="Delete">&#x267B;</i></a>
                 </h5>

            
    </div>
     <div class=" ventas-totales" >
                 <h5>Peru <span class="badge bg-success" id="pendientePeru"></span>
                                  <a href="#"  class="btnEditar delete" id="deletePeru" ><i class="material-icons" style="font-size:15px;" data-toggle="tooltip" title="Delete">&#x267B;</i></a>

                 </h5>

            
    </div>
  			</div>
						 
    

						<div class="col-xs-3">
							
							<a href="#deleteEmployeeModal" class="btn btn-danger"  data-toggle="modal"><i class="material-icons">&#xE15C;</i> <span>Borrar</span></a>	
							<a href="#addEmployeeModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Agregar Ventas</span></a>
                            
						</div>
					</div>
				</div>
				    <div class="container">

				<table class="table table-striped table-hover" id="miTabla">
					<thead>
						<tr>
							<th>
								Elegir
							</th>
														<th>Fecha</th>

						<!--	<th>Tienda Online</th>
							<th>País</th>
							<th>Número de celular</th>
							<th>Red social</th>
							<th>Servicios</th>
							<th>Cantidad</th>
							<th>Enlace</th>
							<th>Método de pago	</th>
							<th>Monto</th>
							<th>Estado</th>-->
							<th>País</th>
							<th>Contacto</th>
							<th>Monto</th>
                            <th>Estado</th>


						</tr>
					</thead>
										<tbody id="datosBody">
										    
					</tbody>

				</table>
				</div>
			</div>
		</div>        
    </div>
    


	<!-- add Modal HTML -->
	<div id="addEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id="formAgregarVenta" action="php/ventas.php" method="post">
					<div class="modal-header">						
						<h4 class="modal-title">Agregar Venta</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
					
					
						<div class="form-group">
							<label>Pais</label>
							<select class="form-control modal-select" id="pais" name="pais" >
        <option value="Venezuela">Venezuela</option>
        <option value="Peru">Peru</option>
             <option value="Ecuador">Ecuador</option>
        <option value="Chile">Chile</option>


      </select>
						</div>
						
							<div class="form-group">
							<label>Contacto</label>
							<input type="text" class="form-control" id="numero_celular" name="numero_celular" required>
						</div>
						
					
						
					
			
						
					
						
						
						
						<div class="form-group">
							<label>Monto</label>
							<input type="text" class="form-control" id="monto" name="monto" required>
						</div>
						
						
					
						<div class="form-group">
							<label>Estado</label>
							<select class="form-control modal-select" id="estado" name="estado" >
        <option value="Completado">Completado</option>
        <option value="Pendiente">Pendiente</option>
             <option value="Por confirmar">Por confirmar</option>


      </select>
						</div>
					
						
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="button" class="btn btn-success" data-dismiss="modal" value="Agregar" id="btnAgregarVenta">
					</div>
				</form>
			</div>
		</div>
	</div>
	
	
	<!-- Edit Modal HTML -->
	<div id="editEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="php/edit.php" method="post">
					<div class="modal-header">						
						<h4 class="modal-title">Editar Venta</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
					    
					    	<div class="form-group">
							<label>ID</label>
							<input type="number" class="form-control" id="id" name="id" required>
						</div>
					    					
						
						
					
						
						
						
						
						
						
						
						
							
						
						
						
					
						
						
							<div class="form-group">
							<label>Estado</label>
							<select class="form-control modal-select" id="estado" name="estado" >
        <option value="Pendiente">Pendiente</option>
        <option value="Procesado">Procesado</option>
             <option value="Completado">Completado</option>
      </select>
						</div>

						
						
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-success" value="Editar">
					</div>
				</form>
			</div>
		</div>
	</div>
	
	
	<!-- Delete Modal HTML -->
	<div id="deleteEmployeeModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
					<div class="modal-header">						
						<h4 class="modal-title">Borrar Ventas</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<p>Estas seguro de borrar estos registros?</p>
						<p class="text-warning"><small>Esta accion no se puede deshacer.</small></p>
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="button" id="eliminarFilas" data-dismiss="modal"  class="btn btn-danger" value="Borrar">
					</div>
				</form>
			</div>
		</div>
	</div>
	
	

	
	
	
	
	
<script>
function obtenerDatos() {

    fetch('php/tabla.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('datosBody');
            tbody.innerHTML = '';
           



            let montoTotal = 0;
          
            

            data.forEach(rowData => {
                const tr = document.createElement('tr');




                // Obtener todas las filas de la tabla
const rows = document.querySelectorAll('tr');

// Ocultar todas las celdas de la segunda columna
rows.forEach(row => {
  const cells = row.querySelectorAll('td:nth-child(2)');
  cells.forEach((cell, index) => {
    if (index !== row.cells.length - 1) {
      cell.style.display = 'none';
    }
  });
});
                
                     // Crea el elemento span para el checkbox personalizado
const checkboxSpan = document.createElement('span');
checkboxSpan.classList.add('custom-checkbox');

// Crea el elemento input para el checkbox
const checkboxInput = document.createElement('input');
checkboxInput.type = 'checkbox';
checkboxInput.id = 'checkbox2';
checkboxInput.name = 'options[]';
checkboxInput.value = rowData.id;

// Crea el elemento label para el checkbox
const checkboxLabel = document.createElement('label');
checkboxLabel.setAttribute('for', 'checkbox2');

// Añade el checkboxInput y el checkboxLabel al checkboxSpan
checkboxSpan.appendChild(checkboxInput);
checkboxSpan.appendChild(checkboxLabel);

// Añade el checkboxSpan a la celda de la primera columna
const checkboxTd = document.createElement('td');
checkboxTd.appendChild(checkboxSpan);

// Añade la celda al tr
tr.appendChild(checkboxTd);



                // En el bucle que crea las celdas de la tabla
                Object.entries(rowData).forEach(([key, value], index, arr) => {
                    const td = document.createElement('td');
                    
                    
                    
                    

                    // Verificar si la clave es 'estado'
                    if (key === 'estado') {
                        const span = document.createElement('span');
                        span.textContent = value;
                        span.classList.add('badge', 'rounded-pill');

                        // Asignar clases según el valor del estado
                        if (value === 'Completado') {
                            span.classList.add('bg-success'); // Clase para estado Completado
                        } else if (value === 'Pendiente') {
                            span.classList.add('bg-warning'); // Clase para estado Pendiente
                        }

                        td.appendChild(span);
                    } else if (key === 'enlace') {
                        const anchor = document.createElement('a');
                        anchor.href = value; // Establecer el valor del enlace como la URL
                        anchor.textContent = value; // Usar el valor como texto visible
                        anchor.classList.add('enlace'); // Asignar la clase 'enlace' al enlace

                        td.appendChild(anchor);
                        td.classList.add('enlace-columna'); // Agregar la clase 'enlace-columna' a la celda de la columna de enlaces
                    }
                    else if (key === 'monto') {
                        
                        var spanMonto = document.getElementById("total");

                        const span = document.createElement('span');
                        span.textContent = value;
                                                td.appendChild(span);
                                                
                                            
                                                
          
    
    
    
    

                    }
                    
                    else {
                        const span = document.createElement('span');
                        span.textContent = value;
                        span.classList.add('truncate-text');
                        span.style.display = 'inline-block';
                        td.appendChild(span);
                        
                      
                        
                        
                    }

                    tr.appendChild(td);
                    
                    
                });
                

               // Crea el td para los botones
const botonesTd = document.createElement('td');

// Crea los botones
const editAnchor = document.createElement('a');
editAnchor.href = "#editEmployeeModal";
editAnchor.classList.add('btnEditar', 'edit');
editAnchor.setAttribute('data-toggle', 'modal');
editAnchor.innerHTML = '<i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>';


    //       const deleteAnchor = document.createElement('a');
//deleteAnchor.href = "#deleteEmployeeModal";
//deleteAnchor.classList.add('btnEditar', 'delete');
//deleteAnchor.setAttribute('data-toggle', 'modal');
//deleteAnchor.innerHTML = '<i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>';


//const viewNotes = document.createElement('a');
//viewNotes.href = "#NotasEmployeeModal";
//viewNotes.classList.add('btnEditar', 'view');
//viewNotes.setAttribute('data-toggle', 'modal');
//viewNotes.innerHTML = '<i class="material-icons" data-toggle="tooltip" title="View">&#x1F441;</i>';


// Añade los botones al td
botonesTd.appendChild(editAnchor);
//botonesTd.appendChild(deleteAnchor);
//botonesTd.appendChild(viewNotes);

// Añade el td a la fila
tr.appendChild(botonesTd);
      // Añade el tr al tbody
      tbody.appendChild(tr);
      





            
            // Obtienes la segunda columna de la tabla
const tdss = document.querySelectorAll('td:nth-child(14)');

// Ocultas la columna
for (const td of tdss) {
  td.style.display = 'none';
}
            
            
            });
// Obtén la fecha actual en formato YYYY-MM-DD de Venezuela
const fechaActual = new Date().toLocaleString('en-US', { timeZone: 'America/Caracas' }).split(',')[0].split('/');
const todayString = `${fechaActual[2]}-${fechaActual[0]}-${fechaActual[1]}`;



            
          
        })
        .catch(error => {
            console.error('Error al obtener los datos:', error);
        });
        
}
obtenerDatos();


// Llamar a obtenerDatos cada cierto intervalo de tiempo (por ejemplo, cada 5 segundos)
// setInterval(obtenerDatos, 000); // Intervalo en milisegundos (5000ms = 5 segundos)
</script>
<script>
  function obtenerValor(boton) {
    // Obtener la fila actual
    var fila = boton.parentNode.parentNode;
    
    // Obtener el checkbox en la misma fila
    var checkbox = fila.querySelector('input[type="checkbox"]');
    
    // Obtener el valor del checkbox
    var valor = checkbox.value;
    
    // Mostrar el valor (solo para propósitos de demostración)
    console.log("El valor del checkbox es: " + valor);
  }
  
  document.addEventListener("DOMContentLoaded", function() {
    var botones = document.querySelectorAll('.btnEditar');
    botones.forEach(function(boton) {
      boton.addEventListener('click', function() {
        obtenerValor(this);
      });
    });
  });
</script>

<script>
$(document).ready(function() {
    $('#eliminarFilas').on('click', function() {
        var filasAEliminar = [];
                                            var modalDelete = document.getElementById("deleteEmployeeModal");
var modalBackdrop = document.querySelector('.modal-backdrop.fade.in');


        // Recopilar los valores de los checkboxes seleccionados
        $('#checkbox2:checked').each(function() {
            var id = $(this).val().replace('[', '').replace(']', '');
            filasAEliminar.push(parseInt(id));
        });

        console.log(filasAEliminar); // Verificar los IDs recopilados

        // Enviar una solicitud AJAX para eliminar las filas directamente
        $.ajax({
            url: 'php/delete.php',
            method: 'POST',
            data: { service: filasAEliminar },
            success: function(response) {

                console.log(response); // Mostrar respuesta del servidor
                if (response === "Eliminación exitosa") {
                    obtenerDatos();

                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});


$(document).ready(function() {
    $('#deletePeru').on('click', function() {
        // Enviar una solicitud AJAX para ejecutar el script PHP
        $.ajax({
            url: 'php/deleteDatosPeru.php',
            method: 'GET', // Utilizar el método GET para ejecutar el script sin enviar datos
            success: function(response) {
                console.log(response); // Mostrar respuesta del servidor
                if (response === "Eliminación exitosa") {
                    
obtenerValoresDesdeDatosPHP();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});

$(document).ready(function() {
    $('#deleteEcuador').on('click', function() {
        // Enviar una solicitud AJAX para ejecutar el script PHP
        $.ajax({
            url: 'php/deleteDatosEcuador.php',
            method: 'GET', // Utilizar el método GET para ejecutar el script sin enviar datos
            success: function(response) {
                console.log(response); // Mostrar respuesta del servidor
                if (response === "Eliminación exitosa") {
                    
            obtenerValoresDesdeDatosPHP();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});


const table = document.querySelector("table");

function filterTable(query) {
  // Obtenemos la tabla
  const table = document.querySelector("table");

  // Filtramos la tabla
  table.querySelectorAll("tbody tr").forEach((row) => {
    // Comparamos el texto escrito con el valor de cada celda
    for (let i = 0; i < row.querySelectorAll("td").length; i++) {
      const cell = row.querySelectorAll("td")[i];
      const value = cell.textContent;

      if (value.toLowerCase().includes(query.toLowerCase())) {
        row.style.display = "table-row";
        return;
      }
    }

    // Si no se encuentra ninguna coincidencia, ocultamos la fila
    row.style.display = "none";
  });
}

const input = document.getElementById("buscar");

input.addEventListener("input", () => {
  // Obtenemos la consulta de búsqueda
  const query = input.value;

  // Filtramos la tabla
  filterTable(query);
});


 window.addEventListener('load', function() {
      const loader = document.getElementById('loader');
      const content = document.getElementById('content');
      
      // Ocultar el loader después de 2 segundos (2000 milisegundos)
      setTimeout(function() {
        loader.style.display = 'none';
        content.style.display = 'block';
      }, 2000); // Cambiar el valor aquí para ajustar el tiempo de espera en milisegundos
    });



var modalAgregar = document.getElementById("AddEmployeeModal");
// Seleccionar el formulario y el botón de agregar
const formAgregarVenta = document.getElementById('formAgregarVenta');
const btnAgregarVenta = document.getElementById('btnAgregarVenta');
+
// Agregar un listener para capturar el evento clic del botón de agregar
btnAgregarVenta.addEventListener('click', function(event) {
    event.preventDefault(); // Prevenir el comportamiento predeterminado del botón (enviar el formulario)

    // Obtener los datos del formulario
    const formData = new FormData(formAgregarVenta);

    // Enviar una solicitud fetch usando POST al archivo PHP
    fetch('php/ventas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Manejar la respuesta si es necesario
        // Por ejemplo, si deseas hacer algo con la respuesta del servidor
        console.log('Solicitud enviada con éxito');
          obtenerDatos();
          obtenerValoresDesdeDatosPHP();

                     setTimeout(function() {
        modalAgregar.style.display = "none";
    }, 1000); // Retrasar la ejecución durante 1 segundo (1000 milisegundos)

    })
    .catch(error => {
        // Manejar errores en caso de que la solicitud falle
        console.error('Error al enviar la solicitud:', error);
    });
});





</script>

	
	
</body>
</html>