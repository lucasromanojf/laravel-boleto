<?php

namespace Eduardokum\LaravelBoleto\Tests;

use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco as Boleto;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco as Remessa;

class DocumentoCnabTest extends TestCase
{
    // ---- Helper documentoCnab ----

    public function testDocumentoCnabNumericoCurto()
    {
        // CPF 11 digitos em campo de 14 -> zero-pad a esquerda
        $this->assertEquals('00099999999999', Util::documentoCnab('999.999.999-99', 14));
    }

    public function testDocumentoCnabNumerico14()
    {
        // CNPJ 14 digitos em campo de 14 -> sem pad
        $this->assertEquals('99999999999999', Util::documentoCnab('99.999.999/9999-99', 14));
    }

    public function testDocumentoCnabAlfanumerico14()
    {
        // CNPJ alfanumerico 14 chars em campo de 14 -> sem pad
        $this->assertEquals('12ABC34501DE35', Util::documentoCnab('12.ABC.345/01DE-35', 14));
    }

    public function testDocumentoCnabAlfanumericoCampo15()
    {
        // CNPJ alfanumerico 14 chars em campo de 15 -> 1 zero a esquerda
        $this->assertEquals('012ABC34501DE35', Util::documentoCnab('12.ABC.345/01DE-35', 15));
    }

    public function testDocumentoCnabNumericoCampo15()
    {
        // CNPJ numerico 14 digitos em campo de 15 -> 1 zero a esquerda
        $this->assertEquals('099999999999999', Util::documentoCnab('99.999.999/9999-99', 15));
    }

    // ---- Helper isDocumentoJuridico ----

    public function testIsDocumentoJuridicoCpf()
    {
        $this->assertFalse(Util::isDocumentoJuridico('999.999.999-99'));
    }

    public function testIsDocumentoJuridicoCnpjNumerico()
    {
        $this->assertTrue(Util::isDocumentoJuridico('99.999.999/9999-99'));
    }

    public function testIsDocumentoJuridicoCnpjAlfanumerico()
    {
        $this->assertTrue(Util::isDocumentoJuridico('12.ABC.345/01DE-35'));
    }

    // ---- Helper normalizeDocumento ----

    public function testNormalizeDocumentoNumerico()
    {
        $this->assertEquals('99999999999999', Util::normalizeDocumento('99.999.999/9999-99'));
    }

    public function testNormalizeDocumentoAlfanumerico()
    {
        $this->assertEquals('12ABC34501DE35', Util::normalizeDocumento('12.ABC.345/01DE-35'));
    }

    // ---- Regressao byte-identico: Bradesco CNAB400 ----

    public function testRemessaBradescoCnab400ByteIdenticoDocumentoNumerico()
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 15, 10, 0, 0));

        $beneficiario = new Pessoa([
            'nome' => 'ACME',
            'endereco' => 'Rua um, 123',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '99.999.999/9999-99',
        ]);

        $pagador = new Pessoa([
            'nome' => 'Cliente',
            'endereco' => 'Rua um, 123',
            'bairro' => 'Bairro',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        $boleto = new Boleto\Bradesco([
            'dataVencimento' => Carbon::create(2025, 2, 15),
            'valor' => 100.00,
            'multa' => 1,
            'juros' => 1,
            'numero' => 1,
            'diasBaixaAutomatica' => 2,
            'numeroDocumento' => 1,
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => '09',
            'agencia' => 1111,
            'conta' => 9999999,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes' => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite' => 'S',
            'especieDoc' => 'DM',
        ]);

        $remessa = new Remessa\Bradesco([
            'idRemessa' => 1,
            'agencia' => 1111,
            'carteira' => '09',
            'conta' => 99999999,
            'contaDv' => 9,
            'codigoCliente' => 12345678901234567890,
            'beneficiario' => $beneficiario,
        ]);
        $remessa->addBoleto($boleto);

        $fixture = file_get_contents(__DIR__ . '/Remessa/fixtures/bradesco400_numerico.txt');
        $this->assertEquals($fixture, $remessa->gerar(), 'Remessa Bradesco CNAB400 com documento numerico deve ser byte-identica');

        Carbon::setTestNow();
    }

    public function testRemessaBradescoCnab400DocumentoAlfanumerico()
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 15, 10, 0, 0));

        $beneficiario = new Pessoa([
            'nome' => 'ACME',
            'endereco' => 'Rua um, 123',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '99.999.999/9999-99',
        ]);

        $pagador = new Pessoa([
            'nome' => 'Cliente Alfa',
            'endereco' => 'Rua um, 123',
            'bairro' => 'Bairro',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '12ABC34501DE35',
        ]);

        $boleto = new Boleto\Bradesco([
            'dataVencimento' => Carbon::create(2025, 2, 15),
            'valor' => 100.00,
            'multa' => 1,
            'juros' => 1,
            'numero' => 1,
            'diasBaixaAutomatica' => 2,
            'numeroDocumento' => 1,
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => '09',
            'agencia' => 1111,
            'conta' => 9999999,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes' => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite' => 'S',
            'especieDoc' => 'DM',
        ]);

        $remessa = new Remessa\Bradesco([
            'idRemessa' => 1,
            'agencia' => 1111,
            'carteira' => '09',
            'conta' => 99999999,
            'contaDv' => 9,
            'codigoCliente' => 12345678901234567890,
            'beneficiario' => $beneficiario,
        ]);
        $remessa->addBoleto($boleto);

        $cnab = $remessa->gerar();
        $lines = explode($remessa->getFimLinha(), $cnab);
        $detalhe = $lines[1]; // segunda linha = detalhe

        // Tipo inscricao deve ser '02' (CNPJ)
        $this->assertEquals('02', substr($detalhe, 218, 2), 'Tipo inscricao pagador alfanumerico deve ser 02');

        // Documento pagador nas posicoes 221-234 (0-indexed: 220-233)
        $this->assertEquals('12ABC34501DE35', substr($detalhe, 220, 14), 'Documento alfanumerico deve preservar letras');

        Carbon::setTestNow();
    }

    // ---- Regressao byte-identico: Santander CNAB400 ----

    public function testRemessaSantanderCnab400ByteIdenticoDocumentoNumerico()
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 15, 10, 0, 0));

        $beneficiario = new Pessoa([
            'nome' => 'ACME',
            'endereco' => 'Rua um, 123',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '99.999.999/9999-99',
        ]);

        $pagador = new Pessoa([
            'nome' => 'Cliente',
            'endereco' => 'Rua um, 123',
            'bairro' => 'Bairro',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        $boleto = new Boleto\Santander([
            'dataVencimento' => Carbon::create(2025, 2, 15),
            'valor' => 100.00,
            'multa' => 1,
            'juros' => 1,
            'numero' => 1,
            'numeroDocumento' => 1,
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => 101,
            'agencia' => 1111,
            'codigoCliente' => 1234567,
            'conta' => 99999999,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes' => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite' => 'S',
            'especieDoc' => 'DM',
        ]);

        $remessa = new Remessa\Santander([
            'idRemessa' => 1,
            'agencia' => 1111,
            'carteira' => 101,
            'conta' => 99999999,
            'codigoCliente' => 1234567,
            'beneficiario' => $beneficiario,
        ]);
        $remessa->addBoleto($boleto);

        $fixture = file_get_contents(__DIR__ . '/Remessa/fixtures/santander400_numerico.txt');
        $this->assertEquals($fixture, $remessa->gerar(), 'Remessa Santander CNAB400 com documento numerico deve ser byte-identica');

        Carbon::setTestNow();
    }

    public function testRemessaSantanderCnab400DocumentoAlfanumerico()
    {
        Carbon::setTestNow(Carbon::create(2025, 1, 15, 10, 0, 0));

        $beneficiario = new Pessoa([
            'nome' => 'ACME',
            'endereco' => 'Rua um, 123',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '12ABC34501DE35', // beneficiario alfanumerico
        ]);

        $pagador = new Pessoa([
            'nome' => 'Cliente Alfa',
            'endereco' => 'Rua um, 123',
            'bairro' => 'Bairro',
            'cep' => '99999-999',
            'uf' => 'UF',
            'cidade' => 'CIDADE',
            'documento' => '12ABC34501DE35',
        ]);

        $boleto = new Boleto\Santander([
            'dataVencimento' => Carbon::create(2025, 2, 15),
            'valor' => 100.00,
            'multa' => 1,
            'juros' => 1,
            'numero' => 1,
            'numeroDocumento' => 1,
            'pagador' => $pagador,
            'beneficiario' => $beneficiario,
            'carteira' => 101,
            'agencia' => 1111,
            'codigoCliente' => 1234567,
            'conta' => 99999999,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes' => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite' => 'S',
            'especieDoc' => 'DM',
        ]);

        $remessa = new Remessa\Santander([
            'idRemessa' => 1,
            'agencia' => 1111,
            'carteira' => 101,
            'conta' => 99999999,
            'codigoCliente' => 1234567,
            'beneficiario' => $beneficiario,
        ]);
        $remessa->addBoleto($boleto);

        $cnab = $remessa->gerar();
        $lines = explode($remessa->getFimLinha(), $cnab);

        // Detalhe: beneficiario doc at positions 2-3 and 4-17 (0-indexed: 1-2 and 3-16)
        $detalhe = $lines[1];
        $this->assertEquals('02', substr($detalhe, 1, 2), 'Tipo inscricao beneficiario alfanumerico deve ser 02');
        $this->assertEquals('12ABC34501DE35', substr($detalhe, 3, 14), 'Documento beneficiario alfanumerico deve preservar letras');

        // Pagador doc at 219-220 and 221-234
        $this->assertEquals('02', substr($detalhe, 218, 2), 'Tipo inscricao pagador alfanumerico deve ser 02');
        $this->assertEquals('12ABC34501DE35', substr($detalhe, 220, 14), 'Documento pagador alfanumerico deve preservar letras');

        Carbon::setTestNow();
    }
}
