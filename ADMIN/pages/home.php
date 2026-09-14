<div class="imagem-backgraund">
    <div
        id="carouselExampleCaptions"
        class="carousel slide"
        data-bs-ride="carousel"
        data-bs-interval="3000">

        <div class="carousel-indicators">
            <button
                type="button"
                data-bs-target="#carouselExampleCaptions"
                data-bs-slide-to="0"
                class="active"
                aria-current="true"
                aria-label="Slide 1">
            </button>

            <button
                type="button"
                data-bs-target="#carouselExampleCaptions"
                data-bs-slide-to="1"
                aria-label="Slide 2">
            </button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img
                    src="<?= $baseUrl ?>/IMG/backgraund.jpg"
                    class="d-block w-100"
                    alt="Covil do Dragão">

                <div class="carousel-caption d-none d-md-block">
                    <h4>ADOTE SEU DRAGÃO AGORA!</h4>

                    <!-- Leva o usuário para a loja. -->
                    <a
                        href="<?= $baseUrl ?>/loja"
                        class="btn btn-primary btn-lg">
                        COMPRE AGORA
                    </a>
                </div>
            </div>

            <div class="carousel-item">
                <img
                    src="<?= $baseUrl ?>/IMG/backgraund.jpg"
                    class="d-block w-100"
                    alt="Covil do Dragão">

                <div class="carousel-caption d-none d-md-block">
                    <h5>DRAGÕES ÚNICOS FEITOS COM DETALHES</h5>

                    <p>
                        Explore nossa coleção de estatuetas, dragões e criaturas
                        inspiradas no universo de fantasia.
                    </p>

                    <!-- Leva o usuário para a loja. -->
                    <a
                        href="<?= $baseUrl ?>/loja"
                        class="btn btn-primary btn-lg">
                        VER PRODUTOS
                    </a>
                </div>
            </div>
        </div>

        <!-- Volta para o slide anterior. -->
        <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#carouselExampleCaptions"
            data-bs-slide="prev">

            <span
                class="carousel-control-prev-icon"
                aria-hidden="true">
            </span>

            <span class="visually-hidden">
                Anterior
            </span>
        </button>

        <!-- Avança para o próximo slide. -->
        <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#carouselExampleCaptions"
            data-bs-slide="next">

            <span
                class="carousel-control-next-icon"
                aria-hidden="true">
            </span>

            <span class="visually-hidden">
                Próximo
            </span>
        </button>
    </div>
</div>

<section class="container py-5">
    <div class="row justify-content-center g-4">
        <div class="col-auto">
            <a
                href="https://www.instagram.com/reel/DX6-CQNixZc/"
                target="_blank">

                <video
                    class="video-card"
                    autoplay
                    muted
                    loop
                    playsinline>

                    <source
                        src="<?= $baseUrl ?>/IMG/video1.mp4"
                        type="video/mp4">
                </video>
            </a>
        </div>

        <div class="col-auto">
            <a
                href="https://www.instagram.com/reel/DX1Bjv6CfHn/"
                target="_blank">

                <video
                    class="video-card"
                    autoplay
                    muted
                    loop
                    playsinline>

                    <source
                        src="<?= $baseUrl ?>/IMG/video2.mp4"
                        type="video/mp4">
                </video>
            </a>
        </div>

        <div class="col-auto">
            <a
                href="https://www.instagram.com/reel/DXv6xRSiwZi/"
                target="_blank">

                <video
                    class="video-card"
                    autoplay
                    muted
                    loop
                    playsinline>

                    <source
                        src="<?= $baseUrl ?>/IMG/video3.mp4"
                        type="video/mp4">
                </video>
            </a>
        </div>

        <div class="col-auto">
            <a
                href="https://www.instagram.com/reel/DXpAwadglAx/"
                target="_blank">

                <video
                    class="video-card"
                    autoplay
                    muted
                    loop
                    playsinline>

                    <source
                        src="<?= $baseUrl ?>/IMG/video4.mp4"
                        type="video/mp4">
                </video>
            </a>
        </div>
    </div>
</section>

<div class="intro-home">
    <div class="conteudo-intro-home">
        <div class="imgi-intro-home">
            <img
                class="imgintro-home"
                src="<?= $baseUrl ?>/IMG/meleys1.webp"
                alt="Estatueta de dragão">
        </div>

        <div class="texto-intro-home">
            <h2>ESTATUETAS FEITAS COM CUIDADO</h2>

            <p>
                Minhas estatuetas de dragão estão disponíveis em quantidades
                limitadas. Para garantir a melhor qualidade e prazos de
                produção razoáveis, as pré-encomendas só são possíveis
                enquanto durarem os estoques.

                Quando um produto estiver disponível, você poderá fazer seu
                pedido normalmente. Assim que todas as vagas de produção
                forem preenchidas, o produto ficará temporariamente
                indisponível.

                Você poderá então se inscrever na lista de notificação de
                reposição de estoque ou criar uma lista de desejos para ser
                notificado assim que novas vagas de pré-encomenda forem
                liberadas.

                Isso permite controlar cada etapa do processo de produção
                e oferecer estatuetas com um nível de detalhe e qualidade
                impossível de alcançar por meio de produção contínua.

                Cada peça é impressa e pintada à mão com o tempo e o cuidado
                que merece.
            </p>
        </div>
    </div>
</div>

<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-dragao">
                <img
                    src="<?= $baseUrl ?>/IMG/craniocarax.webp"
                    alt="Crânio de dragão">

                <div class="card-info">
                    <p>CRÂNIOS</p>
                    <h2>"CINZAS E OSSOS"</h2>

                    <!-- Leva o usuário para a loja. -->
                    <a
                        href="<?= $baseUrl ?>/loja"
                        class="btn btn-comprar">
                        COMPRE AGORA
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-dragao">
                <img
                    src="<?= $baseUrl ?>/IMG/meleysovo.webp"
                    alt="Ovo de dragão">

                <div class="card-info">
                    <p>ESTÁTUAS DE FILHOTES</p>
                    <h2>"NASCIDO DO FOGO"</h2>

                    <!-- Leva o usuário para a loja. -->
                    <a
                        href="<?= $baseUrl ?>/loja"
                        class="btn btn-comprar">
                        COMPRE AGORA
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-dragao">
                <img
                    src="<?= $baseUrl ?>/IMG/drogon.webp"
                    alt="Dragão">

                <div class="card-info">
                    <p>ESTÁTUAS DE DRAGÕES</p>
                    <h2>"SENHORES DA CHAMA"</h2>

                    <!-- Leva o usuário para a loja. -->
                    <a
                        href="<?= $baseUrl ?>/loja"
                        class="btn btn-comprar">
                        COMPRE AGORA
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>