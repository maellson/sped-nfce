<?php

//Define o diretório temporário para armazenar arquivos temporários
putenv('TMPDIR=' . __DIR__ . '/tmp');
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once 'vendor/autoload.php';

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
$tools->disableCertValidation(true); //tem que desabilitar
$tools->model('65');

try {
    $make = new Make();


    //infNFe OBRIGATÓRIA
    $infNFe_ = new \stdClass();
    $infNFe_->Id = '';
    $infNFe_->versao = '4.00';
    $nfce = $make->taginfNFe($infNFe_);

    //ide OBRIGATÓRIA
    $ide_ = new \stdClass();
    $ide_->cUF = 25; //PB
    $ide_->cNF = '00000025'; //Aqui deve ser gerado um número único de NFce
    $ide_->natOp = 'VENDA CONSUMIDOR';
    $ide_->mod = 65;
    $ide_->serie = 1;
    $ide_->nNF = 2025;
    $ide_->dhEmi = (new \DateTime())->format('Y-m-d\TH:i:sP');
    $ide_->dhSaiEnt = null;
    $ide_->tpNF = 1;
    $ide_->idDest = 1; 
    $ide_->cMunFG = 2504009; //CAMPINA GRANDE
    $ide_->tpImp = 4;
    $ide_->tpEmis = 1;
    $ide_->cDV = 2;
    $ide_->tpAmb = 2; //Homologação
    $ide_->finNFe = 1;
    $ide_->indFinal = 1;
    $ide_->indPres = 4;
    $ide_->indIntermed = 0; 
    $ide_->procEmi = 0;
    $ide_->verProc = '1.0.1';
    $ide_->dhCont = null;
    $ide_->xJust = null;

    $nfce = $make->tagIde($ide_);

    //emit OBRIGATÓRIA
    $emit = new \stdClass();
    $emit->CNPJ = '52463011000101';
    $emit->xNome = 'MANALYTICS INTELIGENCIA ARTIFICIAL LTDA';
    $emit->xFant = 'MANALYTICS';
    $emit->IE = '164780319';
    $emit->IEST = null;
    //$emit->IM = '11889016';
    $emit->CNAE = '4642701';
    $emit->CRT = 1;
    $nfce = $make->tagemit($emit);

    //enderEmit OBRIGATÓRIA
    $enderEmit = new \stdClass();
    $enderEmit->xLgr = 'RUA JOAO MACHADO';
    $enderEmit->nro = '412';
    $enderEmit->xCpl = '';
    $enderEmit->xBairro = 'PRATA';
    $enderEmit->cMun = 2504009;
    $enderEmit->xMun = 'CAMPINA GRANDE';
    $enderEmit->UF = 'PB';
    $enderEmit->CEP = '58400510';
    $enderEmit->cPais = 1058;
    $enderEmit->xPais = 'Brasil';
    $enderEmit->fone = '8332472632';
    $nfce = $make->tagenderEmit($enderEmit);

    //dest OPCIONAL
    $dest = new \stdClass();
    $dest->xNome = 'MAELSON M DE LIMA';
    //$dest->CNPJ = '01234123456789'; //AQUI PODE SER CNPJ OU CPF
    $dest->CPF = '07657174412';
    //$dest->idEstrangeiro = 'AB1234';
    $dest->indIEDest = 9;
    //$dest->IE = '';
    //$dest->ISUF = '12345679';
    //$dest->IM = 'XYZ6543212';
    $dest->email = 'mqmaellson39@gmail.com';
    $nfce = $make->tagdest($dest);

    //enderDest OPCIONAL
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
    $nfce = $make->tagenderDest($enderDest);


    //prod OBRIGATÓRIA
    $std = new \stdClass();
    $std->item = 1;
    $std->cProd = '1111';
    $std->cEAN = "SEM GTIN";
    $std->xProd = 'CAMISETA REGATA GG';
    $std->NCM = 61052000; // num
    //$std->cBenef = 'ab222222';
    $std->EXTIPI = '';
    $std->CFOP = 5101;
    $std->uCom = 'UNID';
    $std->qCom = 1;
    $std->vUnCom = 100.00;
    $std->vProd = 100.00;
    $std->cEANTrib = "SEM GTIN"; //'6361425485451';
    $std->uTrib = 'UNID';
    $std->qTrib = 1;
    $std->vUnTrib = 100.00;
    //$std->vFrete = 0.00;
    //$std->vSeg = 0;
    //$std->vDesc = 0;
    //$std->vOutro = 0;
    $std->indTot = 1;
    //$std->xPed = '12345';
    //$std->nItemPed = 1;
    //$std->nFCI = '12345678-1234-1234-1234-123456789012';
    $prod = $make->tagprod($std);

    $tag = new \stdClass();
    $tag->item = 1;
    $tag->infAdProd = 'DE POLIESTER 100%';
    $make->taginfAdProd($tag);

    //Imposto
    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->vTotTrib = 25.00;
    $make->tagimposto($std);

    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->orig = 0;
    $std->CSOSN = '102';
    $std->pCredSN = 0.00;
    $std->vCredICMSSN = 0.00;
    $std->modBCST = null;
    $std->pMVAST = null;
    $std->pRedBCST = null;
    $std->vBCST = null;
    $std->pICMSST = null;
    $std->vICMSST = null;
    $std->vBCFCPST = null; //incluso no layout 4.00
    $std->pFCPST = null; //incluso no layout 4.00
    $std->vFCPST = null; //incluso no layout 4.00
    $std->vBCSTRet = null;
    $std->pST = null;
    $std->vICMSSTRet = null;
    $std->vBCFCPSTRet = null; //incluso no layout 4.00
    $std->pFCPSTRet = null; //incluso no layout 4.00
    $std->vFCPSTRet = null; //incluso no layout 4.00
    $std->modBC = null;
    $std->vBC = null;
    $std->pRedBC = null;
    $std->pICMS = null;
    $std->vICMS = null;
    $std->pRedBCEfet = null;
    $std->vBCEfet = null;
    $std->pICMSEfet = null;
    $std->vICMSEfet = null;
    $std->vICMSSubstituto = null;
    $make->tagICMSSN($std);

    //PIS
    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->CST = '99';
    //$std->vBC = 1200;
    //$std->pPIS = 0;
    $std->vPIS = 0.00;
    $std->qBCProd = 0;
    $std->vAliqProd = 0;
    $pis = $make->tagPIS($std);

    //COFINS
    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->CST = '99';
    $std->vBC = null;
    $std->pCOFINS = null;
    $std->vCOFINS = 0.00;
    $std->qBCProd = 0;
    $std->vAliqProd = 0;
    $make->tagCOFINS($std);

    //icmstot OBRIGATÓRIA
    $std = new \stdClass();
    //$std->vBC = 100;
    //$std->vICMS = 0;
    //$std->vICMSDeson = 0;
    //$std->vFCPUFDest = 0;
    //$std->vICMSUFDest = 0;
    //$std->vICMSUFRemet = 0;
    //$std->vFCP = 0;
    //$std->vBCST = 0;
    //$std->vST = 0;
    //$std->vFCPST = 0;
    //$std->vFCPSTRet = 0.23;
    //$std->vProd = 2000;
    //$std->vFrete = 100;
    //$std->vSeg = null;
    //$std->vDesc = null;
    //$std->vII = 12;
    //$std->vIPI = 23;
    //$std->vIPIDevol = 9;
    //$std->vPIS = 6;
    //$std->vCOFINS = 25;
    //$std->vOutro = null;
    //$std->vNF = 2345.83;
    //$std->vTotTrib = 798.12;
    $icmstot = $make->tagicmstot($std);

    //transp OBRIGATÓRIA
    $std = new \stdClass();
    $std->modFrete = 0;
    $transp = $make->tagtransp($std);


    //pag OBRIGATÓRIA
    $std = new \stdClass();
    $std->vTroco = 0;
    $pag = $make->tagpag($std);

    //detPag OBRIGATÓRIA
    $std = new \stdClass();
    $std->indPag = 1;
    $std->tPag = '01';
    $std->vPag = 100.00;
    $detpag = $make->tagdetpag($std);

    //infadic
    $std = new \stdClass();
    $std->infAdFisco = '';
    $std->infCpl = '';
    $info = $make->taginfadic($std);

    $std = new stdClass();
    $std->CNPJ = '52463011000101'; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
    $std->xContato = 'MANALYTICS'; //Nome da pessoa a ser contatada
    $std->email = 'maelson@manalyticsai.com'; //E-mail da pessoa jurídica a ser contatada
    $std->fone = '83996108796'; //Telefone da pessoa jurídica/física a ser contatada
    //$std->CSRT = 'G8063VRTNDMO886SFNK5LDUDEI24XJ22YIPO'; //Código de Segurança do Responsável Técnico
    //$std->idCSRT = '01'; //Identificador do CSRT
    $make->taginfRespTec($std);

    $make->monta();
    $xml = $make->getXML();

    //Assina
    $xml = $tools->signNFe($xml);

    //Envia para a Sefaz
    $idLote = str_pad(1, 15, '0', STR_PAD_LEFT); // Identificador do lote
    $response = $tools->sefazEnviaLote([$xml], $idLote, 1); //1 = envio síncrono

    $stdCl = new Standardize($response);
    $respObj = $stdCl->toStd();
    
    if ($respObj->cStat != 104) {
        throw new \Exception(sprintf('Lote não enviado (%s - %s)', $respObj->cStat, $respObj->xMotivo));
    }

    if ($respObj->protNFe->infProt->cStat != 100) {
        throw new \Exception(sprintf('Nfce não autorizada (%s - %s)', $respObj->protNFe->infProt->cStat, $respObj->protNFe->infProt->xMotivo));
    }

    //Salva o protocolo de autorização no xml
    $authorizedXml = Complements::toAuthorize($xml, $response);

    //Gera o arquivo xml e salva
    file_put_contents(__DIR__. '/nfce_protocolado.xml', $authorizedXml);
    
    header('Content-Type: application/xml; charset=utf-8');
    echo $authorizedXml;
} catch (\Exception $e) {
    echo $e->getMessage();
}
