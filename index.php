<?php
require 'models/Conexao.php';


$sql = "
    SELECT e.*, t.fantasia
    FROM entregas e
    LEFT JOIN transportadoras t ON t.id = e.id_transportadora
    ORDER BY e.id DESC
";

$rows = Conexao::select($sql);

//se não tiver salvo os dados, salva pelo JSON
if(empty($rows)){
    require 'importar_json.php';
    $rows = Conexao::select($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listagem de Entregas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light p-4">

<div class="container">

    <h2 class="mb-3">📦 Listagem de Entregas</h2>
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="cpf" class="form-control" placeholder="Digite o CPF do destinatário">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" id="btnBuscarCPF">Buscar</button>
        </div>
    </div>

    <div class="mb-3">
        <input type="text" id="busca" class="form-control" placeholder="Digite para buscar por destinatário, transportadora, cidade...">
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Transportadora</th>
                <th>Remetente</th>
                <th>Destinatário</th>
                <th>CPF</th>
                <th>Endereço</th>
                <th>CEP</th>
                <th>Estado</th>
                <th>País</th>
                <th>Lat</th>
                <th>Lng</th>
                <th>Volumes</th>
            </tr>
        </thead>


        <tbody id="lista-entregas">
            <?php foreach ($rows as $e) : ?>
                <tr class="linha-entrega" data-id="<?= $e['id'] ?>">

                    <td><?= $e['id'] ?></td>
                    <td><?= $e['fantasia'] ?></td>
                    <td><?= $e['remetente_nome'] ?></td>
                    <td><?= $e['destinatario_nome'] ?></td>
                    <td><?= $e['destinatario_cpf'] ?></td>
                    <td><?= $e['destinatario_endereco'] ?></td>
                    <td><?= $e['destinatario_cep'] ?></td>
                    <td><?= $e['destinatario_estado'] ?></td>
                    <td><?= $e['destinatario_pais'] ?></td>
                    <td><?= $e['lat'] ?></td>
                    <td><?= $e['lng'] ?></td>
                    <td><?= $e['volumes'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>


    </table>

</div>

<div class="modal fade" id="modalDetalhes" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detalhes da Entrega</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="conteudoModal">
         Carregando...
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Fechar
        </button>
      </div>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

<script>

    $(document).on("click", ".linha-entrega", function() {

        let id = $(this).data("id");

        $.ajax({
            url: "controllers/EntregaController.php?action=detalhes",
            type: "POST",
            data: { id: id },
            success: function(res){
                $("#conteudoModal").html(res);
                $("#modalDetalhes").modal("show");
            }
        });

    });

    $(document).on("keyup", "#busca", function() {

        let termo = $(this).val().trim();

        $.ajax({
            url: "controllers/EntregaController.php?action=buscar",
            type: "POST",
            data: { busca: termo },
            success: function(res){
                $("#lista-entregas").html(res);
            }
        });

    });
    // Buscar por CPF
    $(document).on("click", "#btnBuscarCPF", function () {

        let cpf = $("#cpf").val().trim();

        $.ajax({
            url: "controllers/EntregaController.php?action=buscarPorCPF",
            type: "POST",
            data: { cpf: cpf },
            success: function(res){
                $("#lista-entregas").html(res);
            }
        });

    });

</script>


</body>
</html>
