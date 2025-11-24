<?php
require_once 'Conexao.php';

class EntregaModel {

 
    public static function buscar($busca = "") {

        $sql = "
            SELECT e.*, t.fantasia
            FROM entregas e
            LEFT JOIN transportadoras t ON t.id = e.id_transportadora
        ";

        $params = [];

        if ($busca !== "") {
            $sql .= "
                WHERE
                    e.destinatario_nome LIKE ?
                    OR e.destinatario_endereco LIKE ?
                    OR e.destinatario_estado LIKE ?
                    OR t.fantasia LIKE ?
            ";

            $like = "%$busca%";
            $params = [$like, $like, $like, $like];
        }

        $sql .= " ORDER BY e.id DESC";

        return Conexao::select($sql, $params);
    }

    public static function buscarPorCPF($cpf)
    {

        $sql = "
            SELECT e.*, t.fantasia
            FROM entregas e
            LEFT JOIN transportadoras t ON t.id = e.id_transportadora
            WHERE e.destinatario_cpf = ?
            ORDER BY e.id DESC
        ";

        $resultado = Conexao::select($sql, [$cpf]);

        if (!empty($resultado)) {
            return $resultado; // Achou no banco
        }

  
        $jsonPath = "../API_LISTAGEM_ENTREGAS.json"; 

        if (!file_exists($jsonPath)) {
            return [];
        }

        $json = json_decode(file_get_contents($jsonPath), true);

        if (!isset($json['data'])) {
            return [];
        }

        // Percorrer o JSON
        foreach ($json['data'] as $item) {

            if ($item['_destinatario']['_cpf'] == $cpf) {

        
                self::inserirTransportadoraSeNaoExistir($item['_id_transportadora']);

           
                self::inserirEntregaDoJSON($item);

          
                return [[
                        'id' => $item['_id'],
                        'fantasia' => self::buscarNomeTransportadora($item['_id_transportadora']),
                        'volumes' => $item['_volumes'],
                    
                        // Remetente
                        'remetente_nome' => $item['_remetente']['_nome'],
                    
                        // Destinatário
                        'destinatario_nome' => $item['_destinatario']['_nome'],
                        'destinatario_cpf' => $item['_destinatario']['_cpf'],
                        'destinatario_endereco' => $item['_destinatario']['_endereco'],
                        'destinatario_estado' => $item['_destinatario']['_estado'],
                        'destinatario_cep' => $item['_destinatario']['_cep'],
                        'destinatario_pais' => $item['_destinatario']['_pais'],
                        'lat' => $item['_destinatario']['_geolocalizao']['_lat'],
                        'lng' => $item['_destinatario']['_geolocalizao']['_lng']
                    ]];
                
            }
        }

        return [];
    }
    private static function inserirEntregaDoJSON($item)
    {
        Conexao::execute("
            INSERT INTO entregas (
                id, 
                id_transportadora, 
                volumes, 
                remetente_nome,
                destinatario_nome, 
                destinatario_cpf, 
                destinatario_endereco,
                destinatario_estado, 
                destinatario_cep, 
                destinatario_pais,
                lat, 
                lng
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $item['_id'],
            $item['_id_transportadora'],
            $item['_volumes'],
            $item['_remetente']['_nome'],
            $item['_destinatario']['_nome'],
            $item['_destinatario']['_cpf'],
            $item['_destinatario']['_endereco'],
            $item['_destinatario']['_estado'],
            $item['_destinatario']['_cep'],
            $item['_destinatario']['_pais'],
            $item['_destinatario']['_geolocalizao']['_lat'],
            $item['_destinatario']['_geolocalizao']['_lng']
        ]);
    }

    private static function buscarNomeTransportadora($idTransportadora)
    {
        $jsonPath = "../API_LISTAGEM_TRANSPORTADORAS.json";

        if (!file_exists($jsonPath)) return $idTransportadora;

        $json = json_decode(file_get_contents($jsonPath), true);

        foreach ($json['data'] as $t) {
            if ($t['_id'] == $idTransportadora) {
                return $t['_fantasia'];
            }
        }

        return $idTransportadora;
    }

    private static function inserirTransportadoraSeNaoExistir($idTransportadora)
    {
        $jsonPath = "../API_LISTAGEM_TRANSPORTADORAS.json";

        if (!file_exists($jsonPath)) return;

        $json = json_decode(file_get_contents($jsonPath), true);

        foreach ($json['data'] as $t) {
            if ($t['_id'] == $idTransportadora) {

                // Verificar se já existe no banco
                $existe = Conexao::select("SELECT id FROM transportadoras WHERE id = ?", [$t['_id']]);

                if (!$existe) {
                    Conexao::execute("
                        INSERT INTO transportadoras (id, cnpj, fantasia)
                        VALUES (?, ?, ?)
                    ", [
                        $t['_id'],
                        $t['_cnpj'],
                        $t['_fantasia']
                    ]);
                }
                return;
            }
        }
    }

    
}
