<?php

// Define o diretório temporário para armazenar arquivos temporários
putenv('TMPDIR=' . __DIR__ . '/tmp');
// Define o nivel de relatório de erros para exibir todos os erros e avisos
error_reporting (E_ALL);
// Exibe erros diretamente na tela durante o desenvolvimento
ini_set ('display_errors','on');
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



try {
        $nfce = new Make();

        //===================infNFe OBRIGATÓRIA =======================================

        $info = new \stdClass();
        $info->Id = ''; //se o Id de 44 digitos não for passado será gerado automaticamente
        $info->versao = '4.00'; //versão do layout (string)
        //$info->pk_nItem = null; //deixe essa variavel sempre como NULL

        $nfce->taginfNFe($info);

        //------------------ TAG IDE -----------------------------------

        $ide = new \stdClass();
        $ide->cUF = 25; // codigo da UF para o estado da Paraíba
        $ide->cNF = '01001001'; // Código numérico que compõe a Chave de Acesso
        $ide->natOp = 'VENDA '; // Descrição da Natureza da Operação
        $ide->mod = 65; //aqui // Modelo da Nota Fiscal Eletrônica 65 para nfce  e 55 para nfe
        $ide->serie = 1; // Série da Nota Fiscal
        $ide->nNF = 2023345; // Número da Nota Fiscal, NAO PODE REPETIR
        $ide->dhEmi = (new \DateTime())->format('Y-m-d\TH:i:sP');  //'2015-02-19T13:48:00-02:00'; 
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
        $emit->CNPJ = '37715148000112'; //CNPJ do emitente
        $emit->xNome = 'ALUSKA VANESSA BARBOSA DE OLIVEIRA'; //Razão Social ou Nome do emitente
        $emit->xFant = 'ALUPASTS ARTESANAL'; //Nome fantasia
        $emit->IE = '163700044'; //Inscrição Estadual
        $emit->IEST = null; //Inscrição Estadual do Substituto Tributário
        //$emit->IM = '11889016';
        $emit->CNAE = '4729699';    //CNAE -- contadora
        $emit->CRT = 1; //Código de Regime Tributário. 1=Simples Nacional; 2=Simples Nacional, excesso sublimite de receita bruta; 3=Regime Normal.
        $nfce->tagemit($emit);

        //------------------ TAG ENDEREÇO DO EMITENTE -----------------------------------
        $enderEmit = new \stdClass();
        $enderEmit->xLgr = 'Rua Ozório Paes Carvalho Rocha';
        $enderEmit->nro = '17';
        $enderEmit->xCpl = '';
        $enderEmit->xBairro = 'Tambau';
        $enderEmit->cMun = 2507507;
        $enderEmit->xMun = ' JOAO PESSOA';
        $enderEmit->UF = 'PB';
        $enderEmit->CEP = '58039-090';
        $enderEmit->cPais = 1058;
        $enderEmit->xPais = 'Brasil';
        $enderEmit->fone = '83993327492';
        $nfce ->tagenderEmit($enderEmit);


         //------------------  DESTINATÁRIO -----------------------------------
        $dest = new \stdClass();
        $dest->xNome = 'MAELSON M DE LIMA';//Razão Social ou Nome do destinatário
        $dest->CPF = '07657174412';//CNPJ do destinatário
        $dest->indIEDest = 9;//Indicador da IE do Destinatário 9-Não contribuinte, que pode ou não possuir Inscrição Estadual no Cadastro de Contribuintes do ICMS.
        $dest->email = 'mqmaellson39@gmail.com'; //email do destinatário
        $nfce->tagdest($dest);//Adiciona as tags com as informações do destinatário

        //---------------------- ENDEREÇO DO DESTINATÁRIO ---------------------
        $enderDest = new stdClass();
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

        //------------------ TAG PRODUTOS -----------------------------------

        //prod OBRIGATÓRIA
        $produto = new \stdClass();
        $produto->item = 1; //item da NFe
        $produto->cProd = '146'; //codigo do produto
        $produto->cEAN = "SEM GTIN"; // GETIN do produto é o código de barras
        $produto->xProd = 'CESTA CORPORATIVA PERSONALIZADA '; //descrição do produto
        $produto->NCM = 21069040; // num do produto
        $produto->EXTIPI = '';
        $produto->CFOP = 5102; // cfop é o CÓDIGO FISCAL DE OPERAÇÕES E DE PRESTAÇÕES
        $produto->uCom = 'UNID';
        $produto->qCom = 1;
        $produto->vUnCom = 592.00;
        $produto->vProd = 592.00;
        $produto->cEANTrib = "SEM GTIN"; //'6361425485451';
        $produto->uTrib = 'UNID';
        $produto->qTrib = 1;
        $produto->vUnTrib = 592.00;
        $produto->vFrete = 10.00;
        //$produto->vSeg = 0;
        //$produto->vDesc = 0.00;
        $produto->indTot = 1;
        $nfce->tagprod($produto);

        // descritivo do produto adicional
        $info_prod = new \stdClass();
        $info_prod->item = 1;
        $info_prod->infAdProd = 'CESTA DE PRESENTS GOLD';
        $nfce->taginfAdProd($info_prod);


        //Imposto
        $imposto = new \stdClass();
        $imposto->item = 1; //item da NFe
        $imposto->vTotTrib = 602.00;
        $nfce->tagimposto($imposto);

    //------------------ TAG ICMS-SN nfce -----------------------------------
        $icmsSN_Prod = new \stdClass();
        $icmsSN_Prod->item = 1; //item da NFe
        $icmsSN_Prod->orig = 0; //0-Nacional;1-Estrangeira;2-Nacional com mais de 40% de conteúdo estrangeiro;3-Estrangeira com produção no país
        $icmsSN_Prod->CSOSN = '102'; //102=ICMS cobrado anteriormente por substituição tributária
        $icmsSN_Prod->pCredSN = 0.00; //alíquota aplicável de cálculo do crédito (Simples Nacional)
        $icmsSN_Prod->vCredICMSSN = 0.00; //valor de crédito do ICMS que pode ser aproveitado nos termos do art. 23 da LC 123 (Simples Nacional)
        
        $nfce->tagICMSSN($icmsSN_Prod); //adiciona as tags com as informações do ICMS


    //=======================   PIS   ========================================================
        $pis_prod = new \stdClass();
        $pis_prod->item = 1; //item da NFe
        $pis_prod->CST = '99'; //01=Operação
        //$pis_prod->vBC = 0.00;
        //$pis_prod->pPIS = 0.00;
        $pis_prod->vPIS = 0.00;
        $pis_prod->qBCProd = 0.0; // 
        $pis_prod->vAliqProd = 0.0;
        $nfce->tagPIS($pis_prod);

    //===============  COFINS   ======================================================
        $cofins_prod = new \stdClass();
        $cofins_prod->item = 1; //item da NFe
        $cofins_prod->CST = '99';
        $cofins_prod->vBC = 0.00;
        $cofins_prod->pCOFINS = null;
        $cofins_prod->vCOFINS = 0.00;
        $cofins_prod->qBCProd = 0;
        $cofins_prod->vAliqProd = 0.00;
        $nfce->tagCOFINS($cofins_prod);


        //------------------ TAG TOTAL -----------------------------------

        $icmsTotal = new stdClass();
        /*$icmsTotal->vBC = null;
        $icmsTotal->vICMS = null;
        $icmsTotal->vICMSDeson = null;
        $icmsTotal->vBCST = null;
        $icmsTotal->vST = null;
        $icmsTotal->vProd = null;
        $icmsTotal->vFrete = null;
        $icmsTotal->vSeg = null;
        $icmsTotal->vDesc = null;
        $icmsTotal->vII = null;
        $icmsTotal->vIPI = null;
        $icmsTotal->vPIS = null;
        $icmsTotal->vCOFINS = null;
        $icmsTotal->vOutro = null;
        $icmsTotal->vNF = null;
        $icmsTotal->vIPIDevol = null;
        $icmsTotal->vTotTrib = null;
        $icmsTotal->vFCP = null;
        $icmsTotal->vFCPST = null;
        $icmsTotal->vFCPSTRet = null;
        $icmsTotal->vFCPUFDest = null;
        $icmsTotal->vICMSUFDest = null;
        $icmsTotal->vICMSUFRemet = null;
        $icmsTotal->qBCMono = null;
        $icmsTotal->vICMSMono = null;
        $icmsTotal->qBCMonoReten = null;
        $icmsTotal->vICMSMonoReten = null;
        $icmsTotal->qBCMonoRet = null;
        $icmsTotal->vICMSMonoRet = null;*/
        $nfce->tagicmstot($icmsTotal);

         // ============== transp OBRIGATÓRIA para nfce ======================
        $transport = new \stdClass();
        $transport->modFrete = 9; // 0-por conta do emitente; 1-por conta do destinatário/remetente; 2-por conta de terceiros; 9-sem frete
        $nfce->tagtransp($transport);

        // caso hja transport envie assim:
        ### function tagtransporta($std):DOMElement
    /*  
        $std = new stdClass();
        $std->xNome = 'Rodo Fulano';
        $std->IE = '12345678901';
        $std->xEnder = 'Rua Um, sem numero';
        $std->xMun = 'Cotia';
        $std->UF = 'SP';
        $std->CNPJ = '12345678901234';//só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
        $std->CPF = null;
   

        $nfe->tagtransporta($std);
    */

        //===================== pag OBRIGATÓRIA para nfce ======================
        $troco = new \stdClass();
        $troco->vTroco = 0; //troco
        $nfce->tagpag($troco);

        //=====================detPag OBRIGATÓRIA para nfce ======================
        $detalhe_pagamento = new \stdClass();
        $detalhe_pagamento->indPag = 0; //Indicador da Forma de Pagamento 0=Pagamento à vista 1=Pagamento à prazo 2=Outros
        $detalhe_pagamento->tPag = '01'; //01=Dinheiro 02=Cheque 03=Cartão de Crédito 04=Cartão de Débito 05=Crédito Loja 10=Vale Alimentação 11=Vale Refeição 12=Vale Presente 13=Vale Combustível 99=Outros
        $detalhe_pagamento->vPag = 602.00;
        $nfce->tagdetpag($detalhe_pagamento);

        //==================== inf__adic ======================
        $info_add = new \stdClass();
        $info_add->infAdFisco = '';
        $info_add->infCpl = '';
        $nfce->taginfadic($info_add);

        
        // ====================== responsavelTec ======================
        $respTecnico = new stdClass();
        $respTecnico->CNPJ = '52463011000101'; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
        $respTecnico->xContato = 'MANALYTICS'; //Nome da pessoa a ser contatada
        $respTecnico->email = 'maelson@manalyticsai.com'; //E-mail da pessoa jurídica a ser contatada
        $respTecnico->fone = '83996108796'; //Telefone da pessoa jurídica/física a ser contatada
        //$respTecnico->CSRT = 'G8063VRTNDMO886SFNK5LDUDEI24XJ22YIPO'; //Código de Segurança do Responsável Técnico
        //$respTecnico->idCSRT = '01'; //Identificador do CSRT
        $nfce->taginfRespTec($respTecnico);

        // =============================== run ================================
        $xml = $nfce->getXML();
        /*
        //ob_start();
        $xml = $nfce->getXML();
        header("Content-type: text/xml; charset=utf-8");
        echo $xml;
        ob_end_flush();*/
        

// ==================== MONTANDO O CONFIG =================
        $config = [
            "atualizacao" => (new \DateTime())->format('Y-m-d\TH:i:sP'),
            "tpAmb"       => 2, //Homologação
            "razaosocial" => "ALUSKA VANESSA BARBOSA DE OLIVEIRA",
            "cnpj"        => "37715148000112",
            "ie"          => '163700044',
            "siglaUF"     => "PB",
            "schemes"     => "PL_009_V4",
            "versao"      => '4.00',
            //"tokenIBPT"   => "AAAAAAA",
            "CSC"         => "3C3BC577-3CE0-94FC-EDBE-86541A0F3354",//"4AC2A781AC41BF545E1A7BBFA3BF7E37"
            "CSCid"       => "000001",//000002
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
        $tools->model('65'); 

// ==========================================  ASSINANDO O XML ===================================================================
        try {
            $xmlAssinado = $tools->signNFe($xml); // O conteúdo do XML assinado fica armazenado na variável $xmlAssinado
        } catch (\Exception $e) {
            //aqui você trata possíveis exceptions da assinatura
            exit($e->getMessage());
        }


       //header("Content-type: text/xml; charset=utf-8");
       //echo $xmlAssinado;

// ========================================== ENVIANDO O LOTE =====================================================================
## Enviar Lote
/*
Para o envio do lote vamos precisar da *$configJson*, *$certificadoDigital* e do nosso XML assinado 
que está na variável *$xmlAssinado*. Esse método recebe um array com os XMLs nos permitindo enviar mais 
de um XML por vez, mas nesse caso vamos enviar somente um. 
 */       
   
                    //Envia para a Sefaz
                    $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                    $response = $tools->sefazEnviaLote([$xmlAssinado], $idLote, 1); //1 = envio síncrono

                    $st = new NFePHP\NFe\Common\Standardize();
                    $std = $st->toStd($response);

                    if ($std->cStat != 104) {
                        throw new \Exception(sprintf('Lote não enviado (%s - %s)', $std->cStat, $std->xMotivo));
                    }

                    if ($std->protNFe->infProt->cStat != 103) {
                        throw new \Exception(sprintf('Nfce não autorizada (%s - %s)', $std->protNFe->infProt->cStat, $std->protNFe->infProt->xMotivo));
                    }
                   $recibo = $std->infRec->nRec; // Vamos usar a variável $recibo 
                    echo "Recibo: $recibo\n";

                    //Salva o protocolo de autorização no xml
                    //$authorizedXml = Complements::toAuthorize($xmlAssinado, $response);

                    //Gera o arquivo xml e salva
                   // file_put_contents(__DIR__. '/nfce_protocolado.xml', $authorizedXml);

                   // header('Content-Type: application/xml; charset=utf-8');
                   // echo $authorizedXml;


    }
catch (\Exception $e) {
    // Exibe mensagens de erro em caso de exceção 
        echo "Erro ao consultar o status do serviço:".$e->getMessage();
    
    }


?>