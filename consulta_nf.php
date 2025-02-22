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

    $config = [
        "atualizacao" => (new \DateTime())->format('Y-m-d\TH:i:sP'),
        "tpAmb"       => 2, //Homologação
        "razaosocial" => "ALUSKA VANESSA BARBOSA DE OLIVEIRA",
        "cnpj"        => "37715148000112",
        "ie"          => '163700044',
        "siglaUF"     => "PB",
        "schemes"     => "PL_009_V4",
        "versao"      => '4.00',
        "tokenIBPT"   => "AAAAAAA",
        "CSC"         => "4AC2A781-AC41-BF54-5E1A-7BBFA3BF7E37",
        "CSCid"       => "000002",
        "proxyConf"   => [
            "proxyIp"   => "",
            "proxyPort" => "",
            "proxyUser" => "",
            "proxyPass" => ""
        ]
    ];
    $configJson = json_encode($config);
    $pfxcontent = file_get_contents('certificado.pfx');
    $password = '';
    
    $tools = new Tools($configJson, Certificate::readPfx($pfxcontent, $password));
    //$tools->disableCertValidation(true); //tem que desabilitar
    $tools->model('65'); //65 NFCe

    $chave = '25250237715148000112650010000020251000010019';//'25250224914819000122650020000476951179778078';
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