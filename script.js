function updateCountdown() {
  const targetDate = new Date("2026-02-16T00:00:00");
  const now = new Date();
  const diff = targetDate - now;

  if (diff <= 0) {
    document.getElementById("days").textContent = "00";
    document.getElementById("hours").textContent = "00";
    document.getElementById("minutes").textContent = "00";
    document.getElementById("seconds").textContent = "00";
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
  const minutes = Math.floor((diff / (1000 * 60)) % 60);
  const seconds = Math.floor((diff / 1000) % 60);

  document.getElementById("days").textContent = String(days).padStart(2, '0');
  document.getElementById("hours").textContent = String(hours).padStart(2, '0');
  document.getElementById("minutes").textContent = String(minutes).padStart(2, '0');
  document.getElementById("seconds").textContent = String(seconds).padStart(2, '0');
}

updateCountdown();
setInterval(updateCountdown, 1000); // agora atualiza a cada segundo

const events = document.querySelectorAll('.timeline-event');

function showEvents() {
  const triggerBottom = window.innerHeight * 0.8;

  events.forEach(event => {
    const eventTop = event.getBoundingClientRect().top;
    if(eventTop < triggerBottom) {
      event.classList.add('active');
    } else {
      event.classList.remove('active');
    }
  });
}

window.addEventListener('scroll', showEvents);
window.addEventListener('load', showEvents);

const sections = document.querySelectorAll('.section-ilustracao, .local, .cronograma, .lista-presentes');

const sectionObserver = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('show'); // adiciona classe para animação
      observer.unobserve(entry.target); // opcional: só anima uma vez
    }
  });
}, {
  threshold: 0.2 // 20% da seção precisa estar visível para animar
});

// Observa cada seção
sections.forEach(section => sectionObserver.observe(section));

const animElements = document.querySelectorAll(
  ".section-ilustracao, .local, .cronograma, .lista-presentes, .timeline-event .content-actual"
);

const observer = new IntersectionObserver((entries, obs) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const delay = entry.target.classList.contains("content-actual") ? 1000 : 0;
      setTimeout(() => {
        entry.target.classList.add(entry.target.classList.contains("content-actual") ? "atualiza" : "show");
      }, delay);

      obs.unobserve(entry.target); // anima só uma vez
    }
  });
}, {
  threshold: 0.2
});

animElements.forEach(el => observer.observe(el));
