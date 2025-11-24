<?php
require_once '../models/EntregaModel.php';

class EntregaController {

    public function detalhes()
    {
        $id = $_POST['id'];

        $sql = "
            SELECT e.*, t.fantasia
            FROM entregas e
            LEFT JOIN transportadoras t ON t.id = e.id_transportadora
            WHERE e.id = ?
        ";

        $dados = Conexao::select($sql, [$id]);

        if (!$dados) {
            echo "<p>Entrega não encontrada.</p>";
            return;
        }

        $e = $dados[0];

        echo "
            <h4>Informações da Entrega</h4><hr>

            <b>ID:</b> {$e['id']}<br>
            <b>Transportadora:</b> {$e['fantasia']}<br>

            <hr><h5>Remetente</h5>
            <b>Nome:</b> {$e['remetente_nome']}<br>

            <hr><h5>Destinatário</h5>
            <b>Nome:</b> {$e['destinatario_nome']}<br>
            <b>CPF:</b> {$e['destinatario_cpf']}<br>
            <b>Endereço:</b> {$e['destinatario_endereco']}<br>
            <b>CEP:</b> {$e['destinatario_cep']}<br>
            <b>Estado:</b> {$e['destinatario_estado']}<br>
            <b>País:</b> {$e['destinatario_pais']}<br>

            <hr><h5>Localização</h5>
            <b>Latitude:</b> {$e['lat']}<br>
            <b>Longitude:</b> {$e['lng']}<br>

            <hr><h5>Volumes</h5>
            <b>Quantidade:</b> {$e['volumes']}<br>
        ";
    }

    public function buscar() {

        $busca = isset($_POST['busca']) ? trim($_POST['busca']) : "";

        $dados = EntregaModel::buscar($busca);

        if (!$dados) {
            echo "<tr><td colspan='5' class='text-center'>Nenhum resultado encontrado...</td></tr>";
            return;
        }

        foreach ($dados as $e) {
            echo "
                <tr class='linha-entrega' data-id='{$e['id']}'>
                    <td>{$e['id']}</td>
                    <td>{$e['fantasia']}</td>
                    <td>{$e['remetente_nome']}</td>
                    <td>{$e['destinatario_nome']}</td>
                    <td>{$e['destinatario_cpf']}</td>
                    <td>{$e['destinatario_endereco']}</td>
                    <td>{$e['destinatario_cep']}</td>
                    <td>{$e['destinatario_estado']}</td>
                    <td>{$e['destinatario_pais']}</td>
                    <td>{$e['lat']}</td>
                    <td>{$e['lng']}</td>
                    <td>{$e['volumes']}</td>
                </tr>
            ";
        }
    }

    public function buscarPorCPF()
    {
        $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : "";

        $dados = EntregaModel::buscarPorCPF($cpf);

        if (!$dados) {
            echo "<tr><td colspan='5' class='text-center'>Nenhum resultado encontrado no banco ou JSON.</td></tr>";
            return;
        }

        foreach ($dados as $e) {

            // fantasia = ID
            $fantasia = $this->getTransportadoraNome($e['fantasia']);

            echo "
                <tr class='linha-entrega' data-id='{$e['id']}'>
                    <td>{$e['id']}</td>
                    <td>{$e['fantasia']}</td>
                    <td>{$e['remetente_nome']}</td>
                    <td>{$e['destinatario_nome']}</td>
                    <td>{$e['destinatario_cpf']}</td>
                    <td>{$e['destinatario_endereco']}</td>
                    <td>{$e['destinatario_cep']}</td>
                    <td>{$e['destinatario_estado']}</td>
                    <td>{$e['destinatario_pais']}</td>
                    <td>{$e['lat']}</td>
                    <td>{$e['lng']}</td>
                    <td>{$e['volumes']}</td>
                </tr>
                ";

        }
    }


    private function getTransportadoraNome($id)
    {
        $jsonPath = "../API_LISTAGEM_TRANSPORTADORAS.json";

        if (!file_exists($jsonPath)) return $id;

        $json = json_decode(file_get_contents($jsonPath), true);

        foreach ($json['data'] as $t) {
            if ($t['_id'] == $id) {
                return $t['_fantasia'];
            }
        }

        return $id;
    }

    
}


if (isset($_GET['action'])) {

    $c = new EntregaController();

    if ($_GET['action'] === 'buscar') {
        $c->buscar();
    }

    if ($_GET['action'] === 'buscarPorCPF') {
        $c->buscarPorCPF();
    }

    if ($_GET['action'] === 'detalhes') {
        $c->detalhes();
    }
}
