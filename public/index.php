<?php

require_once "setup.php";

$test_curl = curl_init();
$url = getenv("API_ROOT") . "get-routes/";
curl_setopt($test_curl, CURLOPT_URL, $url);
// curl_setopt($test_curl, CURLOPT_HTTPGET, 1);
curl_setopt($test_curl, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($test_curl);
curl_close($test_curl);
var_dump($result);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>G.E. Cristo-Rei</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header class="barra-navegacao">
        <img src="img/logo.png" alt="Logotipo G.E. Cristo-Rei">
        <input type="checkbox" id="menu-toggle" class="menu-toggle">
        <label for="menu-toggle" class="hamburger" aria-label="Abrir menu">
            <span></span>
            <span></span>
            <span></span>
        </label>
        <nav class="links-navegacao" aria-label="Links principais">
            <a href="#" class="link-navegacao">Sobre</a>
            <a href="#" class="link-navegacao">Projetos</a>
            <a href="#" class="link-navegacao">Contato</a>
        </nav>
        <div class="botao-login">
            <a href="#">Login</a>
        </div>
    </header>

    <main>
        <section class="secao-principal">
            <div class="conteudo-principal">
                <h1>Uma missão de honra, serviço e cidadania — Isso é escotismo</h1>
                <h2>Promovemos educação, cidadania e desenvolvimento pessoal por meio do escotismo, impactando positivamente jovens e suas comunidades.</h2>
                <div class="linha-botoes">
                    <a href="#" class="botao-primario">Quero Participar</a>
                    <a href="#" class="botao-secundario">Saiba Mais</a>
                </div>
            </div>

            <div class="imagem-principal">
                <img src="img/sp100img.png" alt="Atividades escoteiras">
            </div>
        </section>

        <div class="divisoria" aria-hidden="true"></div>

        <!-- seção carrossel -->
        <section class="secao-carrossel" aria-labelledby="oque-title">
            <div class="carrossel" aria-label="Galeria de imagens" tabindex="0">
                <button class="anterior" aria-label="Slide anterior" type="button">&lt;</button>
                <div class="faixa-carrossel">
                    <img src="img/slide1.jpg" alt="Aventura ao ar livre">
                    <img src="img/slide2.jpg" alt="Valores e camaradagem">
                    <img src="img/slide3.jpg" alt="Atividades em comunidade">
                </div>
                <button class="proximo" aria-label="Próximo slide" type="button">&gt;</button>
                <div class="indicadores" aria-hidden="false"></div>
            </div>

            <aside class="painel-texto" id="oque-title">
                <h1>O que é o Escotismo?</h1>
                <p class="resumo">Um movimento educacional que desenvolve jovens através de experiências práticas, convivência e contato com a natureza — preparando cidadãos responsáveis e comprometidos.</p>

                <h2>Pilares que nos guiam</h2>
                <p><strong>Aventura</strong> — vivências ao ar livre que incentivam coragem, autonomia e trabalho em equipe.</p>
                <p><strong>Valores</strong> — honestidade, lealdade e solidariedade como base para atitudes e escolhas.</p>
                <p><strong>Comunidade</strong> — ações que fortalecem o vínculo social, o apoio mútuo e o desenvolvimento local.</p>

                <p>Participe de acampamentos, trilhas e projetos que transformam a vida dos jovens e impactam positivamente as comunidades.</p>
                <a href="#" class="botao-primario">Quero Participar</a>
            </aside>
        </section>
    </main>

    <footer class="rodape" role="contentinfo" aria-label="Rodapé do site">
        <div class="rodape-interno">
            <div class="marca-rodape">
                <img src="img/logo.png" alt="Logotipo G.E. Cristo-Rei" class="logo-rodape">
                <div class="texto-marca">
                    <h3>G.E. Cristo‑Rei</h3>
                    <p>Escotismo que forma cidadãos, promove serviço e constrói comunidade.</p>
                </div>
            </div>

            <div class="meio-rodape" aria-label="Links e informação">
                <nav class="links-rodape" aria-label="Links úteis">
                    <a href="#">Sobre</a>
                    <a href="#">Projetos</a>
                    <a href="#">Programação</a>
                    <a href="#">Voluntariado</a>
                </nav>
                <p class="boletim">Participe dos nossos projetos e atividades. <a href="#" class="link-acao">Saiba como</a></p>
            </div>

            <div class="contato-rodape" aria-label="Contacto">
                <p class="linha-contato">Contato</p>
                <p><a href="mailto:contato@cristo-rei.org">contato@cristo-rei.org</a></p>
                <p class="copyright">© <span id="year"></span> G.E. Cristo‑Rei</p>
            </div>
        </div>
    </footer>

    <!-- script do carrossel e menu lateral -->
    <script>
    (function () {
      const carrossel = document.querySelector('.carrossel');
      if (!carrossel) return;

      const faixa = carrossel.querySelector('.faixa-carrossel');
      const slides = Array.from(faixa.querySelectorAll('img'));
      const botaoPrev = carrossel.querySelector('.anterior');
      const botaoNext = carrossel.querySelector('.proximo');
      const indicadoresContainer = carrossel.querySelector('.indicadores');
      let index = 0;

      // Garante que cada slide ocupe 100% do carrossel
      slides.forEach(img => { img.style.flex = '0 0 100%'; });

      // cria indicadores
      indicadoresContainer.innerHTML = '';
      slides.forEach((_, i) => {
        const b = document.createElement('button');
        b.className = 'indicador';
        b.type = 'button';
        b.setAttribute('aria-label', 'Slide ' + (i + 1));
        b.addEventListener('click', () => { index = i; update(); resetTimer(); });
        indicadoresContainer.appendChild(b);
      });
      const indicadores = Array.from(indicadoresContainer.children);

      function update() {
        faixa.style.transform = 'translateX(' + (-index * 100) + '%)';
        indicadores.forEach((btn, i) => btn.classList.toggle('active', i === index));
      }

      botaoPrev && botaoPrev.addEventListener('click', () => { index = (index - 1 + slides.length) % slides.length; update(); resetTimer(); });
      botaoNext && botaoNext.addEventListener('click', () => { index = (index + 1) % slides.length; update(); resetTimer(); });

      // suporte por teclado (quando o carrossel estiver em foco)
      carrossel.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft') botaoPrev && botaoPrev.click();
        if (e.key === 'ArrowRight') botaoNext && botaoNext.click();
      });

      // autoplay com pausa ao passar o mouse / foco
      let timer = setInterval(() => { index = (index + 1) % slides.length; update(); }, 4000);
      function resetTimer() { clearInterval(timer); timer = setInterval(() => { index = (index + 1) % slides.length; update(); }, 4000); }

      carrossel.addEventListener('mouseenter', () => clearInterval(timer));
      carrossel.addEventListener('mouseleave', () => resetTimer());
      carrossel.addEventListener('focusin', () => clearInterval(timer));
      carrossel.addEventListener('focusout', () => resetTimer());

      // inicializa
      update();
    })();

    // Menu lateral mobile
    (function () {
      const menuToggle = document.getElementById('menu-toggle');
      const menuLateral = document.createElement('div');
      menuLateral.className = 'menu-lateral';
      menuLateral.innerHTML = `
        <button class="close-btn" aria-label="Fechar menu">&times;</button>
        <a href="#" class="link-navegacao">Sobre</a>
        <a href="#" class="link-navegacao">Projetos</a>
        <a href="#" class="link-navegacao">Contato</a>
        <div class="botao-login">
          <a href="#">Login</a>
        </div>
      `;
      document.body.appendChild(menuLateral);

      const hamburger = document.querySelector('.hamburger');
      const closeBtn = menuLateral.querySelector('.close-btn');

      hamburger.addEventListener('click', () => menuLateral.classList.add('open'));
      closeBtn.addEventListener('click', () => menuLateral.classList.remove('open'));
      menuLateral.addEventListener('click', (e) => {
        if (e.target === menuLateral) menuLateral.classList.remove('open');
      });
    })();

    // atualiza ano no footer
    document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>