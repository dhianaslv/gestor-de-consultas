<?php
if (!isset($_SESSION["loggedUser"])) {
  header("Location: ./");
}
?>
<html>

<head>
  <title>Unidade de Saúde | Dados do Médico(a)</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="./public/styles/img/doctors-list.svg" type="image/x-icon" />
  <link rel="stylesheet" type="text/css" href="./public/styles/css/main.css" />
  <link rel="stylesheet" type="text/css" href="./public/styles/css/home.css" />
  <link rel="stylesheet" type="text/css" href="./public/styles/css/buttons.css" />
  <link rel="stylesheet" type="text/css" href="./public/styles/css/form.css" />
  <link rel="stylesheet" type="text/css" href="./public/styles/css/card.css" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;800&display=swap" rel="stylesheet" />
</head>
<script src="./public/scripts/toggle.js"></script>

<body>

  <header>
    <h3 class="logo">Unidade de Saúde</h3>
  </header>
  <main class="container">
    <section class="quick-access">

      <a href="?page=doctor/search" class="home-button">
        <h3>
          <p>Procurar Médico(a)</p>
          <img src="./public/styles/img/doctor.svg" alt="Imagem de pesquisa" />
        </h3>
      </a>
      <a href="?page=doctor/register" class="home-button">
        <h3>
          <p>Cadastrar Médico(a)</p>
          <img src="./public/styles/img/add-doctor.svg" alt="Imagem de cadastro de médicos" />
        </h3>
      </a>
      <a href="?page=doctor/list" class="home-button">
        <h3>
          <p>Listar Médicos</p>
          <img src="./public/styles/img/doctors-list.svg" alt="Imagem de lista de médicos" />
        </h3>
      </a>
      <a href="?page=home" class="home-button">
        <h3>
          <p>Home</p>
          <img src="./public/styles/img/home.svg" alt="Imagem de Home" />
        </h3>
      </a>
    </section>
    <?php
    require_once "app/components/MessageContainer.php";

    if (isset($_SESSION["errorMessage"])) {
      MessageContainer::errorMessage("Mensagem de Erro", $_SESSION["errorMessage"]);
      $_SESSION["errorMessage"] = null;
    } else if (isset($_SESSION["successMessage"])) {
      MessageContainer::successMessage("Operação realizada", $_SESSION["successMessage"]);
      $_SESSION["successMessage"] = null;
    } else {
    ?>
      <section class="box">
        <div class="form">
          <h2>Dados do Médico(a)</h2>
          <form method="POST" action="?class=Doctor&action=update">
            <div class="input-block">
              <label for="id">ID</label>
              <input id="id" value="<?php echo ($doctor->getId()) ?>" disabled />
              <input type="hidden" name="id" value="<?php echo ($doctor->getId()) ?>" required />
            </div>
            <div class="input-block">
              <label for="name">Nome</label>
              <input id="name" name="name" value="<?php echo ($doctor->getName()) ?>" required />
            </div>
            <div class="input-block">
              <label for="specialty">Especialidade</label>
              <input id="specialty" name="specialty" value="<?php echo ($doctor->getSpecialty()) ?>" required />
            </div>
            <div class="input-block">
              <label for="genre">Gênero</label>
              <input type="hidden" name="genre" id="genre" value="<?php echo ($doctor->getGenre()) ?>" required />

              <div class="button-select">
                <button data-value="Feminino" onclick="toggleGenre(event)" type="button" <?php
                                                                                          if ($doctor->getGenre() == "Feminino") {
                                                                                          ?> class="active-genre" <?php
                                                                                                              }
                                                                                                                ?>>
                  Feminino
                </button>
                <button data-value="Masculino" onclick="toggleGenre(event)" type="button" <?php
                                                                                          if ($doctor->getGenre() == "Masculino") {
                                                                                          ?> class="active-genre" <?php
                                                                                                              }
                                                                                                                ?>>
                  Masculino
                </button>
              </div>
            </div>
            <div class="input-block">
              <label for="active">Status</label>
              <input type="hidden" name="active" id="active" value="<?php echo ($doctor->getActive()) ?>" required />

              <div class="button-select">
                <button data-value=1 onclick="toggleActive(event)" type="button" <?php
                                                                                  if ($doctor->getActive() == 1) {
                                                                                  ?> class="active" <?php
                                                                                                }
                                                                                                  ?>>
                  Ativo
                </button>
                <button data-value=0 onclick="toggleActive(event)" type="button" <?php
                                                                                  if ($doctor->getActive() == 0) {
                                                                                  ?> class="active" <?php
                                                                                                }
                                                                                                  ?>>
                  Inativo
                </button>
              </div>
            </div>
            <button type="submit" class="primary-button">Salvar Alterações</button>
          </form>
        </div>
      </section>
    <?php
    }
    ?>
  </main>

  <footer>
    <p>2021 - Unidade de Saúde</p>
  </footer>
</body>

</html>