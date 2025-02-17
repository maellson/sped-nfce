<?php

putenv('TMPDIR=' . __DIR__ . '/tmp');

// Exibe erros diretamente na tela durante o desenvolvimento
ini_set ('display_errors', 1);
// Exibe erros durante a inicialização do PHP ini_set ('display_startup_errors', 1);
// Define o nivel de relatório de erros para exibir todos os erros e avisos
error_reporting (E_ALL);
// Carrega automaticamente as dependências instaladas via Composer 
require 'vendor/autoload.php';
// Importa classes necessárias do pacote NFePHP 

use  NFePHP\NFe\Tools;
use  NFePHP\Common\Certificate;



try {
    // Cria um JSON com as configurações necessárias para o serviço da NFe
    $configJson = json_encode([
        "atualizacao" => "2015-10-02 06:01:21", // Data de atualização do schema
        "tpAmb" => 2, // Ambiente de homologação (2) ou produção (15
        "razaosocial" => "Manalyticsai Ltda", // Razão social da empresa
        "siglaUF" => "PB", // Sigla do estado
        "cnpj" => "52463011000101", // CNPJ da empresa
        "schemes" => "PL_008i2", // Versão do esquema XML
        "versao" => "4.00", // Versão da NF-e
        "tokenIBPT" => "AAAAAAA", // Token para consultas IBPT
        "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G", // Código de segurança do contribuinte
        "CSCid" => "000002", // Identificador do CSC
        "aProxyConf" =>[ // Configuração de proxy (caso exista)
            "proxyIp" => "",
            "proxyPort" =>"",
            "proxyUser" => "",
            "proxyPass" => "",
       ]
        ]);
    // Carrega o conteúdo do certificado digital no formato PFX
    $certificado = file_get_contents('chave.pfx');
    // Define a senha do certificado digital
    $password = '';//password
    // Lê o certificado PFX usando a classe Certificate
    $certificate = Certificate::readPfx($certificado, $password);
    // Inicializa a classe Tools com as configurações e certificado
    $tools = new Tools($configJson, $certificate);
    // Define o modelo da nota fiscal como 55 (NF-e)
    $tools->model('55');// se precisar o padrao era 55. o nosso o padrao sera 65

    // Consulta o status do serviço na SEFAZ
    $response = $tools->sefazStatus();
    // Cria um objeto DOMDocument para manipular o XML retornado
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false; // Remove espaços em branco desnecessários 
    $dom->formatOutput = true; // Formata a saída do XML 
    $dom-> loadXML($response); // Carrega o XML de resposta da SEFAZ
    // Converte o XML em um objeto SimpleXML para facilitar a manipulação
    $xml = simplexml_Load_string ($response, null, LIBXML_NOCDATA) ;
    // Registra o namespace utilizado no XML
    $xml->registerXPathNamespace('ns', 'http://www.portalfiscal.inf.br/nfe');
    // Captura o código de status da resposta
    $status = (string) $xml->xpath('//ns:cStat') [0];
    // Captura o motivo do status
    $motivo = (string)$xml->xpath('//ns:xMotivo') [0];
    // Captura a data e hora de recebimento
    $dataRecebimento = (string)$xml->xpath('//ns:dhRecbto') [0];
    // Define o cabeçalho da resposta como JSON 
    header ('Content-Type: application/json');
    // Armazena status e motivo em um array associativo
    $dadosRetorno ['status'] = $status;
    $dadosRetorno ['motivo'] = $motivo;
    $dadosRetorno ['dataHora'] = $dataRecebimento;

    // Retorna os dados como JSON

    echo json_encode($dadosRetorno);
} catch (Exception $e) {
// Exibe mensagens de erro em caso de exceção 
    echo "Erro ao consultar o status do serviço:".$e->getMessage();

}