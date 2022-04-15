/// front end javascript

/// variables globales
const regionScript = document.getElementById('departamento');

const home = document.getElementById('tejidohumano');

// 1. departamento js
if(regionScript) {
	let wraper = document.getElementById('deptowrap');
	let expandiv = document.getElementById('proyoverlay');
	let calledYear = ''; 

	function openYear(year, yerbtn) {
		let contenido, contAnt, minplus;
		if (calledYear === '')  {
			contenido = document.getElementById(year);
			contenido.classList.add('unhid');
			calledYear = year;
		} else if(calledYear !== '' && calledYear !== year) {
			contAnt = document.getElementById(calledYear);
			contAnt.classList.remove('unhid');
			contenido = document.getElementById(year);
			contenido.classList.add('unhid');
			calledYear = year;
		} else if (calledYear === year) {
			contenido = document.getElementById(year);
			contenido.classList.remove('unhid');
			calledYear = '';
		}
	}

	function openproy(id) {
		
		expandiv.classList.remove('hidden');

		let stringToSend = 'openarticulo=1' + '&id=' + id;
		
		if (id !== '') {
			var rq = new XMLHttpRequest();
			rq.open ("POST", 'views/proy.php', true);
			rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			rq.onreadystatechange = function() {
				if(rq.status !== 200) {
					// handle error
					alert( 'Error: ' + rq.status);
					return;
				} else if(rq.readyState === 4 && rq.status === 200) {
					//location.reload();
					console.log('llega la respuesta');
					document.getElementById('proyecontenido').innerHTML = rq.response;
				}
			}
			rq.send(stringToSend);
		}

	}

	function closeproy() {
		expandiv.classList.add('hidden');
	}

	function closelist() {
		wraper.classList.remove('loaded');
		wraper.classList.add('unload');
	}

	function reopenlist() {
		wraper.classList.remove('unload');
		wraper.classList.add('loaded');
	}

	window.onload = function() {
		wraper.classList.add('loaded');
	}
}


// 2. home js
if(home) {
	console.log('cargado el home');

	let tejidoelem1 = document.getElementById('elem1');

	function showhideart() {
		tejidoelem1.classList.add('tejidoculto');
	}

	function hideart() {
		tejidoelem1.classList.remove('tejidoculto');
	}
}