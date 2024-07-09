import Swiper from '../../../node_modules/swiper/swiper-bundle.js'


/* АККОРДИОН */
function dropdown() {
	const collapses = document.querySelectorAll('[data-cc]');
	if (collapses.length === 0) return false;
	collapses.forEach(collapse => {
		const header = collapse.querySelector('[data-cc-head]')
		const body = collapse.querySelector('[data-cc-body]')
		header.addEventListener('click', event => {
			event.preventDefault();
			collapse.classList.toggle('active');
			$(body).stop().slideToggle();
		})
	});
}
dropdown();


/* ФИЛЬТР ВАКАНСИЙ */
function vacancyFilter() {
	let tabs = document.querySelectorAll('[data-tab-city]');
	if (tabs.length === 0) return false;
	let panels = document.querySelectorAll('[data-panel-city]');
	
	tabs.forEach( tab => {
		tab.addEventListener('click', function(e) {
			e.preventDefault();
			tabs.forEach(tab => {
				tab.classList.remove('active');
			})
			this.classList.add('active');
			let tabCity = this.dataset.tabCity;
			
			panels.forEach(panel => {
				let panelCity = panel.dataset.panelCity;
				if ( panelCity.includes(tabCity) ) {
					panel.style.display = 'block';
				}
				else {
					panel.style.display = 'none';
				}
			})
		})
	})
}
vacancyFilter();


/* АНИМАЦИЯ БУКВ */
let animWords = document.querySelectorAll('.anim-word');
if (animWords.length) {
	animWords.forEach(link => {
		link.innerHTML = '<div><span>' + link.textContent.trim().split('').join('</span><span>') + '</span></div>'
		link.querySelectorAll('span').forEach(s => s.innerHTML = s.textContent == ' ' ? '&nbsp;' : s.textContent)
		link.insertAdjacentHTML('beforeend', '<div class="line-wrap"><svg preserveAspectRatio="none" viewBox="0 0 192 5"><path d="M191.246 4H129C129 4 127.781 4.00674 127 4C114.767 3.89447 108.233 1 96 1C83.7669 1 77.2327 3.89447 65 4C64.219 4.00674 63 4 63 4H0.751923" /></svg></div>')
	});
}

/* ОТЗЫВЫ */
let swiperFeedback;
(function () {
	const slider = document.querySelector('.part-vacancy-feedback .swiper');
	if (!slider) return;
	swiperFeedback = new Swiper(slider, {
		loop: true,
		slidesPerView: 1,
		spaceBetween: 30,
		navigation: {
			nextEl: '.part-vacancy-feedback .arrow-next',
			prevEl: '.part-vacancy-feedback .arrow-prev',
		},
		pagination: {
			el: '.part-vacancy-feedback .swiper-pagination',
			clickable: true
		},
		breakpoints: {
			992: {
				slidesPerView: 3,
			},
			768: {
				slidesPerView: 2,
			},
		}
	});
})();




