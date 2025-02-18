<?php

// Define o diretório temporário para armazenar arquivos temporários
putenv('TMPDIR=' . __DIR__ . '/tmp');

// Exibe erros diretamente na tela durante o desenvolvimento
ini_set ('display_errors', 1);
// Exibe erros durante a inicialização do PHP ini_set ('display_startup_errors', 1);
// Define o nivel de relatório de erros para exibir todos os erros e avisos
error_reporting (E_ALL);
// Carrega automaticamente as dependências instaladas via Composer 
require 'vendor/autoload.php';
// Importa classes necessárias do pacote NFePHP 

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;

try {

     // Cria um JSON com as configurações necessárias para o serviço da NFe
     $configJson = json_encode([
        "atualizacao" => "2015-10-02 06:01:21", // Data de atualização do schema
        "tpAmb" => 2, // Ambiente de homologação (2) ou produção (1)
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
    $password = '207100';//password

    $certificate = Certificate::readPfx($certificado, $password);
    $tools = new Tools($configJson, $certificate);
    $tools->model('65');

    $chave = '25250224914819000122650020000476951179778078';
    $response = $tools->sefazConsultaChave($chave);

    //você pode padronizar os dados de retorno atraves da classe abaixo
    //de forma a facilitar a extração dos dados do XML
    //NOTA: mas lembre-se que esse XML muitas vezes será necessário, 
    //      quando houver a necessidade de protocolos
    $stdCl = new Standardize($response);
    //nesse caso $std irá conter uma representação em stdClass do XML
    $std = $stdCl->toStd();
    //nesse caso o $arr irá conter uma representação em array do XML
    $arr = $stdCl->toArray();

    //nesse caso o $json irá conter uma representação em JSON do XML
    $json = $stdCl->toJson();


   echo json_encode($json);

} catch (Exception $e) {
// Exibe mensagens de erro em caso de exceção 
    echo "Erro ao consultar o status do serviço:".$e->getMessage();

}

?>