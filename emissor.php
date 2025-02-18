
<?php

// Define o diretório temporário para armazenar arquivos temporários
putenv('TMPDIR=' . __DIR__ . '/tmp');

// Define o nivel de relatório de erros para exibir todos os erros e avisos
error_reporting (E_ALL);
// Exibe erros diretamente na tela durante o desenvolvimento
ini_set ('display_errors', 1);
// Exibe erros durante a inicialização do PHP ini_set ('display_startup_errors', 1);

// Carrega automaticamente as dependências instaladas via Composer 
require_once 'vendor/autoload.php';

// Importa classes necessárias do pacote NFePHP 

use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapFake;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;


$arr = [
    "atualizacao" => "2017-02-20 09:11:21",
    "tpAmb"       => 2, //Homologação
    "razaosocial" => "MANALYTICS INTELIGENCIA ARTIFICIAL LTDA",
    "cnpj"        => "52463011000101",
    "siglaUF"     => "PB",
    "schemes"     => "PL_009_V4",
    "versao"      => '4.00',
    "tokenIBPT"   => "AAAAAAA",
    "CSC"         => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
    "CSCid"       => "000003",
    "proxyConf"   => [
        "proxyIp"   => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];
$configJson = json_encode($arr);
$pfxcontent = file_get_contents('chave.pfx');
$password = '207100';

$tools = new Tools($configJson, Certificate::readPfx($pfxcontent, $password));
//$tools->disableCertValidation(true); //tem que desabilitar
$tools->model('65');

try {
        $nfce = new Make();

        /*
        //NOTA: se o parametro $std->Id não for passado a chave será criada e inclusa e poderá ser
        //recuperada no parâmetro chNFe da classe, De outra forma se a chave for passada no 
        //parâmetro $std->Id e estiver incorreta, um erro será inserido na proriedade errors.

        $std = new stdClass();
        $std->versao = '4.00'; //versão do layout (string)
        $std->Id = 'NFe35150271780456000160550010000000021800700082'; //se o Id de 44 digitos não for passado será gerado automaticamente
        $std->pk_nItem = null; //deixe essa variavel sempre como NULL

        $nfe->taginfNFe($std);

        */

        $ide = new stdClass();
        $ide->cUF = 25; // codigo da UF para o estado da Paraíba
        $ide->cNF = '00000001'; // Código numérico que compõe a Chave de Acesso
        $ide->natOp = 'VENDA '; // Descrição da Natureza da Operação

        //$ide->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00

        $ide->mod = 65; //aqui // Modelo da Nota Fiscal Eletrônica 65 para nfce  e 55 para nfe
        $ide->serie = 1; // Série da Nota Fiscal
        $ide->nNF = 2025; // Número da Nota Fiscal, NAO PODE REPETIR
        $ide->dhEmi = date('Y-m-dTH:i:dp');  //'2015-02-19T13:48:00-02:00'; 
        $ide->dhSaiEnt = null; //aqui // data e hora de saida. nao informar este campo para NFC-e
        $ide->tpNF = 1; // Tipo de Operação 0-entrada 1-saída
        $ide->idDest = 1; // Identificador de local de destino da operação 1-Operação interna 2-Interestadual 3-Exterior
        $ide->cMunFG = 2507507; // Código do Município de Ocorrência do Fato Gerador - JP
        $ide->tpImp = 4;  //aqui // Formato de Impressão: 1=DANFE normal, Retrato; 2=DANFE normal, Paisagem; 3=DANFE Simplificado; 4=DANFE NFC-e; 
                        //5=DANFE NFC-e em mensagem eletrônica (o envio demensagem eletrônica pode ser feita de forma 
                        //simultânea com a impressão do DANFE; usar o tpImp=5 quando esta for a única forma de disponibilização do DANFE).
        $ide->tpEmis = 1; // Forma de Emissão da NF-e. Para NFC-e testarei apenas 1-Normal 9-Contingência off-line da NFC-e;
                        //Observação: Para a NFC-e somente é válida a opção de
                        //contingência: 9-Contingência Off-Line e, a critério da UF,
                        //opção 4-Contingência EPEC. (NT 2015/002)
        $ide->cDV = 2; // Dígito Verificador da Chave de Acesso da NF-e
        $ide->tpAmb = 2; // Identificação do Ambiente: 1-Produção 2-Homologação
        $ide->finNFe = 1; // Finalidade de emissão da NF-e: 1-NF-e normal 2-NF-e complementar 3-NF-e de ajuste
        $ide->indFinal = 1; //aqui // Indica operação com consumidor final 0-Não 1-Consumidor final
        $ide->indPres = 4; // Indicador de presença do comprador no estabelecimento comercial no momento da operação 0-Não se aplica 1-Operação presencial 2-Não presencial, internet 3-Não presencial, teleatendimento 4-NFC-e em operação com entrega em domicílio 9-Não presencial, outros
        $ide->indIntermed = 0; // Indicador de intermediação (a ser informado quando se tratar de operações com mercadorias comercial
        $ide->procEmi = 0; // Processo de emissão da NF-e 0-Emissão de NF-e com aplicativo do contribuinte 1-Emissão de NF-e avulsa pelo Fisco 2-Emissão de NF-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco 3-Emissão NF-e pelo contribuinte com aplicativo fornecido pelo Fisco
        $ide->verProc = '1.0.1'; // Versão do Processo de emissão da NF-e
        $ide->dhCont = null;
        $ide->xJust = null;

        $nfce->tagide($ide);


        //------------------ TAG EMITENTE -----------------------------------
        $emit = new \stdClass(); //tag emitente
        $emit->CNPJ = '52463011000101'; //CNPJ do emitente
        $emit->xNome = 'MANALYTICS INTELIGENCIA ARTIFICIAL LTDA'; //Razão Social ou Nome do emitente
        $emit->xFant = 'MANALYTICS'; //Nome fantasia
        $emit->IE = '164780319'; //Inscrição Estadual
        //$emit->IEST = null; //Inscrição Estadual do Substituto Tributário
        //$emit->IM = '11889016';
        $emit->CNAE = '4642701';    //CNAE -- contadora
        $emit->CRT = 1; //Código de Regime Tributário. 1=Simples Nacional; 2=Simples Nacional, excesso sublimite de receita bruta; 3=Regime Normal.

        $nfce->tagemit($emit);

        //------------------ TAG ENDEREÇO DO EMITENTE -----------------------------------
        $enderEmit = new \stdClass(); //tag do endereço do emitente
        $enderEmit->xLgr = 'RUA JOAO MACHADO'; // Logradouro
        $enderEmit->nro = '412'; // Número
        $enderEmit->xCpl = '102'; // Complemento
        $enderEmit->xBairro = 'PRATA'; // Bairro
        $enderEmit->cMun = 2504009; // Código do Município
        $enderEmit->xMun = 'CAMPINA GRANDE'; // Nome do Município
        $enderEmit->UF = 'PB'; // Sigla do Estado
        $enderEmit->CEP = '58400510'; // CEP
        $enderEmit->cPais = 1058; // Código do País
        $enderEmit->xPais = 'Brasil'; // Nome do País
        $enderEmit->fone = '8332472632';

        $nfce ->tagenderEmit($enderEmit);


         //------------------DESTINATÁRIO contadora-----------------------------------
        $dest = new \stdClass();
        $dest->xNome = 'MAELSON M DE LIMA';
        $dest->CPF = '07657174412';
        $dest->indIEDest = 9;
        $dest->email = 'mqmaellson39@gmail.com';
        $nfce->tagdest($dest);

        //---------------------- ENDEREÇO DO DESTINATÁRIO ---------------------
        $enderDest = new \stdClass();
        $enderDest->xLgr = 'RUA FERNANDO BARBOSA DE MELO';
        $enderDest->nro = '510';
        $enderDest->xCpl = 'd1002';
        $enderDest->xBairro = 'CATOLE';
        $enderDest->cMun = 2504009;
        $enderDest->xMun = 'CAMPINA GRANDE';
        $enderDest->UF = 'PB';
        $enderDest->CEP = '58410440';
        $enderDest->cPais = 1058;
        $enderDest->xPais = 'Brasil';
        $enderDest->fone = '83996108796';
        $nfce->tagenderDest($enderDest);



}
catch (Exception $e) {
    // Exibe mensagens de erro em caso de exceção 
        echo "Erro ao consultar o status do serviço:".$e->getMessage();
    
    }
?>