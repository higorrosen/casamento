// itens do cronograma (já usam .active no CSS)
const timelineItems = document.querySelectorAll('.timeline-event');

// sections que devem animar igual
const animatedSections = document.querySelectorAll(
  '.section-ilustracao, .local, .cronograma, .lista-presentes'
);

// animação de entrada (mesma lógica do cronograma)
function animateOnScroll() {
  // só anima depois da intro revelar o conteúdo
  if (!document.body.classList.contains('reveal')) return;

  const triggerBottom = window.innerHeight * 0.8;

  [...timelineItems, ...animatedSections].forEach(el => {
    const top = el.getBoundingClientRect().top;
    if (top < triggerBottom) {
      el.classList.add('active');
    } else {
      el.classList.remove('active');
    }
  });
}

window.addEventListener('scroll', animateOnScroll);
window.addEventListener('load', animateOnScroll);

// após a animação da intro, revela e já checa o que está na tela
document.addEventListener("DOMContentLoaded", () => {
  const intro = document.getElementById("intro");
  const button = document.getElementById("enter-button");

  button.addEventListener("click", () => {
    intro.classList.add("animate");
    setTimeout(() => {
      intro.classList.add("hidden");
      document.body.classList.add("reveal");
      animateOnScroll(); // ✅ garante que a primeira section já entre animada
    }, 1200);
  });
});


document.addEventListener("DOMContentLoaded", () => {
  const intro = document.getElementById("intro");
  const button = document.getElementById("enter-button");

  button.addEventListener("click", () => {
    // dispara a animação das metades
    intro.classList.add("animate");

    // após a animação, esconde o intro e revela o conteúdo
    setTimeout(() => {
      intro.classList.add("hidden");
      document.body.classList.add("reveal");
    }, 1200); // mesmo tempo do CSS transition
  });
});




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
  threshold: 1 // 20% da seção precisa estar visível para animar
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