<script>
	let asidePaddingTop;
	let sectionPaddingTop;
	let coverSlid = false;
	const hideCover = Boolean({{$hide_cover}});
	let coverHeight;

	document.addEventListener('DOMContentLoaded', function() {
		const coverPhoto = document.getElementById('cover-photo');
		if (!coverPhoto) return;

		// If screen width is smaller than 755px, remove the cover photo
		if (window.innerWidth < 755) {
			coverPhoto.remove();
			coverSlid = true;
			return;
		}

		// Otherwise, set up the cover photo's size
		coverPhoto.classList.remove('d-none');
		coverHeight = Math.round(window.innerWidth / 2.75862069);
		coverPhoto.style.height = `${coverHeight}px`;
		datasrc2src('#cover-photo > img');

		// Bind events for hiding the cover
		document.addEventListener('click', slideUpCover);
		document.addEventListener('mouseup', slideUpCover);
		document.addEventListener('keyup', slideUpCover);

		// Hide the cover if required
		if (hideCover) {
			hideCoverFunction();
		} else if (!coverSlid) {
			coverVisibleActions();
		}

		// Throttle scroll event
		let lastScrollTop = 0;
		const scrollThreshold = 100;
		window.addEventListener('scroll', function() {
			const scrollTop = window.scrollY;
			if (Math.abs(scrollTop - lastScrollTop) < scrollThreshold) {
				return;
			}
			lastScrollTop = scrollTop;
			handleScroll(scrollTop);
		});

		// Adjust cover photo on resize
		window.addEventListener('resize', function() {
			if (!coverPhoto) return;

			coverHeight = Math.round(window.innerWidth / 2.75862069);
			coverPhoto.style.height = `${coverHeight}px`;

			if (window.innerWidth < 755) {
				coverPhoto.remove();
				coverHiddenActions();
				coverSlid = true;
			}
		});
	});

	// Scroll event handling
	function handleScroll(scrollTop) {
		const coverPhoto = document.getElementById('cover-photo');
		if (!coverPhoto) return;

		if (scrollTop >= coverHeight) {
			coverHiddenActions();
			coverSlid = true;
		} else if (scrollTop < coverHeight) {
			if (coverSlid) {
				document.body.style.overflow = 'hidden'; // Disable scrolling
				window.scrollTo(0, coverHeight);
				setTimeout(function() {
					coverSlid = false;
					document.body.style.overflow = 'auto'; // Enable scrolling
				}, 500);
			} else {
				coverVisibleActions();
			}
		}

		// Fade effect for main content opacity
		const mainElement = document.querySelector('main');
		if (mainElement && parseFloat(getComputedStyle(mainElement).opacity) < 1) {
			mainElement.style.opacity = (scrollTop / coverHeight).toFixed(1);
		}
	}

	// Functions to handle showing/hiding the cover
	function slideUpCover() {
		if (coverSlid) return;
		window.scrollTo({
			top: coverHeight,
			behavior: 'smooth'
		});
	}

	function hideCoverFunction() {
		if (coverSlid) return;
		window.scrollTo(0, coverHeight);
	}

	// Actions when the cover is visible
	function coverVisibleActions() {
		document.body.style.cursor = 'n-resize';
		const navbar = document.getElementById('navbar-top');
		if (navbar) navbar.classList.remove('fixed-top');
		const mainElement = document.querySelector('main');
		if (mainElement) mainElement.style.opacity = 0;
	}

	// Actions when the cover is hidden
	function coverHiddenActions() {
		document.body.style.cursor = '';
		const navbar = document.getElementById('navbar-top');
		if (navbar) navbar.classList.add('fixed-top');
		const mainElement = document.querySelector('main');
		if (mainElement) mainElement.style.opacity = 1;
	}
</script>

<div class="d-none" id="cover-photo" title="{{$hovertitle}}">
	{{$photo_html}}
	<div id="cover-photo-caption">
		<h1>{{$title}}</h1>
		<h3>{{$subtitle}}</h3>
	</div>
</div>
