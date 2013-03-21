var GLOBALch;

function defch() {
	var defaultch = 280;
	var corr = document.getElementById("corretoR");
	var etc = document.getElementById("etc");
	if (!corr) return;
	if (corr.offsetHeight > defaultch) {
		GLOBALch = corr.offsetHeight;
		corr.style.height = defaultch+'px';
		etc.style.display = "block";
	}
}

function fullch() {
	var corr = document.getElementById("corretoR");
	corr.style.height = GLOBALch+'px';
	etc.style.display = "none";
}
