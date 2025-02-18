# sped-nfce
sistema php para emissao de nota fiscal nfce

### Definindo temp
code
'''

mkdir -p /Applications/XAMPP/xamppfiles/htdocs/sped-nfce/tmp
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/sped-nfce/tmp

'''

### validando internamente no arquivo status.php:
code
'''
    putenv('TMPDIR=' . __DIR__ . '/tmp');

'''


### Evolução de sistema

* CRUD de dados da empresa
  -- fazer input do certificado (chave pfx)
  -- dados da empresa
     --- Razão social
     --- CNPJ
     --- CSC
     --- CSCid
     --- Estado
     --- Cidade

 * Consultas externas:
   --   codigos do Estado e cidades do IBGE:
   --   código da Cidade

