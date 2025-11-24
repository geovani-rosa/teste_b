CREATE TABLE `entregas_rastreamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entrega_id` varchar(50) DEFAULT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `data_evento` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entrega_id` (`entrega_id`),
  CONSTRAINT `entregas_rastreamento_ibfk_1` FOREIGN KEY (`entrega_id`) REFERENCES `entregas` (`id`)
)


CREATE TABLE `entregas` (
  `id` varchar(50) NOT NULL,
  `id_transportadora` varchar(50) DEFAULT NULL,
  `volumes` int(11) DEFAULT NULL,
  `remetente_nome` varchar(255) DEFAULT NULL,
  `destinatario_nome` varchar(255) DEFAULT NULL,
  `destinatario_cpf` varchar(20) DEFAULT NULL,
  `destinatario_endereco` varchar(255) DEFAULT NULL,
  `destinatario_estado` varchar(100) DEFAULT NULL,
  `destinatario_cep` varchar(20) DEFAULT NULL,
  `destinatario_pais` varchar(50) DEFAULT NULL,
  `lat` decimal(10,6) DEFAULT NULL,
  `lng` decimal(10,6) DEFAULT NULL,
  `destinatario_bairro` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_transportadora` (`id_transportadora`),
  CONSTRAINT `entregas_ibfk_1` FOREIGN KEY (`id_transportadora`) REFERENCES `transportadoras` (`id`)
)

transportadoras, CREATE TABLE `transportadoras` (
  `id` varchar(50) NOT NULL,
  `cnpj` bigint(20) NOT NULL,
  `fantasia` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
)

