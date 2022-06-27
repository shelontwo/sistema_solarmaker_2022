

var date_p = document.getElementById('days');
var start_date = new Date(date_p.innerHTML).getTime();

setInterval(function () {
	var now = new Date().getTime();
	var distance = start_date - now;
	var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	var mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	var secs = Math.floor((distance % (1000 * 60)) / 1000);

	document.getElementById('days').innerHTML = days;
	document.getElementById('hours').innerHTML = hours;
	document.getElementById('mins').innerHTML = mins;
	document.getElementById('secs').innerHTML = secs;

	document.getElementById('days').removeAttribute('style');
}, 1000, start_date, date_p);


/*
	Faz a setinha de scroll down rolar para a seção de eventos
*/
window.scroll_down = function () {
	document.getElementsByClassName('event-section')[0].scrollIntoView({ behavior: 'instant', block: 'center' });
	document.getElementById('home__scroll').style.display = 'none';
}


/*
	Remove todas as seções da home
*/
function removeSections() {
	var section_list = document.getElementsByClassName('spa-section');
	for (var i = (section_list.length - 1); i >= 0; i--) {
		section_list[i].style.display = 'none';
	}

	document.getElementsByClassName('footer')[0].style.display = 'none';
	if (window.screen.width <= 1024) {
		document.getElementsByClassName('passport-info-section')[0].style.display = 'none';
		document.getElementsByClassName('buy-ticket-section')[0].style.display = 'none';
	}

	var spa_headers = document.getElementsByClassName('spa-header');
	for (var i = 0; i < spa_headers.length; i++) {
		spa_headers[i].setAttribute('style', 'display:none !important');
	}
}


/*
	Adiciona todas as seções de volta na home
*/
function addSections() {
	var section_list = document.getElementsByClassName('spa-section');
	for (var i = (section_list.length - 1); i >= 0; i--) {
		section_list[i].removeAttribute('style');
	}
	document.getElementsByClassName('footer')[0].removeAttribute('style');
	if (window.screen.width <= 1024) {
		document.getElementsByClassName('passport-info-section')[0].removeAttribute('style');
		document.getElementsByClassName('buy-ticket-section')[0].removeAttribute('style');
	}

	var spa_headers = document.getElementsByClassName('spa-header');
	for (var i = 0; i < spa_headers.length; i++) {
		spa_headers[i].removeAttribute('style', 'display:none !important');
	}
}


window.load_speakers = function () {
	removeSections();
	ga('gtag_UA_78628666_1.send', 'event', 'Interação com páginas', 'Abriu seção', 'abriu_mais_palestrantes');
	document.getElementsByClassName('spa-header-x')[0].style.display = 'block';
	document.getElementsByClassName('header')[0].classList.add("header-black");
	document.getElementById('home__scroll').setAttribute('style', 'display:none;');
	if (window.screen.width <= 1024) {
		document.getElementsByClassName('buy-ticket-section')[0].setAttribute('style', 'display:none !important');
	}
	window.speaker_on = true;
	$('#speakers-spa').load('/palestrantes');
}


function close_speakers() {
	document.getElementsByClassName('spa-header-x')[0].setAttribute('style', 'display:none !important');
	document.getElementsByClassName('header')[0].classList.remove("header-black");
	var speakers = document.getElementsByClassName('spa__container');
	for (var i = (speakers.length - 1); i >= 0; i--) {
		speakers[i].parentNode.removeChild(speakers[i]);
	}

	for (var j = 2; j >= 0; j--) {
		document.getElementsByClassName('speakers-row')[j].parentNode.removeChild(document.getElementsByClassName('speakers-row')[j]);
	}

	addSections();
	window.speaker_on = false;
}


window.close_speakers_spa = function () {
	close_speakers();
	document.getElementsByClassName('speakers-section')[0].scrollIntoView({ behavior: 'instant', block: 'center' });
}


/*
	Fecha a seção de fotos das edições passadas
*/
window.close_photos = function () {
	close_speakers();
	document.getElementsByClassName('header')[0].classList.remove("header-black");
	document.getElementsByClassName('spa-photos')[0].setAttribute('style', 'display:none !important');
	document.getElementsByClassName('previous-editions')[0].scrollIntoView({ behavior: 'instant', block: 'center' });
}


/*
	Abre a seção de fotos das edições passadas
*/
window.load_photos = function () {
	removeSections();
	ga('gtag_UA_78628666_1.send', 'event', 'Interação com páginas', 'Abriu seção', 'abriu_mais_fotos');
	document.getElementsByClassName('header')[0].classList.add("header-black");
	document.getElementsByClassName('spa-photos')[0].style.display = 'block';
	document.getElementById('home__scroll').setAttribute('style', 'display:none;');
	if (window.screen.width <= 1024) {
		document.getElementsByClassName('buy-ticket-section')[0].setAttribute('style', 'display:none !important');
	}
	$('#photos-spa').load('/edicoes-anteriores');
}


/*
	Abre a seção de informações sobre os passaportes
*/
window.move_left = function () {
	ga('gtag_UA_78628666_1.send', 'event', 'Interação com páginas', 'Abriu seção', 'clicou_comprar');
	document.getElementsByClassName('passport-info-section')[0].classList.add('passport-active');
	document.getElementById('passport-buy-button').setAttribute('onclick', 'close_passports()');
	document.getElementById('home__scroll').setAttribute('style', 'display:none;');
	document.getElementsByClassName('passport-info-section')[0].scrollTo(0, 0);
	if (screen.width <= 1024) {
		document.getElementsByClassName('buy-ticket-section__call')[0].style.top = '65px';
	}
	setTimeout(function () {
		// document.getElementsByClassName('passport-info-section__over')[0].classList.add('passport-hide');
		document.getElementsByClassName('passport-info-section__purple')[0].style.position = 'fixed';
	}, 1000);
}


/*
	Fecha a seção de informações sobre os passaportes
*/
window.close_passports = function () {
	// document.getElementsByClassName('passport-info-section__over')[0].classList.remove('passport-hide');
	document.getElementsByClassName('passport-info-section')[0].classList.remove('passport-active');
	document.getElementsByClassName('buy-ticket-section')[0].removeAttribute('style');
	if (screen.width <= 1024) {
		document.getElementsByClassName('buy-ticket-section__call')[0].style.top = '0';
	}
	document.getElementsByClassName('passport-info-section__purple')[0].style.position = 'absolute';
	document.getElementById('passport-buy-button').setAttribute('onclick', 'move_left()');
}

window.turnCard = function () {
	setTimeout(function () {
		document.getElementsByClassName('modal-body green-section__modal-front')[0].style.display = 'block';
		document.getElementsByClassName('modal-body green-section__modal-back')[0].style.display = 'none';
		document.getElementsByClassName('modal-body green-section__modal-error')[0].style.display = 'none';
	}, 700);
}

if ("__AUTH" in localStorage) {
	document.getElementById("header-button-main").classList.add("focused");
	var submenus = document.getElementById("mobile-menu-main").children;
	submenus[0].style.display = "none";
	submenus[2].style.display = "flex";
	submenus[3].style.display = "flex";
	submenus[4].style.display = "flex";
	submenus[5].style.display = "flex";
} else {
	document.getElementById("header-button-main").classList.remove("focused");
	var submenus = document.getElementById("mobile-menu-main").children;
	submenus[0].style.display = "flex";
	submenus[2].style.display = "none";
	submenus[3].style.display = "none";
	submenus[4].style.display = "none";
	submenus[5].style.display = "none";
}

var menu_mobile_main = true;
window.openMobileMenuMain = function() {
  if ("__AUTH" in localStorage) {
    if (menu_mobile_main) {
      document.getElementById(
        "header-button-main"
      ).childNodes[2].style.display = "flex";
      menu_mobile_main = false;
    } else {
      document.getElementById(
        "header-button-main"
      ).childNodes[2].style.display = "none";
      menu_mobile_main = true;
    }
  }
};

var titles = document.getElementsByClassName("passport-section__title");
for (let i = 0; i < titles.length; i++) {
	titles[i].style.color = titles[i].dataset.color;
}

var value = document.getElementsByClassName("passport-section__value");
for (let i = 0; i < value.length; i++) {
	value[i].style.color = value[i].dataset.color;
}

var passport_list = document.getElementsByClassName("passport-section__card");
for (var k = 0; k < passport_list.length; k++) {
	let ul = passport_list[k].getElementsByTagName("ul")[0];
	if (ul) {
		ul.classList.add("passport-info-section__info");
		var lis = ul.children;
		for (let j = 0; j < lis.length; j++) {
			lis[j].style.display = "flex";
			if (lis[j].children[0]) {
				lis[j].classList.add("passport-info-section__info--strike");
				lis[j].innerHTML = "<img src='/img/small-ga.webp' alt='Bullet da lista'>" + lis[j].innerHTML
			} else {
				lis[j].innerHTML = "<img src='/img/small-ba.webp' alt='Bullet da lista'>" + lis[j].innerHTML;
			}
		}
	}
}