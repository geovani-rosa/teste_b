<?php
require_once('models/Conexao.php');

// Carrega os JSON
$transportadoras = json_decode(file_get_contents("API_LISTAGEM_TRANSPORTADORAS.json"), true)['data'];
$entregas        = json_decode(file_get_contents("API_LISTAGEM_ENTREGAS.json"), true)['data'];

foreach ($transportadoras as $t) {

    Conexao::execute("
        INSERT INTO transportadoras (id, cnpj, fantasia)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
        cnpj = VALUES(cnpj),
        fantasia = VALUES(fantasia)
    ", [
        $t['_id'],
        $t['_cnpj'],
        $t['_fantasia']
    ]);
}

//salva os dados de entrega
foreach ($entregas as $e) {

    Conexao::execute("
        INSERT INTO entregas (
            id, id_transportadora, volumes, remetente_nome,
            destinatario_nome, destinatario_cpf, destinatario_endereco,
            destinatario_estado, destinatario_cep, destinatario_pais,
            lat, lng
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        volumes = VALUES(volumes)
    ", [
        $e['_id'],
        $e['_id_transportadora'],
        $e['_volumes'],
        $e['_remetente']['_nome'],
        $e['_destinatario']['_nome'],
        $e['_destinatario']['_cpf'],
        $e['_destinatario']['_endereco'],
        $e['_destinatario']['_estado'],
        $e['_destinatario']['_cep'],
        $e['_destinatario']['_pais'],
        $e['_destinatario']['_geolocalizao']['_lat'],
        $e['_destinatario']['_geolocalizao']['_lng']
    ]);

    // Importar rastreamento
    foreach ($e['_rastreamento'] as $r) {
        Conexao::execute("
            INSERT INTO entregas_rastreamento (entrega_id, mensagem, data_evento)
            VALUES (?, ?, ?)
        ", [
            $e['_id'],
            $r['message'],
            $r['date']
        ]);
    }
}

//echo "Importação concluída com sucesso!";
