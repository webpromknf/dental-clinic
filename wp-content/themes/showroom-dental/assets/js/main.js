(function () {
	"use strict";

	var nav = document.querySelector("[data-nav]");
	var toggle = document.querySelector("[data-nav-toggle]");
	if (nav && toggle) {
		toggle.addEventListener("click", function () {
			var open = nav.classList.toggle("is-open");
			toggle.setAttribute("aria-expanded", open ? "true" : "false");
		});
		nav.querySelectorAll("a").forEach(function (a) {
			a.addEventListener("click", function () {
				nav.classList.remove("is-open");
				toggle.setAttribute("aria-expanded", "false");
			});
		});
	}

	function showToast(message) {
		var toast = document.querySelector("[data-toast]");
		if (!toast) return;
		toast.textContent = message;
		toast.hidden = false;
		clearTimeout(showToast.timer);
		showToast.timer = setTimeout(function () { toast.hidden = true; }, 2800);
	}

	document.querySelectorAll("[data-before-after]").forEach(function (wrap) {
		var range = wrap.querySelector("input[type='range']");
		var after = wrap.querySelector(".after");
		if (!range || !after) return;
		function update() { after.style.width = range.value + "%"; }
		range.addEventListener("input", update);
		update();
	});

	document.querySelectorAll("[data-booking-form]").forEach(function (form) {
		var current = 1;
		var total = 5;
		var prev = form.querySelector("[data-prev]");
		var next = form.querySelector("[data-next]");
		var status = form.querySelector("[data-form-status]");
		var summary = form.querySelector("[data-booking-summary]");
		function panel(n) { return form.querySelector("[data-step-panel='" + n + "']"); }
		function selected(name) {
			var input = form.querySelector("[name='" + name + "']:checked");
			return input ? input.value : "";
		}
		function field(name) {
			var input = form.querySelector("[name='" + name + "']");
			return input ? input.value : "";
		}
		function updateSummary() {
			if (!summary) return;
			summary.innerHTML = "<strong>Resumo:</strong><br>" +
				"Tratamento: " + (selected("treatment") || "-") + "<br>" +
				"Dentista: " + (selected("doctor") || "-") + "<br>" +
				"Data: " + (field("date") || "-") + " às " + (field("time") || "-") + "<br>" +
				"Paciente: " + (field("name") || "-");
		}
		function validCurrent() {
			var inputs = panel(current).querySelectorAll("input, select, textarea");
			for (var i = 0; i < inputs.length; i++) {
				if (!inputs[i].checkValidity()) {
					inputs[i].reportValidity();
					return false;
				}
			}
			return true;
		}
		function render() {
			for (var i = 1; i <= total; i++) {
				panel(i).classList.toggle("is-active", i === current);
			}
			prev.disabled = current === 1;
			next.hidden = current === total;
			updateSummary();
		}
		next.addEventListener("click", function () {
			if (!validCurrent()) return;
			current = Math.min(total, current + 1);
			render();
		});
		prev.addEventListener("click", function () {
			current = Math.max(1, current - 1);
			render();
		});
		form.addEventListener("input", updateSummary);
		form.addEventListener("change", updateSummary);
		form.addEventListener("submit", function (event) {
			event.preventDefault();
			if (!form.checkValidity()) {
				if (status) status.textContent = window.dcData ? dcData.invalid : "Preencha os campos obrigatórios.";
				form.reportValidity();
				return;
			}
			if (status) status.textContent = window.dcData ? dcData.success : "Marcação demonstrativa criada.";
			showToast(window.dcData ? dcData.success : "Marcação demonstrativa criada.");
			form.reset();
			current = 1;
			render();
		});
		render();
	});

	document.querySelectorAll("[data-contact-form]").forEach(function (form) {
		form.addEventListener("submit", function (event) {
			event.preventDefault();
			var status = form.querySelector("[data-form-status]");
			if (!form.checkValidity()) {
				form.reportValidity();
				return;
			}
			form.reset();
			if (status) status.textContent = "Pedido demonstrativo recebido.";
			showToast("Pedido demonstrativo recebido.");
		});
	});

	var observer = "IntersectionObserver" in window ? new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (entry.isIntersecting) {
				entry.target.classList.add("is-visible");
				observer.unobserve(entry.target);
			}
		});
	}, { threshold: 0.1 }) : null;
	document.querySelectorAll(".reveal").forEach(function (el) {
		if (observer) observer.observe(el);
		else el.classList.add("is-visible");
	});
})();
