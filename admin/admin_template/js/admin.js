///// JS file


////////////////////////////// variables y DOM globales
const artMenu = document.getElementById('articulo_menu');
const regionMenu = document.getElementById('region_menu');
const progMenu = document.getElementById('programa_menu');
const proyecto = document.getElementById('proyectFilter');
const addProyect = document.getElementById('add_proyecto');
const checkAll = document.getElementById('checkall');
const uProj = document.getElementById('up_proy');
const userMenu = document.getElementById('user_menu');
const deLogo = document.getElementById('dele_logo');
let checkCall = false;
let called = ''; 

/// elementos para quitar tíldes de las string que lo requieran
var chars={
	"á":"a", "é":"e", "í":"i", "ó":"o", "ú":"u",
	"à":"a", "è":"e", "ì":"i", "ò":"o", "ù":"u", "ñ":"n",
	"Á":"A", "É":"E", "Í":"I", "Ó":"O", "Ú":"U",
	"À":"A", "È":"E", "Ì":"I", "Ò":"O", "Ù":"U", "Ñ":"N"
}
var expr=/[áàéèíìóòúùñ]/ig;


////////////////////////////// Funciones x Seccion

// --> artíiculos
if(artMenu) {
	const btnDeleteArticle = document.getElementById('delete_art');
	const articleNodes = document.querySelectorAll('.artcheck');
	let chekedArticles = [];

	checkAll.addEventListener('input', function(evt) {
		checkAllInputs(articleNodes);
	});

	btnDeleteArticle.addEventListener('click', function() {
		deleteElement(articleNodes, chekedArticles, 'Artículo', 'functions/d_element.php');
	});	
}
// --> regiones
if(regionMenu) {
	const listenArea = document.getElementById('proyecto-list');
	const imgDiv = document.getElementById("foto-preview");
	const btnDeleteRegion = document.getElementById('delete_reg');
	const addedRegion = document.getElementById('advertencia');
	let viewImage = document.querySelectorAll('.region-foto i');
	
	listenArea.addEventListener('click', function(e) {
		viewImage = Array.from(viewImage);
		if(e.target !== viewImage[0] && viewImage.includes(e.target)) {
			let url = e.target.parentNode.title;
			imgDiv.classList.toggle('hid');
			imgDiv.innerHTML = '<div id="fp-wrap"><i class="far fa-times-circle" id="closeimg"></i><img src="' + url + '"></div>';
			const close = document.getElementById('closeimg');
			close.addEventListener('click', function() {
				imgDiv.classList.toggle('hid');
				imgDiv.innerHTML = '';
			});
		}
	});

	const regionNodes = document.querySelectorAll('.itemcheck');
	let chekedRegion = [];

	checkAll.addEventListener('input', function(evt) {
		checkAllInputs(regionNodes);
	});

	btnDeleteRegion.addEventListener('click', function() {
		if(!addedRegion) {
			deleteElement(regionNodes, chekedRegion, 'Region', 'functions/d_element.php');
		} else {
			alert('No es posible borrar inmediatamente despues de agregar/borrar una región, intente de nuevo');
			location.reload();
		}
	});

}
// --> programas
if(progMenu) {
	const btnDeleteProg = document.getElementById('delete_prog');
	const nodes = document.querySelectorAll('.edlistener');
	let nodeArray = [];
	for(i=0; i < nodes.length; i++) {
		nodeArray.push(nodes[i].id);
	}
	if(nodeArray.length !== 0) {
		for(i=0; i < nodeArray.length; i++) {
			let cur = 'prog_' + ( i + 1 );
			document.getElementById(nodeArray[i]).addEventListener('click', function() {
				openInlineEdit(cur);
			});
			console.log(cur);
		}	
	}

	const progNodes = document.querySelectorAll('.itemcheck');
	let chekedProgs = [];

	checkAll.addEventListener('input', function(evt) {
		checkAllInputs(progNodes);
	});

	btnDeleteProg.addEventListener('click', function() {
		deleteElement(progNodes, chekedProgs, 'Programa', 'functions/d_element.php');
	});

}
// --> Proyectos
if(proyecto) {

	const dataInner = document.getElementById('filter_store').innerHTML;

	if(dataInner !== '') {
		const regInner = document.getElementById('reg-data');
		const yerInner = document.getElementById('yer-data');
		const limInner = document.getElementById('limit-data');
		if(regInner) {
			selectFilter('region_filter', regInner.innerHTML);
		}
		if(yerInner) {
			selectFilter('year_filter', yerInner.innerHTML);
		}
		if(limInner) {
			selectFilter('limite', limInner.innerHTML);
		}
	} 

	function selectFilter(id, select) {
		let selector = document.getElementById(id);
		for (i = 0; i < selector.length; i++) {
		    if(selector.options[i].value === select) {
		    	selector.selectedIndex = selector.options[i].index;
		    }
		}
	}

	const btnDeleteProy = document.getElementById('delete_proy');
	const proyNodes = document.querySelectorAll('.checkproy');
	let chekedProy = [];

	checkAll.addEventListener('input', function(evt) {
		checkAllInputs(proyNodes);
	});
	
	btnDeleteProy.addEventListener('click', function() {
		deleteElement(proyNodes, chekedProy, 'Proyecto', 'functions/d_element.php');
	});
	
}
// --> añadir proyecto
if(addProyect) {
	const sentProgData = document.querySelectorAll('.sent-progdata');
	const sentRegData = document.querySelectorAll('.sent-regdata');
	if(sentProgData.length > 0) {
		let nodeChecks = document.querySelectorAll('input[type=checkbox]');
		for( i = 0 ; i < progsArray.length; i++ ) {
			for( b = 0 ; b < nodeChecks.length; b++ ) {
				if (nodeChecks[b].value === progsArray[i] ) {
					if(nodeChecks[b].checked === false) {
						nodeChecks[b].checked = true;
					}
				}
			}
		}
	}
	if(sentRegData.length > 0) {
		let selectorRegion = document.getElementById('reg-selec');
		for (i = 0; i < selectorRegion.length; i++) {
		    if(selectorRegion.options[i].value === selectRegion) {
		    	selectorRegion.selectedIndex = selectorRegion.options[i].index;
		    }
		}
	}
}
// --> actualizar proyecto
if(uProj) { 
	const btnDeleteProj = document.getElementById('dele_foto');
	const faltanteArray = Array.from(document.querySelectorAll('.faltante'));
	const faltanteArea = document.getElementById('faltante_msg');

	if(faltanteArray) {
		console.log(faltanteArray);
		if(faltanteArray.length === 1) {
			faltanteArea.classList.remove('hid');
			faltanteArea.innerHTML = '<span class="faltante"><b>El programa marcado así</b></span> no se encuentra en la lista de programas pero está en este proyecto. Si requiere volver a ingresarlo hágalo en el link de arriba, de lo contrario si mantiene seleccionada la opción, será guardado dicho programa solo en este proyecto.';
		} else if(faltanteArray.length > 1) {
			faltanteArea.classList.remove('hid');
			faltanteArea.innerHTML = '<span class="faltante"><b>Los programas marcados así</b></span> no se encuentran en la lista de programas pero están en este proyecto. Si requiere volver a ingresarlos hágalo en el link de arriba, de lo contrario si mantiene seleccionadas las opciónes, serán guardados dichos programas solo en este proyecto.';
		}
	}

	if(btnDeleteProj) { 
		btnDeleteProj.addEventListener('click', function() {

			let imgURL = document.getElementById('imgurl').innerHTML; 
			let id = document.getElementById('imgurl').title;
			let rqVal = confirm('¿Está seguro de eliminar  la foto : "' + imgURL + '" ?');
	
			let stringToSend = 'deleteFoto=' + id + '&url=' + imgURL;

			if (rqVal === true) {
				var rq = new XMLHttpRequest();
				rq.open ("POST", 'functions/d_element.php', true);
				rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				rq.onreadystatechange = function() {
					if(rq.status !== 200) {
						// handle error
					    alert( 'Error: ' + rq.status);
					    return;
					} else if(rq.readyState === 4 && rq.status === 200) {
						location.reload();
					}
				}
				rq.send(stringToSend);
			}
		});
	}
}
// --> usuarios
if(userMenu) {
	const btnDeleteUser = document.getElementById('delete_user');
	const userNodes = document.querySelectorAll('.checkuser');
	let chekedUser = [];
	let isSelected = false;

	checkAll.addEventListener('input', function(evt) {
		// ultima alerta por usuarios  y variables a usar, uso especial de checkall para evitar lios en usuarios.
		let alertUsers = Array.from(userNodes);
		if(alertUsers.length > 1) {
			// 1. si esta deseleccionada seleccionar y confirmar
			if(isSelected === false) {
				let userVal = confirm('No recomendamos borrar más de un usuario a la vez, ¿está seguro de continuar?');
				if(userVal === true) {
					checkAllInputs(userNodes);
					isSelected = true;
				} else {
					checkAll.checked = false;
				}
			} else if(isSelected === true) {
				// 2. si esta seleccionada deseleccionar sin confirmar
				checkAllInputs(userNodes);
				isSelected = false;
			}
			
		}
	});

	btnDeleteUser.addEventListener('click', function() {
		deleteElement(userNodes, chekedUser, 'Usuario', 'functions/d_element.php');
	})
}

if(deLogo) {
	const btnDeleteLogo = document.getElementById('dlbot');
	const logoUrl = document.getElementById('url').innerHTML;

	btnDeleteLogo.addEventListener('click', function() {
		console.log(logoUrl);
	})
	console.log('cargo');
}
////////////////////////////// Funciones re-usables
function checkAllInputs(nodes) {
	if(checkCall === false) {		
		for (i = 0; i < nodes.length; ++i) {
			nodes[i].checked = true;
		}
		checkCall = true;
	} else if(checkCall === true) {
		for (i = 0; i < nodes.length; ++i) {
			nodes[i].checked = false;
		}
		checkCall = false;
	}
}

function editElement(nodes, arr, type) {
	for(i = 0; i < nodes.length; i++) {
		if(nodes[i].checked == true) {
			arr.push(nodes[i].value);
		}
	}
	//// quitar tildes
	var stripedType = type.toLowerCase();
	stripedType = stripedType.replace(expr, function(e) {return chars[e]});	
	//// continuar
	if(arr.length === 1) {
		window.location.href="index.php?update_" + stripedType + "=" + arr[0];
	} else if(arr.length === 0) {
		alert('Seleccione un ' + type + ' para editar');
	} else {
		alert('Solo puede editar un ' + type + ' a la vez, seleccione solo uno');
	}
	arr = []; // vaciar el array al final evitar errores en el array seleccionado.
}

function deleteElement(nodes, arr, type, file) {
	for (i = 0; i < nodes.length; i++) {
		if(nodes[i].checked == true) {
			arr.push(nodes[i].value)
		}
	}
	let plural = '';
	if(arr.length !== 0) {
		var arrayToSend = '';
		//// quitar mayúsculas y tíldes
		var stripedType = type.toLowerCase();
		stripedType = stripedType.replace(expr, function(e) {
						return chars[e]
					  });	
		//// continuar
		arrayToSend += 'delete' + stripedType + '=1';
		for(i = 0; i < arr.length; i++ ) {
			arrayToSend += '&id' + i + '=' +  arr[i];
		}

		//console.log(arrayToSend);
		if(nodes.length > 1) {
			plural = 'estos elementos';
		} else {
			plural = 'este elemento';
		}


		var rqVal = confirm("¿Está seguro de eliminar " + plural + "?");
		let status = document.getElementById('status'); /// borrar
		
		if (rqVal == true) {
			//console.log('hm?');/// borrar
			var rq = new XMLHttpRequest();
			rq.open ("POST", file, true);
			rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			rq.onreadystatechange = function() {
				//console.log('llegamos aca? ' + type); /// borrar
				if(rq.status !== 200) {
					// handle error
				    alert( 'Error: ' + rq.status);
				    return;
				} else if(rq.readyState === 4 && rq.status === 200) {
					location.reload();
					/*if(status) { /// borrar
						status.innerHTML = rq.responseText; /// borrar
					} /// borrar*/
				}
			}
			rq.send(arrayToSend);

			arr = [];
		}

	} else {
		alert('No hay usuarios seleccionados para borrar');
	}
}

function openInlineEdit(el) {
	if(called !== '' && called !== el) {
		document.getElementById(called).classList.toggle('hid');
		document.getElementById(el).classList.toggle('hid');
		called = el;
	} else if(called === el) {
		document.getElementById(el).classList.add('hid');
		called = '';
	} else {
		document.getElementById(el).classList.toggle('hid');
		called = el;
	}
	
	
}