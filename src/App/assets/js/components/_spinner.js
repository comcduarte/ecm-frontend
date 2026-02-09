const spinner = document.querySelector(".spinner-wrapper");

window.addEventListener('load', () => {
//	spinner.style.opacity = '0';
	
	setTimeout(() => {
//		spinner.style.display = 'none';
	}, 2000);
});

export function showSpinner() {
	spinner.style.opacity = ".5"; 
	spinner.style.zIndex = "9999";
}

window.addEventListener('click', (e) => {
	const link = e.target.closest("a");
	if (!link) return;
	
	const isNavigationLink =
	  link.origin === location.origin &&
	  !link.hash &&
	  link.target !== "_blank" &&
	  link.dataset.bsToggle !== "modal";

	if (!isNavigationLink) return;
	
	showSpinner();
});