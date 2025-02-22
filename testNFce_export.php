<?php

// Define o diretório temporário para arquivos temporários
putenv('TMPDIR=' . __DIR__ . '/tmp');

// Configurações de exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Carrega as dependências do Composer
require_once 'vendor/autoload.php';

use NFePHP\NFe\Tools;
use NFePHP\NFe\Make;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Complements;

/**
 * Retorna o JSON de configuração.
 */
function getConfigJson()
{
    $config = [
        "atualizacao" => (new \DateTime())->format('Y-m-d\TH:i:sP'),
        "tpAmb"       => 2, // Homologação
        "razaosocial" => "ALUSKA VANESSA BARBOSA DE OLIVEIRA",
        "cnpj"        => "37715148000112",
        "ie"          => "163700044",
        "siglaUF"     => "PB",
        "schemes"     => "PL_009_V4",
        "versao"      => "4.00",
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
    return json_encode($config);
}

/**
 * Retorna o certificado digital lido a partir do arquivo PFX.
 */
function getCertificate()
{
    $pfxcontent = file_get_contents('certificado.pfx');
    $password = '';
    return Certificate::readPfx($pfxcontent, $password);
}

/**
 * Função para gerar o XML da NFC-e.
 */
function generateXML()
{
    $nfce = new Make();

    // TAG infNFe (obrigatória)
    $info = new \stdClass();
    $info->Id = ''; // Se não for informado, será gerado automaticamente
    $info->versao = '4.00';
    $nfce->taginfNFe($info);

    // TAG IDE
    $ide = new \stdClass();
    $ide->cUF = 25;
    $ide->cNF = '01001001';
    $ide->natOp = 'VENDA';
    $ide->mod = 65;
    $ide->serie = 1;
    $ide->nNF = 2023345;
    $ide->dhEmi = (new \DateTime())->format('Y-m-d\TH:i:sP');
    $ide->dhSaiEnt = null;
    $ide->tpNF = 1;
    $ide->idDest = 1;
    $ide->cMunFG = 2507507;
    $ide->tpImp = 4;
    $ide->tpEmis = 1;
    $ide->cDV = 2;
    $ide->tpAmb = 2;
    $ide->finNFe = 1;
    $ide->indFinal = 1;
    $ide->indPres = 4;
    $ide->indIntermed = 0;
    $ide->procEmi = 0;
    $ide->verProc = '1.0.1';
    $ide->dhCont = null;
    $ide->xJust = null;
    $nfce->tagide($ide);

    // TAG EMITENTE
    $emit = new \stdClass();
    $emit->CNPJ = '37715148000112';
    $emit->xNome = 'ALUSKA VANESSA BARBOSA DE OLIVEIRA';
    $emit->xFant = 'ALUPASTS ARTESANAL';
    $emit->IE = '163700044';
    $emit->IEST = null;
    $emit->CNAE = '4729699';
    $emit->CRT = 1;
    $nfce->tagemit($emit);

    // TAG ENDEREÇO DO EMITENTE
    $enderEmit = new \stdClass();
    $enderEmit->xLgr = 'Rua Ozório Paes Carvalho Rocha';
    $enderEmit->nro = '17';
    $enderEmit->xCpl = '';
    $enderEmit->xBairro = 'Tambau';
    $enderEmit->cMun = 2507507;
    $enderEmit->xMun = 'JOAO PESSOA';
    $enderEmit->UF = 'PB';
    $enderEmit->CEP = '58039-090';
    $enderEmit->cPais = 1058;
    $enderEmit->xPais = 'Brasil';
    $enderEmit->fone = '83993327492';
    $nfce->tagenderEmit($enderEmit);

    // TAG DESTINATÁRIO
    $dest = new \stdClass();
    $dest->xNome = 'MAELSON M DE LIMA';
    $dest->CPF = '07657174412';
    $dest->indIEDest = 9;
    $dest->email = 'mqmaellson39@gmail.com';
    $nfce->tagdest($dest);

    // TAG ENDEREÇO DO DESTINATÁRIO
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

    // TAG PRODUTO (obrigatória)
    $produto = new \stdClass();
    $produto->item = 1;
    $produto->cProd = '146';
    $produto->cEAN = "SEM GTIN";
    $produto->xProd = 'CESTA CORPORATIVA PERSONALIZADA';
    $produto->NCM = 21069040;
    $produto->EXTIPI = '';
    $produto->CFOP = 5102;
    $produto->uCom = 'UNID';
    $produto->qCom = 1;
    $produto->vUnCom = 592.00;
    $produto->vProd = 592.00;
    $produto->cEANTrib = "SEM GTIN";
    $produto->uTrib = 'UNID';
    $produto->qTrib = 1;
    $produto->vUnTrib = 592.00;
    $produto->vFrete = 10.00;
    $produto->indTot = 1;
    $nfce->tagprod($produto);

    // Produto adicional (opcional)
    $info_prod = new \stdClass();
    $info_prod->item = 1;
    $info_prod->infAdProd = 'CESTA DE PRESENTS GOLD';
    $nfce->taginfAdProd($info_prod);

    // TAG IMPOSTO
    $imposto = new \stdClass();
    $imposto->item = 1;
    $imposto->vTotTrib = 602.00;
    $nfce->tagimposto($imposto);

    // TAG ICMS-SN (para NFC-e)
    $icmsSN_Prod = new \stdClass();
    $icmsSN_Prod->item = 1;
    $icmsSN_Prod->orig = 0;
    $icmsSN_Prod->CSOSN = '102';
    $icmsSN_Prod->pCredSN = 0.00;
    $icmsSN_Prod->vCredICMSSN = 0.00;
    $nfce->tagICMSSN($icmsSN_Prod);

    // TAG PIS
    $pis_prod = new \stdClass();
    $pis_prod->item = 1;
    $pis_prod->CST = '99';
    $pis_prod->vPIS = 0.00;
    $pis_prod->qBCProd = 0.0;
    $pis_prod->vAliqProd = 0.0;
    $nfce->tagPIS($pis_prod);

    // TAG COFINS
    $cofins_prod = new \stdClass();
    $cofins_prod->item = 1;
    $cofins_prod->CST = '99';
    $cofins_prod->vBC = 0.00;
    $cofins_prod->pCOFINS = null;
    $cofins_prod->vCOFINS = 0.00;
    $cofins_prod->qBCProd = 0;
    $cofins_prod->vAliqProd = 0.00;
    $nfce->tagCOFINS($cofins_prod);

    // TAG TOTAL
    $icmsTotal = new \stdClass();
    $nfce->tagicmstot($icmsTotal);

    // TAG TRANSPORTE
    $transport = new \stdClass();
    $transport->modFrete = 9;
    $nfce->tagtransp($transport);

    // TAG PAGAMENTO
    $troco = new \stdClass();
    $troco->vTroco = 0;
    $nfce->tagpag($troco);

    // TAG DETALHE DO PAGAMENTO
    $detalhe_pagamento = new \stdClass();
    $detalhe_pagamento->indPag = 0;
    $detalhe_pagamento->tPag = '01';
    $detalhe_pagamento->vPag = 602.00;
    $nfce->tagdetpag($detalhe_pagamento);

    // TAG INFORMAÇÕES ADICIONAIS
    $info_add = new \stdClass();
    $info_add->infAdFisco = '';
    $info_add->infCpl = '';
    $nfce->taginfadic($info_add);

    // TAG RESPONSÁVEL TÉCNICO
    $respTecnico = new \stdClass();
    $respTecnico->CNPJ = '52463011000101';
    $respTecnico->xContato = 'MANALYTICS';
    $respTecnico->email = 'maelson@manalyticsai.com';
    $respTecnico->fone = '83996108796';
    $nfce->taginfRespTec($respTecnico);

    // Gera e retorna o XML final
    return $nfce->getXML();
}

/**
 * Função para assinar o XML.
 */
function signXML($xml)
{
    $configJson = getConfigJson();
    $certificate = getCertificate();
    $tools = new Tools($configJson, $certificate);
    $tools->model('65');

    try {
        return $tools->signNFe($xml);
    } catch (\Exception $e) {
        exit("Erro na assinatura: " . $e->getMessage());
    }
}

/**
 * Função para enviar o lote e retornar o Tools e o número do recibo.
 */
function sendLot($xmlAssinado)
{
    $configJson = getConfigJson();
    $certificate = getCertificate();
    $tools = new Tools($configJson, $certificate);
    $tools->model('65');

    // Gera um ID de lote único (garanta que seja único, por exemplo, usando mt_rand e time())
    $idLote = str_pad(mt_rand(2561, 9900) . time(), 15, '0', STR_PAD_LEFT);

    try {
        $resp = $tools->sefazEnviaLote([$xmlAssinado], $idLote, 1);
        $st = new Standardize();
        $std = $st->toStd($resp);

        if ($std->cStat != 103) {
            exit("Erro no envio do lote: [$std->cStat] $std->xMotivo");
        }

        return [$tools, $std->infRec->nRec];
    } catch (\Exception $e) {
        exit("Erro no envio do lote: " . $e->getMessage());
    }
}

/**
 * Função para consultar o recibo e obter o protocolo.
 */
function consultRecibo($tools, $recibo)
{
    $st = new Standardize();
    $tentativas = 5;
    $protocolo = null;

    while ($tentativas > 0) {
        try {
            $protocoloResp = $tools->sefazConsultaRecibo($recibo);
            $protocoloStd = $st->toStd($protocoloResp);

            if (isset($protocoloStd->protNFe->infProt->nProt)) {
                $protocolo = $protocoloStd->protNFe->infProt->nProt;
                break;
            }
        } catch (\Exception $e) {
            echo "Erro ao consultar o recibo: " . $e->getMessage() . "\n";
        }
        sleep(3);
        $tentativas--;
    }

    if (!$protocolo) {
        exit("Não foi possível obter o protocolo da nota após várias tentativas.");
    }

    return $protocolo;
}

// Execução do fluxo
try {
    // 1. Gerar o XML
    $xml = generateXML();
    echo "XML gerado com sucesso.\n";

    // 2. Assinar o XML
    $xmlAssinado = signXML($xml);
    echo "XML assinado com sucesso.\n";

    // 3. Enviar o lote e obter o recibo
    list($tools, $recibo) = sendLot($xmlAssinado);
    echo "Recibo obtido: $recibo\n";

    // 4. Consultar o recibo para obter o protocolo
    $protocolo = consultRecibo($tools, $recibo);
    echo "Protocolo da nota: $protocolo\n";

    // 5. Gerar o XML final autorizado
    $xmlFinal = Complements::toAuthorize($xmlAssinado, $protocolo);
    header('Content-type: text/xml; charset=UTF-8');
    echo $xmlFinal;
} catch (\Exception $e) {
    echo "Erro no processo: " . $e->getMessage();
}
