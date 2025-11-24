<?php
require_once '../models/EntregaModel.php';

class EntregaController {

    public function buscar() {

        $busca = isset($_POST['busca']) ? trim($_POST['busca']) : "";

        $dados = EntregaModel::buscar($busca);

        if (!$dados) {
            echo "<tr><td colspan='5' class='text-center'>Nenhum resultado encontrado...</td></tr>";
            return;
        }

        foreach ($dados as $e) {
            echo "
                <tr>
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
                <tr>
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

    $controller = new EntregaController();

    if ($_GET['action'] === 'buscarPorCPF') {
        $controller->buscarPorCPF();
    }

    if ($_GET['action'] === 'buscar') { 
        $controller->buscar();
    }
}
